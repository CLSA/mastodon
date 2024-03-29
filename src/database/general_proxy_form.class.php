<?php
/**
 * general_proxy_form.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace mastodon\database;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * general_proxy_form: record
 */
class general_proxy_form extends base_form
{
  /**
   * Implements the parent's abstract import method.
   * @param database\form_entry $db_base_form_entry The entry to be used as the valid data.
   * @access public
   */
  public function import( $db_general_proxy_form_entry )
  {
    parent::import( $db_general_proxy_form_entry );

    // add the continue qnaire, continue dcs visits and future HIN consent
    $db_form = $this->get_form();
    if( !is_null( $db_general_proxy_form_entry->continue_questionnaires ) )
      $db_form->add_consent(
        'continue questionnaires', array( 'accept' => $db_general_proxy_form_entry->continue_questionnaires ) );
    if( !is_null( $db_general_proxy_form_entry->continue_dcs_visits ) )
      $db_form->add_consent(
        'continue DCS visits', array( 'accept' => $db_general_proxy_form_entry->continue_dcs_visits ) );
    if( !is_null( $db_general_proxy_form_entry->hin_future_access ) )
      $db_form->add_consent(
        'continue health card', array( 'accept' => $db_general_proxy_form_entry->hin_future_access ) );

    $informant_exists =
      !is_null( $db_general_proxy_form_entry->informant_first_name ) &&
      !is_null( $db_general_proxy_form_entry->informant_last_name );

    $informant_same = (
      !$informant_exists &&
      $db_general_proxy_form_entry->same_as_proxy
    ) || (
      $informant_exists &&
      $db_general_proxy_form_entry->proxy_first_name == $db_general_proxy_form_entry->informant_first_name &&
      $db_general_proxy_form_entry->proxy_last_name == $db_general_proxy_form_entry->informant_last_name
    );

    if( !is_null( $db_general_proxy_form_entry->proxy_first_name ) &&
        !is_null( $db_general_proxy_form_entry->proxy_last_name ) )
    {
      $alternate_type_list = ['proxy'];
      if( $informant_same && $informant_same ) $alternate_type_list[] = 'informant';
      $db_form->add_alternate( array(
        'alternate_type_list' => $alternate_type_list,
        'first_name' => $db_general_proxy_form_entry->proxy_first_name,
        'last_name' => $db_general_proxy_form_entry->proxy_last_name,
        'global_note' => $db_general_proxy_form_entry->proxy_note,
        'address_international' => $db_general_proxy_form_entry->proxy_address_international,
        'apartment_number' => $db_general_proxy_form_entry->proxy_apartment_number,
        'street_number' => $db_general_proxy_form_entry->proxy_street_number,
        'street_name' => $db_general_proxy_form_entry->proxy_street_name,
        'box' => $db_general_proxy_form_entry->proxy_box,
        'rural_route' => $db_general_proxy_form_entry->proxy_rural_route,
        'address_other' => $db_general_proxy_form_entry->proxy_address_other,
        'city' => $db_general_proxy_form_entry->proxy_city,
        'region_id' => $db_general_proxy_form_entry->proxy_region_id,
        'international_region' => $db_general_proxy_form_entry->proxy_international_region,
        'international_country_id' => $db_general_proxy_form_entry->proxy_international_country_id,
        'postcode' => $db_general_proxy_form_entry->proxy_postcode,
        'address_note' => $db_general_proxy_form_entry->proxy_address_note,
        'phone_international' => $db_general_proxy_form_entry->proxy_phone_international,
        'phone' => $db_general_proxy_form_entry->proxy_phone,
        'phone_note' => $db_general_proxy_form_entry->proxy_phone_note
      ));
    }

    if( $informant_exists && !$informant_same )
    {
      $db_form->add_alternate( array(
        'alternate_type_list' => ['informant'],
        'first_name' => $db_general_proxy_form_entry->informant_first_name,
        'last_name' => $db_general_proxy_form_entry->informant_last_name,
        'global_note' => $db_general_proxy_form_entry->informant_note,
        'address_international' => $db_general_proxy_form_entry->informant_address_international,
        'apartment_number' => $db_general_proxy_form_entry->informant_apartment_number,
        'street_number' => $db_general_proxy_form_entry->informant_street_number,
        'street_name' => $db_general_proxy_form_entry->informant_street_name,
        'box' => $db_general_proxy_form_entry->informant_box,
        'rural_route' => $db_general_proxy_form_entry->informant_rural_route,
        'address_other' => $db_general_proxy_form_entry->informant_address_other,
        'city' => $db_general_proxy_form_entry->informant_city,
        'region_id' => $db_general_proxy_form_entry->informant_region_id,
        'international_region' => $db_general_proxy_form_entry->informant_international_region,
        'international_country_id' => $db_general_proxy_form_entry->informant_international_country_id,
        'postcode' => $db_general_proxy_form_entry->informant_postcode,
        'address_note' => $db_general_proxy_form_entry->informant_address_note,
        'phone_international' => $db_general_proxy_form_entry->informant_phone_international,
        'phone' => $db_general_proxy_form_entry->informant_phone,
        'phone_note' => $db_general_proxy_form_entry->informant_phone_note
      ) );
    }
  }
}
