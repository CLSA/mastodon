<?php
/**
 * base_form_entry_list.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Base class for all form entry lists.
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
  }

  /**
   * Processes arguments, preparing them for the operation.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @throws exception\notice
   * @access protected
   */
  protected function prepare()
  {
    parent::prepare();

    $this->add_column( $this->form_type.'_form_id', 'number', 'ID', true );
    $this->add_column( 'date', $this->form_type.'_form.date', 'Date Added', false );
  
    // the "add" function is overridden, so just make sure it gets included in the template
    $this->set_addable( true );
  }

  /**
   * Sets up the operation with any pre-execution instructions that may be necessary.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function setup()
  {
    parent::setup();
    
    $form_id_name = $this->form_type.'_form_id';
    $get_form_method = sprintf( 'get_%s_form', $this->form_type );

    foreach( $this->get_record_list() as $record )
    {
      $this->add_row( $record->id,
        array( $this->form_type.'_form_id' => $record->$form_id_name,
               'date' => $record->$get_form_method()->date ) );
    }
  }

  /**
   * Overrides the parent class method to restrict form entry list based on user's role
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param database\modifier $modifier Modifications to the list.
   * @return int
   * @access protected
   */
  public function determine_record_count( $modifier = NULL )
  {
    $form_name = $this->form_type.'_form';
    $form_entry_list_class_name = lib::get_class_name( 'database\\'.$form_name );
    $db_user = lib::create( 'business\session' )->get_user();

    if( is_null( $modifier ) ) $modifier = lib::create( 'database\modifier' );
    $modifier->where( $form_name.'.invalid', '=', false );
    $modifier->where( $form_name.'.complete', '=', false );
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
  public function determine_record_list( $modifier = NULL )
  {
    $form_name = $this->form_type.'_form';
    $form_entry_list_class_name = lib::get_class_name( 'database\\'.$form_name );
    $db_user = lib::create( 'business\session' )->get_user();

    if( is_null( $modifier ) ) $modifier = lib::create( 'database\modifier' );
    $modifier->where( $form_name.'.invalid', '=', false );
    $modifier->where( $form_name.'.complete', '=', false );
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
