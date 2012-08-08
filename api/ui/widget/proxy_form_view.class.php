<?php
/**
 * proxy_form_view.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget proxy_form view
 */
class proxy_form_view extends base_form_view
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
    parent::__construct( 'proxy_form', $args );
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

    // add entry values
    $this->add_item( 'uid', 'CLSA ID' ); 
    $this->add_item( 'proxy', 'Use Proxy' ); 
    $this->add_item( 'already_identified', 'Already Identified' ); 
    $this->add_item( 'proxy_first_name', 'Proxy\'s First Name' ); 
    $this->add_item( 'proxy_last_name', 'Proxy\'s Last Name' ); 
    $this->add_item( 'proxy_apartment_number', 'Proxy\'s Apartment #' ); 
    $this->add_item( 'proxy_street_number', 'Proxy\'s Street #' ); 
    $this->add_item( 'proxy_street_name', 'Proxy\'s Street Name' ); 
    $this->add_item( 'proxy_box', 'Proxy\'s Post Office Box #' ); 
    $this->add_item( 'proxy_rural_route', 'Proxy\'s Rural Route #' ); 
    $this->add_item( 'proxy_address_other', 'Proxy\'s Other Address' ); 
    $this->add_item( 'proxy_city', 'Proxy\'s City' ); 
    $this->add_item( 'proxy_region_id', 'Proxy\'s Province' ); 
    $this->add_item( 'proxy_postcode', 'Proxy\'s Postal Code' ); 
    $this->add_item( 'proxy_address_note', 'text', 'Proxy Address Note' ); 
    $this->add_item( 'proxy_phone', 'Proxy\'s Phone Number' ); 
    $this->add_item( 'proxy_phone_note', 'text', 'Proxy Phone Note' ); 
    $this->add_item( 'proxy_note', 'text', 'Proxy Note' ); 
    $this->add_item( 'informant', 'Use Informant' ); 
    $this->add_item( 'same_as_proxy', 'Same As Proxy' ); 
    $this->add_item( 'informant_first_name', 'Informant\'s First Name' ); 
    $this->add_item( 'informant_last_name', 'Informant\'s Last Name' ); 
    $this->add_item( 'informant_apartment_number', 'Informant\'s Apartment #' ); 
    $this->add_item( 'informant_street_number', 'Informant\'s Street #' ); 
    $this->add_item( 'informant_street_name', 'Informant\'s Street Name' ); 
    $this->add_item( 'informant_box', 'Informant\'s Post Office Box #' ); 
    $this->add_item( 'informant_rural_route', 'Informant\'s Rural Route #' ); 
    $this->add_item( 'informant_address_other', 'Informant\'s Other Address' ); 
    $this->add_item( 'informant_city', 'Informant\'s City' ); 
    $this->add_item( 'informant_region_id', 'Informant\'s Province' ); 
    $this->add_item( 'informant_postcode', 'Informant\'s Postal Code' ); 
    $this->add_item( 'informant_address_note', 'text', 'Informant Address Note' ); 
    $this->add_item( 'informant_phone', 'Informant\'s Phone Number' ); 
    $this->add_item( 'informant_phone_note', 'text', 'Informant Phone Note' ); 
    $this->add_item( 'informant_note', 'text', 'Informant Note' ); 
    $this->add_item( 'informant_continue', 'Informant Continue' ); 
    $this->add_item( 'health_card', 'Health Card Continue' ); 
    $this->add_item( 'signed', 'Signed' );
    $this->add_item( 'date', 'Date Signed' ); 
  }
}
?>
