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
    if( is_null( $db_proxy_form_entry ) || !$db_proxy_form_entry->id )
    {
      throw lib::create( 'exception\runtime',
        'Tried to import invalid proxy form entry.', __METHOD__ );
    }

    $database_class_name = lib::get_class_name( 'database\database' );
    $participant_class_name = lib::get_class_name( 'database\participant' );
    $alternate_class_name = lib::get_class_name( 'database\alternate' );
    $db_participant =
      $participant_class_name::get_unique_record( 'uid', $db_proxy_form_entry->uid );

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

      // if this participant already has an alternate with the same first and last name then
      // overwrite instead of creating a new record
      $alternate_mod = lib::create( 'database\modifier' );
      $alternate_mod->where( 'participant_id', '=', $db_participant->id );
      $alternate_mod->where( 'first_name', '=', $db_proxy_form_entry->proxy_first_name );
      $alternate_mod->where( 'last_name', '=', $db_proxy_form_entry->proxy_last_name );
      $alternate_list = $alternate_class_name::select( $alternate_mod );
      $db_proxy_alternate = current( $alternate_list );

      if( false == $db_proxy_alternate )
      { // create a new alternate if no match was found
        $db_person = lib::create( 'database\person' );
        $db_person->save();
        $db_proxy_alternate = lib::create( 'database\alternate' );
        $db_proxy_alternate->person_id = $db_person->id;
      }
      else
      {
        $db_person = $db_proxy_alternate->get_person();

        // replace any address and phone numbers
        foreach( $db_proxy_alternate->get_address_list() as $db_address ) $db_address->delete();
        foreach( $db_proxy_alternate->get_phone_list() as $db_phone ) $db_phone->delete();
      }

      $db_proxy_alternate->participant_id = $db_participant->id;
      $db_proxy_alternate->alternate = false;
      $db_proxy_alternate->informant =
        $db_proxy_form_entry->same_as_proxy && $db_proxy_form_entry->informant;
      $db_proxy_alternate->proxy = true;
      $db_proxy_alternate->first_name = $db_proxy_form_entry->proxy_first_name;
      $db_proxy_alternate->last_name = $db_proxy_form_entry->proxy_last_name;
      $db_proxy_alternate->association = 'Unknown';
      $db_proxy_alternate->save();

      if( !is_null( $db_proxy_form_entry->proxy_note ) )
      {
        // import data to the person_note table
        $db_participant_note = lib::create( 'database\person_note' );
        $db_participant_note->person_id = $db_person->id;
        $db_participant_note->user_id = $db_proxy_form_entry->user_id;
        $db_participant_note->datetime = util::get_datetime_object()->format( 'Y-m-d' );
        $db_participant_note->note = $db_proxy_form_entry->proxy_note;
        $db_participant_note->save();
      }

      // import data to the address table
      $address = util::parse_address(
        $db_proxy_form_entry->proxy_apartment_number,
        $db_proxy_form_entry->proxy_street_number,
        $db_proxy_form_entry->proxy_street_name,
        $db_proxy_form_entry->proxy_box,
        $db_proxy_form_entry->proxy_rural_route,
        $db_proxy_form_entry->proxy_address_other );

      $db_proxy_address = lib::create( 'database\address' );
      $db_proxy_address->person_id = $db_person->id;
      $db_proxy_address->active = true;
      $db_proxy_address->rank = 1;
      $db_proxy_address->address1 = $address[0];
      $db_proxy_address->address2 = $address[1];
      $db_proxy_address->city = $db_proxy_form_entry->proxy_city;
      $db_proxy_address->region_id = $db_proxy_form_entry->proxy_region_id;
      $postcode = 6 == strlen( $db_proxy_form_entry->proxy_postcode )
                ? sprintf( '%s %s',
                           substr( $db_proxy_form_entry->proxy_postcode, 0, 3 ),
                           substr( $db_proxy_form_entry->proxy_postcode, 3, 3 ) )
                : $db_proxy_form_entry->proxy_postcode;
      $db_proxy_address->postcode = $postcode;
      $db_proxy_address->source_postcode();
      $db_proxy_address->note = $db_proxy_form_entry->proxy_address_note;
      $db_proxy_address->save();

      // import data to the phone table
      $db_proxy_phone = lib::create( 'database\phone' );
      $db_proxy_phone->person_id = $db_person->id;
      $db_proxy_phone->active = true;
      $db_proxy_phone->rank = 1;
      $db_proxy_phone->type = 'other';
      $db_proxy_phone->number = $db_proxy_form_entry->proxy_phone;
      $db_proxy_phone->note = $db_proxy_form_entry->proxy_phone_note;
      $db_proxy_phone->save();
    }

    if( $db_proxy_form_entry->informant && !$db_proxy_form_entry->same_as_proxy )
    {
      // import data to the person and alternate table

      // if this participant already has an alternate with the same first and last name then
      // overwrite instead of creating a new record
      $alternate_mod = lib::create( 'database\modifier' );
      $alternate_mod->where( 'participant_id', '=', $db_participant->id );
      $alternate_mod->where( 'first_name', '=', $db_proxy_form_entry->informant_first_name );
      $alternate_mod->where( 'last_name', '=', $db_proxy_form_entry->informant_last_name );
      $alternate_list = $alternate_class_name::select( $alternate_mod );
      $db_informant_alternate = current( $alternate_list );

      if( false == $db_informant_alternate )
      { // create a new alternate if no match was found
        $db_person = lib::create( 'database\person' );
        $db_person->save();
        $db_informant_alternate = lib::create( 'database\alternate' );
        $db_informant_alternate->person_id = $db_person->id;
      }
      else
      {
        $db_person = $db_informant_alternate->get_person();

        // replace any address and phone numbers
        foreach( $db_informant_alternate->get_address_list() as $db_address ) $db_address->delete();
        foreach( $db_informant_alternate->get_phone_list() as $db_phone ) $db_phone->delete();
      }

      $db_informant_alternate->participant_id = $db_participant->id;
      $db_informant_alternate->alternate = false;
      $db_informant_alternate->informant = true;
      $db_informant_alternate->proxy = false;
      $db_informant_alternate->first_name = $db_proxy_form_entry->informant_first_name;
      $db_informant_alternate->last_name = $db_proxy_form_entry->informant_last_name;
      $db_informant_alternate->association = 'Unknown';
      $db_informant_alternate->save();

      if( !is_null( $db_proxy_form_entry->informant_note ) )
      {
        // import data to the person_note table
        $db_participant_note = lib::create( 'database\person_note' );
        $db_participant_note->person_id = $db_person->id;
        $db_participant_note->user_id = $db_proxy_form_entry->user_id;
        $db_participant_note->datetime = util::get_datetime_object()->format( 'Y-m-d' );
        $db_participant_note->note = $db_proxy_form_entry->informant_note;
        $db_participant_note->save();
      }

      // import data to the address table
      $address = util::parse_address(
        $db_proxy_form_entry->informant_apartment_number,
        $db_proxy_form_entry->informant_street_number,
        $db_proxy_form_entry->informant_street_name,
        $db_proxy_form_entry->informant_box,
        $db_proxy_form_entry->informant_rural_route,
        $db_proxy_form_entry->informant_address_other );

      $db_informant_address = lib::create( 'database\address' );
      $db_informant_address->person_id = $db_person->id;
      $db_informant_address->active = true;
      $db_informant_address->rank = 1;
      $db_informant_address->address1 = $address[0];
      $db_informant_address->address2 = $address[1];
      $db_informant_address->city = $db_proxy_form_entry->informant_city;
      $db_informant_address->region_id = $db_proxy_form_entry->informant_region_id;
      $postcode = 6 == strlen( $db_proxy_form_entry->informant_postcode )
                ? sprintf( '%s %s',
                           substr( $db_proxy_form_entry->informant_postcode, 0, 3 ),
                           substr( $db_proxy_form_entry->informant_postcode, 3, 3 ) )
                : $db_proxy_form_entry->informant_postcode;
      $db_informant_address->postcode = $postcode;
      $db_informant_address->source_postcode();
      $db_informant_address->note = $db_proxy_form_entry->informant_address_note;
      $db_informant_address->save();

      // import data to the phone table
      $db_informant_phone = lib::create( 'database\phone' );
      $db_informant_phone->person_id = $db_person->id;
      $db_informant_phone->active = true;
      $db_informant_phone->rank = 1;
      $db_informant_phone->type = 'other';
      $db_informant_phone->number = $db_proxy_form_entry->informant_phone;
      $db_informant_phone->note = $db_proxy_form_entry->informant_phone_note;
      $db_informant_phone->save();
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
        $database_class_name::format_string( $db_proxy_form_entry->uid ),
        $database_class_name::format_string( $db_proxy_form_entry->health_card ) ) );
    }

    // save the new alternate record to the form
    $this->complete = true;
    if( $db_proxy_form_entry->proxy )
      $this->proxy_alternate_id = $db_proxy_alternate->id;
    if( $db_proxy_form_entry->same_as_proxy )
      $this->informant_alternate_id = $db_proxy_alternate->id;
    else if( $db_proxy_form_entry->informant )
      $this->informant_alternate_id = $db_informant_alternate->id;
    $this->save();
  }
}
?>
