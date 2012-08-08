<?php
/**
 * base_form_adjudicate.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: base_form adjudicate
 *
 * Base class to adjudicate conflicts in two form entries.
 */
abstract class base_form_adjudicate extends \cenozo\ui\push\base_record
{
  /**
   * Constructor.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param string $form_type The type of form being adjudicated.
   * @param array $args Push arguments
   * @access public
   */
  public function __construct( $form_type, $args )
  {
    parent::__construct( $form_type.'_form', 'adjudicate', $args );
    $this->form_type = $form_type;
  }

  /**
   * This method executes the operation's purpose.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function execute()
  {
    parent::execute();

    $columns = $this->get_argument( 'columns' );

    // there has to be 2 non-deferred entries for this form
    $entry_list_method_name = sprintf( 'get_%s_entry_list', $this->get_subject() );
    $form_entry_mod = lib::create( 'database\modifier' );
    $form_entry_mod->where( 'deferred', '=', false );
    $form_entry_list =
      $this->get_record()->$entry_list_method_name( $form_entry_mod );
    if( 2 > count( $form_entry_list ) )
      throw lib::create( 'exception\runtime',
        sprintf( 'Tried to adjudicate %s ID %d that does not have 2 ready entries.',
                 str_replace( '_', ' ', $this->get_subject() ),
                 $this->get_record()->id ),
        __METHOD__ );

    // create an array of both entries indexed by username
    $db_temp_form_entry = current( $form_entry_list );
    $db_form_entries[$db_temp_form_entry->get_user()->name] = $db_temp_form_entry;
    $db_temp_form_entry = next( $form_entry_list );
    $db_form_entries[$db_temp_form_entry->get_user()->name] = $db_temp_form_entry;

    // first see if all adjudications are for one user only
    $exclusive_username = current( $columns );
    foreach( $columns as $username )
    {
      if( $exclusive_username != $username )
      {
        $exclusive_username = NULL;
        break;
      }
    }

    if( !is_null( $exclusive_username ) )
    {
      // import the exclusive user's entry
      $this->get_record()->import( $db_form_entries[$exclusive_username] );
    }
    else // we have a mix of adjudications, so create a new entry with all checked values
    {
      $form_entry_name = sprintf( 'database\%s_entry', $this->get_subject() );
      $form_entry_class_name = lib::get_class_name( $form_entry_name );
      $form_id_column_name = sprintf( '%s_id', $this->get_subject() );
      $user_id = lib::create( 'business\session' )->get_user()->id;

      // if this user already has an entry edit that one, otherwise make a new one
      $db_form_entry = $form_entry_class_name::get_unique_record(
        array( $form_id_column_name, 'user_id' ),
        array( $this->get_record()->id, $user_id ) );

      if( is_null( $db_form_entry ) )
      {
        $db_form_entry = lib::create( $form_entry_name );
        $db_form_entry->$form_id_column_name = $this->get_record()->id;
        $db_form_entry->user_id = $user_id;
      }

      foreach( $db_form_entry->get_column_names() as $column )
      {
        // don't set the id, form_id or user_id columns
        if( 'id' != $column && $form_id_column_name != $column && 'user_id' != $column )
        {
          $selected_username = array_key_exists( $column, $columns )
                             ? $columns[$column] // selected username
                             : key( $db_form_entries ); // identical: either user will do
          $db_form_entry->$column = $db_form_entries[$selected_username]->$column;
        }
      }

      // save the new entry and import it
      $db_form_entry->save();
      $this->get_record()->import( $db_form_entry );
    }
  }

  /**
   * The type of form (ie: consent, contact, proxy)
   * @var string $form_type
   * @access private
   */
  private $form_type;
}
?>
