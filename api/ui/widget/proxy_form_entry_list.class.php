<?php
/**
 * proxy_form_entry_list.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget proxy_form_entry list
 * 
 * @package mastodon\ui
 */
class proxy_form_entry_list extends \cenozo\ui\widget\base_list
{
  /**
   * Constructor
   * 
   * Defines all variables required by the proxy_form_entry list.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args An associative array of arguments to be processed by the widget
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'proxy_form_entry', $args );
    
    $this->add_column( 'proxy_form_id', 'number', 'ID', true );
    $this->add_column( 'date', 'proxy_form.date', 'Date Added', false );
  }
  
  /**
   * Set the rows array needed by the template.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function finish()
  {
    // the "add" function is overridden, so just make sure it gets included in the template
    $this->set_addable( true );

    parent::finish();
    
    foreach( $this->get_record_list() as $record )
    {
      $this->add_row( $record->id,
        array( 'proxy_form_id' => $record->proxy_form_id,
               'date' => $record->get_proxy_form()->date ) );
    }

    $this->finish_setting_rows();
  }

  /**
   * Overrides the parent class method to restrict proxy_form_entry list based on user's role
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param database\modifier $modifier Modifications to the list.
   * @return int
   * @access protected
   */
  protected function determine_record_count( $modifier = NULL )
  {
    $db_user = lib::create( 'business\session' )->get_user();

    if( is_null( $modifier ) ) $modifier = lib::create( 'database\modifier' );
    $modifier->where( 'proxy_form.invalid', '!=', true );
    $modifier->where( 'proxy_form.proxy_id', '=', NULL );
    $modifier->where( 'user_id', '=', $db_user->id );
    $modifier->where( 'deferred', '=', true );

    return parent::determine_record_count( $modifier );
  }
  
  /**
   * Overrides the parent class method to restrict proxy_form_entry list based on user's role
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param database\modifier $modifier Modifications to the list.
   * @return array( record )
   * @access protected
   */
  protected function determine_record_list( $modifier = NULL )
  {
    $db_user = lib::create( 'business\session' )->get_user();

    if( is_null( $modifier ) ) $modifier = lib::create( 'database\modifier' );
    $modifier->where( 'proxy_form.invalid', '!=', true );
    $modifier->where( 'proxy_form.proxy_id', '=', NULL );
    $modifier->where( 'user_id', '=', $db_user->id );
    $modifier->where( 'deferred', '=', true );

    return parent::determine_record_list( $modifier );
  }
}
?>
