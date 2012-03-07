<?php
/**
 * proxy_form.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\database
 * @filesource
 */

namespace mastodon\database;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * proxy_form: record
 *
 * @package mastodon\database
 */
class proxy_form extends base_form
{
  /**
   * The proxy form links to the alternate table.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @return string
   * @access public
   */
  public static function get_link_name()
  {
    return 'alternate_id';
  }

  /**
   * Implements the parent's abstract import method.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param database\form_entry $db_base_form_entry The entry to be used as the valid data.
   * @access public
   */
  public function import( $db_proxy_form_entry )
  {
    if( is_null( $db_proxy_form_entry ) || !$db_proxy_form_entry->id )
    {
      throw lib::create( 'exception\runtime',
        'Tried to import invalid proxy form entry.', __METHOD__ );
    }

    $participant_class_name = lib::get_class_name( 'database\participant' );
    $db_participant =
      $participant_class_name::get_unique_record( 'uid', $db_consent_form_entry->uid );

    // link to the form
    $this->validated_proxy_form_entry_id = $db_proxy_form_entry->id;

    // import data to the status table
    $db_status = lib::create( 'database\status' );
    $db_status->participant_id = $db_participant->id;
    $db_status->datetime = $db_proxy_form_entry->date;
    $db_status->event = 'consent for proxy received';
    $db_status->save();

    if( $db_proxy_form_entry->proxy )
    {
      // import data to the person and alternate table
      $db_person = lib::create( 'database\person' );
      $db_person->save();

      $db_alternate = lib::create( 'database\alternate' );
      $db_alternate->person_id = $db_person->id;
      $db_alternate->participant_id = $db_participant->id;
      $db_alternate->alternate = false;
      $db_alternate->informant =
        $db_proxy_form_entry->same_as_proxy && $db_proxy_form_entry->informant;
      $db_alternate->proxy = true;
      $db_alternate->first_name = $db_proxy_form_entry->proxy_first_name;
      $db_alternate->last_name = $db_proxy_form_entry->proxy_last_name;
      $db_alternate->association = 'Unknown';
      $db_alternate->save();

      if( !is_null( $db_proxy_form_entry->proxy_note ) )
      {
        // import data to the person_note table
        $db_participant_note = lib::create( 'database\person_note' );
        $db_participant_note->person_id = $db_person->id;
        $db_participant_note->user_id = $db_proxy_form_entry->user_id;
        $db_participant_note->datetime = util::get_datetime_object()->format( 'Y-m-d' );
        $db_participant_note->note = $db_proxy_form_entry->proxy_note;
      }

      // import data to the address table
      $address = util::parse_address(
        $db_proxy_form_entry->proxy_apartment_number,
        $db_proxy_form_entry->proxy_street_number,
        $db_proxy_form_entry->proxy_street_name,
        $db_proxy_form_entry->proxy_box,
        $db_proxy_form_entry->proxy_rural_route,
        $db_proxy_form_entry->proxy_other );

      $db_address = lib::create( 'database\address' );
      $db_address->address1 = $address[0];
      $db_address->address2 = $address[1];
      $db_address->city = $db_proxy_form_entry->proxy_city;
      $db_address->region_id = $db_proxy_form_entry->proxy_region_id;
      $db_address->postcode = $db_proxy_form_entry->proxy_postcode;

      // import data to the phone table
      $db_phone = lib::create( 'database\phone' );
      $db_phone->person_id = $db_person->id;
      $db_phone->active = true;
      $db_phone->rank = 1;
      $db_phone->type = 'other';
      $db_phone->number = $db_proxy_form_entry->proxy_phone;
      $db_phone->save();
    }

    if( $db_proxy_form_entry->informant && !$db_proxy_form_entry->same_as_proxy )
    {
      // import data to the person and alternate table
      $db_person = lib::create( 'database\person' );
      $db_person->save();

      $db_alternate = lib::create( 'database\alternate' );
      $db_alternate->person_id = $db_person->id;
      $db_alternate->participant_id = $db_participant->id;
      $db_alternate->alternate = false;
      $db_alternate->informant = true;
      $db_alternate->proxy = false;
      $db_alternate->first_name = $db_proxy_form_entry->informant_first_name;
      $db_alternate->last_name = $db_proxy_form_entry->informant_last_name;
      $db_alternate->association = 'Unknown';
      $db_alternate->save();

      if( !is_null( $db_proxy_form_entry->informant_note ) )
      {
        // import data to the person_note table
        $db_participant_note = lib::create( 'database\person_note' );
        $db_participant_note->person_id = $db_person->id;
        $db_participant_note->user_id = $db_proxy_form_entry->user_id;
        $db_participant_note->datetime = util::get_datetime_object()->format( 'Y-m-d' );
        $db_participant_note->note = $db_proxy_form_entry->informant_note;
      }

      // import data to the address table
      $address = util::parse_address(
        $db_proxy_form_entry->proxy_apartment_number,
        $db_proxy_form_entry->proxy_street_number,
        $db_proxy_form_entry->proxy_street_name,
        $db_proxy_form_entry->proxy_box,
        $db_proxy_form_entry->proxy_rural_route,
        $db_proxy_form_entry->proxy_other );

      $db_address = lib::create( 'database\address' );
      $db_address->address1 = $address[0];
      $db_address->address2 = $address[1];
      $db_address->city = $db_proxy_form_entry->proxy_city;
      $db_address->region_id = $db_proxy_form_entry->proxy_region_id;
      $db_address->postcode = $db_proxy_form_entry->proxy_postcode;

      // import data to the phone table
      $db_phone = lib::create( 'database\phone' );
      $db_phone->person_id = $db_person->id;
      $db_phone->active = true;
      $db_phone->rank = 1;
      $db_phone->type = 'other';
      $db_phone->number = $db_proxy_form_entry->proxy_phone;
      $db_phone->save();
    }
    
    // import data to the participant table
    if( !is_null( $db_proxy_form_entry->informant_continue ) )
      $db_participant->use_informant = $db_proxy_form_entry->informant_continue;

    // import data to the hin table
    if( !is_null( $db_proxy_form_entry->health_card ) )
    {
      static::db()->execute( sprintf(
        'INSERT INTO hin SET uid = %s, future_access = %s '.
        'ON DUPLICATE KEY '.
        'UPDATE uid = VALUES( uid ), future_access = VALUES( future_access )',
        $database_class_name::format_string( $db_consent_form_entry->uid ),
        $database_class_name::format_string( $db_consent_form_entry->health_card ) ) );
    }

    // save the new alternate record to the form
    $this->alternate_id = $db_alternate->id;
    $this->save();
  }
}
?>
