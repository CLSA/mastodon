<?php
/**
 * proxy_form_entry_view.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget proxy_form_entry view
 */
class proxy_form_entry_view extends base_form_entry_view
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
    parent::__construct( 'proxy', $args );
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

    // add the entry values
    $this->add_item( 'uid', 'string', 'CLSA ID' );
    $this->add_item( 'proxy', 'boolean', 'Use Proxy' );
    $this->add_item( 'already_identified', 'boolean', 'Already Identified' );
    $this->add_item( 'proxy_first_name', 'string', 'Proxy\'s First Name' );
    $this->add_item( 'proxy_last_name', 'string', 'Proxy\'s Last Name' );
    $this->add_item( 'proxy_apartment_number', 'string', 'Proxy\'s Apartment #' );
    $this->add_item( 'proxy_street_number', 'string', 'Proxy\'s Street #' );
    $this->add_item( 'proxy_street_name', 'string', 'Proxy\'s Street Name' );
    $this->add_item( 'proxy_box', 'string', 'Proxy\'s Post Office Box #' );
    $this->add_item( 'proxy_rural_route', 'string', 'Proxy\'s Rural Route #' );
    $this->add_item( 'proxy_address_other', 'string', 'Proxy\'s Other Address' );
    $this->add_item( 'proxy_city', 'string', 'Proxy\'s City' );
    $this->add_item( 'proxy_region_id', 'enum', 'Proxy\'s Province' );
    $this->add_item( 'proxy_postcode', 'string', 'Proxy\'s Postal Code' );
    $this->add_item( 'proxy_address_note', 'text', 'Proxy Address Note' );
    $this->add_item( 'proxy_phone', 'string', 'Proxy\'s Phone Number' );
    $this->add_item( 'proxy_phone_note', 'text', 'Proxy Phone Note' );
    $this->add_item( 'proxy_note', 'text', 'Proxy Note' );
    $this->add_item( 'informant', 'boolean', 'Use Informant' );
    $this->add_item( 'same_as_proxy', 'boolean', 'Same As Proxy' );
    $this->add_item( 'informant_first_name', 'string', 'Informant\'s First Name' );
    $this->add_item( 'informant_last_name', 'string', 'Informant\'s Last Name' );
    $this->add_item( 'informant_apartment_number', 'string', 'Informant\'s Apartment #' );
    $this->add_item( 'informant_street_number', 'string', 'Informant\'s Street #' );
    $this->add_item( 'informant_street_name', 'string', 'Informant\'s Street Name' );
    $this->add_item( 'informant_box', 'string', 'Informant\'s Post Office Box #' );
    $this->add_item( 'informant_rural_route', 'string', 'Informant\'s Rural Route #' );
    $this->add_item( 'informant_address_other', 'string', 'Informant\'s Other Address' );
    $this->add_item( 'informant_city', 'string', 'Informant\'s City' );
    $this->add_item( 'informant_region_id', 'enum', 'Informant\'s Province' );
    $this->add_item( 'informant_postcode', 'string', 'Informant\'s Postal Code' );
    $this->add_item( 'informant_address_note', 'text', 'Informant Address Note' );
    $this->add_item( 'informant_phone', 'string', 'Informant\'s Phone Number' );
    $this->add_item( 'informant_phone_note', 'text', 'Informant Phone Note' );
    $this->add_item( 'informant_note', 'text', 'Informant Note' );
    $this->add_item( 'informant_continue', 'boolean', 'Informant Continue' );
    $this->add_item( 'health_card', 'boolean', 'Health Card Continue' );
    $this->add_item( 'signed', 'boolean', 'Signed' );
    $this->add_item( 'date', 'date', 'Date Signed' );
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

    $region_class_name = lib::get_class_name( 'database\region' );

    // create enum arrays
    $region_mod = lib::create( 'database\modifier' );
    $region_mod->order( 'country' );
    $region_mod->order( 'name' );
    $region_list = array();
    foreach( $region_class_name::select( $region_mod ) as $db_region )
      $region_list[$db_region->id] = $db_region->name.', '.$db_region->country;

    // set the entry values
    $record = $this->get_record();
    $this->set_item( 'uid', $record->uid, false );
    $this->set_item( 'proxy', $record->proxy, false );
    $this->set_item( 'already_identified', $record->already_identified, false );
    $this->set_item( 'proxy_first_name', $record->proxy_first_name, false );
    $this->set_item( 'proxy_last_name', $record->proxy_last_name, false );
    $this->set_item( 'proxy_apartment_number', $record->proxy_apartment_number, false );
    $this->set_item( 'proxy_street_number', $record->proxy_street_number, false );
    $this->set_item( 'proxy_street_name', $record->proxy_street_name, false );
    $this->set_item( 'proxy_box', $record->proxy_box, false );
    $this->set_item( 'proxy_rural_route', $record->proxy_rural_route, false );
    $this->set_item( 'proxy_address_other', $record->proxy_address_other, false );
    $this->set_item( 'proxy_city', $record->proxy_city, false );
    $this->set_item( 'proxy_region_id', $record->proxy_region_id, false, $region_list );
    $this->set_item( 'proxy_postcode', $record->proxy_postcode, false );
    $this->set_item( 'proxy_address_note', $record->proxy_address_note, false );
    $this->set_item( 'proxy_phone', $record->proxy_phone, false );
    $this->set_item( 'proxy_phone_note', $record->proxy_phone_note, false );
    $this->set_item( 'proxy_note', $record->proxy_note, false );
    $this->set_item( 'informant', $record->informant, false );
    $this->set_item( 'same_as_proxy', $record->same_as_proxy, false );
    $this->set_item( 'informant_first_name', $record->informant_first_name, false );
    $this->set_item( 'informant_last_name', $record->informant_last_name, false );
    $this->set_item( 'informant_apartment_number', $record->informant_apartment_number, false );
    $this->set_item( 'informant_street_number', $record->informant_street_number, false );
    $this->set_item( 'informant_street_name', $record->informant_street_name, false );
    $this->set_item( 'informant_box', $record->informant_box, false );
    $this->set_item( 'informant_rural_route', $record->informant_rural_route, false );
    $this->set_item( 'informant_address_other', $record->informant_address_other, false );
    $this->set_item( 'informant_city', $record->informant_city, false );
    $this->set_item( 'informant_region_id', $record->informant_region_id, false, $region_list );
    $this->set_item( 'informant_postcode', $record->informant_postcode, false );
    $this->set_item( 'informant_address_note', $record->informant_address_note, false );
    $this->set_item( 'informant_phone', $record->informant_phone, false );
    $this->set_item( 'informant_phone_note', $record->informant_phone_note, false );
    $this->set_item( 'informant_note', $record->informant_note, false );
    $this->set_item( 'informant_continue', $record->informant_continue, false );
    $this->set_item( 'health_card', $record->health_card, false );
    $this->set_item( 'signed', $this->get_record()->signed, true );
    $this->set_item( 'date', $record->date, false );
  }
}
?>
