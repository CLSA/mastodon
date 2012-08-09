<?php
/**
 * alternate_view.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget alternate view
 */
class alternate_view extends \cenozo\ui\widget\base_view
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
    
    // create an associative array with everything we want to display about the alternate
    $this->add_item( 'first_name', 'string', 'First Name' );
    $this->add_item( 'last_name', 'string', 'Last Name' );
    $this->add_item( 'association', 'string', 'Association' );
    $this->add_item( 'alternate', 'boolean', 'Alternate' );
    $this->add_item( 'informant', 'boolean', 'Informant' );
    $this->add_item( 'proxy', 'boolean', 'Proxy' );
    
    // create the address sub-list widget
    $this->address_list = lib::create( 'ui\widget\address_list', $this->arguments );
    $this->address_list->set_parent( $this );
    $this->address_list->set_heading( 'Addresses' );

    // create the phone sub-list widget
    $this->phone_list = lib::create( 'ui\widget\phone_list', $this->arguments );
    $this->phone_list->set_parent( $this );
    $this->phone_list->set_heading( 'Phone numbers' );
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

    // add a proxy form download action
    $db_proxy_form = $this->get_record()->get_proxy_form();
    if( !is_null( $db_proxy_form ) )
      $this->set_variable( 'proxy_form_id', $db_proxy_form->id );
    $this->add_action( 'proxy_form', 'Proxy Form', NULL,
      'Download this alternate\'s consent for proxy form, if available' );

    // set the view's items
    $this->set_item( 'first_name', $this->get_record()->first_name, true );
    $this->set_item( 'last_name', $this->get_record()->last_name, true );
    $this->set_item( 'association', $this->get_record()->association, true );
    $this->set_item( 'alternate', $this->get_record()->alternate, true );
    $this->set_item( 'informant', $this->get_record()->informant, true );
    $this->set_item( 'proxy', $this->get_record()->proxy, true );

    try
    {
      $this->address_list->process();
      $this->set_variable( 'address_list', $this->address_list->get_variables() );
    }
    catch( \cenozo\exception\permission $e ) {}

    try
    {
      $this->phone_list->process();
      $this->set_variable( 'phone_list', $this->phone_list->get_variables() );
    }
    catch( \cenozo\exception\permission $e ) {}
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
