<?php
/**
 * consent_form_list.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget consent_form list
 * 
 * @package mastodon\ui
 */
class consent_form_list extends \cenozo\ui\widget\base_list
{
  /**
   * Constructor
   * 
   * Defines all variables required by the consent_form list.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args An associative array of arguments to be processed by the widget
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'consent_form', $args );
    
    $this->add_column( 'id', 'number', 'ID', true );
    $this->add_column( 'date', 'date', 'Date Added', true );
    $name = 'typist' == lib::create( 'business\session' )->get_role()->name
          ? 'deferred' : 'conflict';
    $this->add_column( $name, 'boolean', ucwords( $name ), false );
  }
  
  /**
   * Set the rows array needed by the template.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function finish()
  {
    parent::finish();
    
    $session = lib::create( 'business\session' );
    $db_user = $session->get_user();
    $db_role = $session->get_role();
    $name = 'typist' == $db_role->name ? 'deferred' : 'conflict';

    foreach( $this->get_record_list() as $record )
    {
      // get the value of the deferred or conflict item
      if( 'typist' == $db_role->name )
      { // deferred
        $modifier = lib::create( 'database\modifier' );
        $modifier->where( 'user_id', '=', $db_user->id );
        $value = 0 < count( $record->get_consent_form_entry_list( $modifier ) );
      }
      else
      { // conflict
        $modifier = lib::create( 'database\modifier' );
        $modifier->where( 'deferred', '!=', true );
        $value = 1 < count( $record->get_consent_form_entry_list( $modifier ) );
      }
      $this->add_row( $record->id,
        array( 'id' => $record->id,
               'date' => util::get_formatted_date( $record->date ),
               $name => $value ) );
    }

    $this->finish_setting_rows();
  }

  /**
   * Overrides the parent class method to restrict consent_form list based on user's role
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param database\modifier $modifier Modifications to the list.
   * @return int
   * @access protected
   */
  protected function determine_record_count( $modifier = NULL )
  {
    $session = lib::create( 'business\session' );
    $db_user = $session->get_user();
    $db_role = $session->get_role();

    if( is_null( $modifier ) ) $modifier = lib::create( 'database\modifier' );
    $modifier->where( 'invalid', '!=', true );
    $modifier->where( 'consent_id', '=', NULL );

    if( 'typist' == $db_role->name )
      $modifier->where( 'consent_form_entry.user_id', '=', $db_user->id );

    return parent::determine_record_count( $modifier );
  }
  
  /**
   * Overrides the parent class method to restrict consent_form list based on user's role
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param database\modifier $modifier Modifications to the list.
   * @return array( record )
   * @access protected
   */
  protected function determine_record_list( $modifier = NULL )
  {
    $session = lib::create( 'business\session' );
    $db_user = $session->get_user();
    $db_role = $session->get_role();

    if( is_null( $modifier ) ) $modifier = lib::create( 'database\modifier' );
    $modifier->where( 'invalid', '!=', true );
    $modifier->where( 'consent_id', '=', NULL );

    if( 'typist' == $db_role->name )
      $modifier->where( 'consent_form_entry.user_id', '=', $db_user->id );

    return parent::determine_record_list( $modifier );
  }
}
?>
