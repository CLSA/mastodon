<?php
/**
 * base_form_entry_list.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Base class for all form entry lists.
 * 
 * @package mastodon\ui
 */
class base_form_entry_list extends \cenozo\ui\widget\base_list
{
  /**
   * Constructor
   * 
   * Defines all variables required by the form entry list.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param string $form_type The type of form being listed.
   * @param array $args An associative array of arguments to be processed by the widget
   * @access public
   */
  public function __construct( $form_type, $args )
  {
    parent::__construct( $form_type.'_form_entry', $args );
    $this->form_type = $form_type;
    
    $this->add_column( $this->form_type.'_form_id', 'number', 'ID', true );
    $this->add_column( 'date', $this->form_type.'_form.date', 'Date Added', false );
  }
  
  /**
   * Set the rows array needed by the template.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function finish()
  {
    $form_id_name = $this->form_type.'_form_id';
    $get_form_method = sprintf( 'get_%s_form', $this->form_type );

    // the "add" function is overridden, so just make sure it gets included in the template
    $this->set_addable( true );

    parent::finish();
    
    foreach( $this->get_record_list() as $record )
    {
      $this->add_row( $record->id,
        array( $this->form_type.'_form_id' => $record->$form_id_name,
               'date' => $record->$get_form_method()->date ) );
    }

    $this->finish_setting_rows();
  }

  /**
   * Overrides the parent class method to restrict form entry list based on user's role
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param database\modifier $modifier Modifications to the list.
   * @return int
   * @access protected
   */
  protected function determine_record_count( $modifier = NULL )
  {
    $form_name = $this->form_type.'_form';
    $form_entry_list_class_name = lib::get_class_name( 'database\\'.$form_name );
    $link_id_name = $form_entry_list_class_name::get_link_name();
    $db_user = lib::create( 'business\session' )->get_user();

    if( is_null( $modifier ) ) $modifier = lib::create( 'database\modifier' );
    $modifier->where( $form_name.'.invalid', '!=', true );
    $modifier->where( $form_name.'.'.$link_id_name, '=', NULL );
    $modifier->where( 'user_id', '=', $db_user->id );
    $modifier->where( 'deferred', '=', true );

    return parent::determine_record_count( $modifier );
  }
  
  /**
   * Overrides the parent class method to restrict form entry list based on user's role
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param database\modifier $modifier Modifications to the list.
   * @return array( record )
   * @access protected
   */
  protected function determine_record_list( $modifier = NULL )
  {
    $form_name = $this->form_type.'_form';
    $form_entry_list_class_name = lib::get_class_name( 'database\\'.$form_name );
    $link_id_name = $form_entry_list_class_name::get_link_name();
    $db_user = lib::create( 'business\session' )->get_user();

    if( is_null( $modifier ) ) $modifier = lib::create( 'database\modifier' );
    $modifier->where( $form_name.'.invalid', '!=', true );
    $modifier->where( $form_name.'.'.$link_id_name, '=', NULL );
    $modifier->where( 'user_id', '=', $db_user->id );
    $modifier->where( 'deferred', '=', true );

    return parent::determine_record_list( $modifier );
  }

  /**
   * The type of form (ie: consent, contact, proxy)
   * @var string $form_type
   * @access private
   */
  private $form_type;
}
?>
