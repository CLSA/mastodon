<?php
/**
 * alternate_view.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget alternate view
 * 
 * @package mastodon\ui
 */
class alternate_view extends base_view
{
  /**
   * Constructor
   * 
   * Defines all variables which need to be set for the associated template.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args An associative array of arguments to be processed by the widget
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'alternate', 'view', $args );
    
    // create an associative array with everything we want to display about the alternate
    $this->add_item( 'first_name', 'string', 'First Name' );
    $this->add_item( 'last_name', 'string', 'Last Name' );
    $this->add_item( 'association', 'string', 'Association' );
    $this->add_item( 'alternate', 'boolean', 'Alternate' );
    $this->add_item( 'informant', 'boolean', 'Informant' );
    $this->add_item( 'proxy', 'boolean', 'Proxy' );
    
    try
    {
      // create the address sub-list widget
      $this->address_list = lib::create( 'ui\widget\address_list', $args );
      $this->address_list->set_parent( $this );
      $this->address_list->set_heading( 'Addresses' );
    }
    catch( exc\permission $e )
    {
      $this->address_list = NULL;
    }

    try
    {
      // create the phone sub-list widget
      $this->phone_list = lib::create( 'ui\widget\phone_list', $args );
      $this->phone_list->set_parent( $this );
      $this->phone_list->set_heading( 'Phone numbers' );
    }
    catch( exc\permission $e )
    {
      $this->phone_list = NULL;
    }
  }

  /**
   * Finish setting the variables in a widget.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function finish()
  {
    parent::finish();

    // set the view's items
    $this->set_item( 'first_name', $this->get_record()->first_name );
    $this->set_item( 'last_name', $this->get_record()->last_name );
    $this->set_item( 'association', $this->get_record()->association );
    $this->set_item( 'alternate', $this->get_record()->alternate );
    $this->set_item( 'informant', $this->get_record()->informant );
    $this->set_item( 'proxy', $this->get_record()->proxy );

    $this->finish_setting_items();

    if( !is_null( $this->address_list ) )
    {
      $this->address_list->finish();
      $this->set_variable( 'address_list', $this->address_list->get_variables() );
    }

    if( !is_null( $this->phone_list ) )
    {
      $this->phone_list->finish();
      $this->set_variable( 'phone_list', $this->phone_list->get_variables() );
    }
  }
  
  /**
   * The address list widget.
   * @var address_list
   * @access protected
   */
  protected $address_list = NULL;
  
  /**
   * The phone list widget.
   * @var phone_list
   * @access protected
   */
  protected $phone_list = NULL;
}
?>
