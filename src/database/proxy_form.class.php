<?php
/**
 * proxy_form.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\database;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * proxy_form: record
 */
class proxy_form extends base_form
{
  /**
   * Implements the parent's abstract import method.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param database\form_entry $db_base_form_entry The entry to be used as the valid data.
   * @access public
   */
  public function import( $db_proxy_form_entry )
  {
    parent::import( $db_consent_form_entry );

    // add the physical tests and future HIN consent
    $db_form = $this->get_form();
    $db_form->add_consent(
      'continue physical tests', array( 'accept' => $db_proxy_form_entry->informant_continue ) );
    $db_form->add_consent( 'HIN future access', array( 'accept' => $db_proxy_form_entry->health_card ) );

    if( $db_proxy_form_entry->proxy )
    {
      $db_form->add_proxy_alternate( array(
        'first_name' => $db_proxy_form_entry->proxy_first_name,
        'last_name' => $db_proxy_form_entry->proxy_last_name,
        'apartment_number' => $db_proxy_form_entry->proxy_apartment_number,
        'street_number' => $db_proxy_form_entry->proxy_street_number,
        'street_name' => $db_proxy_form_entry->proxy_street_name,
        'box' => $db_proxy_form_entry->proxy_box,
        'rural_route' => $db_proxy_form_entry->proxy_rural_route,
        'address_other' => $db_proxy_form_entry->proxy_address_other,
        'city' => $db_proxy_form_entry->proxy_city,
        'region_id' => $db_proxy_form_entry->proxy_region_id,
        'postcode' => $db_proxy_form_entry->proxy_postcode,
        'address_note' => $db_proxy_form_entry->proxy_address_note,
        'phone' => $db_proxy_form_entry->proxy_phone,
        'phone_note' => $db_proxy_form_entry->proxy_phone_note,
        'note' => $db_proxy_form_entry->proxy_note,
        'informant' => $db_proxy_form_entry->informant,
        'same_as_proxy' => $db_proxy_form_entry->same_as_proxy
      ) );
    }

    if( $db_proxy_form_entry->informant )
    {
      $db_form->add_informant_alternate( array(
        'first_name' => $db_proxy_form_entry->informant_first_name,
        'last_name' => $db_proxy_form_entry->informant_last_name,
        'apartment_number' => $db_proxy_form_entry->informant_apartment_number,
        'street_number' => $db_proxy_form_entry->informant_street_number,
        'street_name' => $db_proxy_form_entry->informant_street_name,
        'box' => $db_proxy_form_entry->informant_box,
        'rural_route' => $db_proxy_form_entry->informant_rural_route,
        'address_other' => $db_proxy_form_entry->informant_address_other,
        'city' => $db_proxy_form_entry->informant_city,
        'region_id' => $db_proxy_form_entry->informant_region_id,
        'postcode' => $db_proxy_form_entry->informant_postcode,
        'address_note' => $db_proxy_form_entry->informant_address_note,
        'phone' => $db_proxy_form_entry->informant_phone,
        'phone_note' => $db_proxy_form_entry->informant_phone_note,
        'note' => $db_proxy_form_entry->informant_note
      ) );
    }
  }
}
