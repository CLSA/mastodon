<?php
/**
 * contact_form.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\database;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * contact_form: record
 */
class contact_form extends base_form
{
  /**
   * Implements the parent's abstract import method.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param database\form_entry $db_base_form_entry The entry to be used as the valid data.
   * @access public
   */
  public function import( $db_contact_form_entry )
  {
    if( is_null( $db_contact_form_entry ) || !$db_contact_form_entry->id )
    {
      throw lib::create( 'exception\runtime',
        'Tried to import invalid contact form entry.', __METHOD__ );
    }

    $address_class_name = lib::get_class_name( 'database\address' );
    $source_class_name = lib::get_class_name( 'database\source' );
    $participant_class_name = lib::get_class_name( 'database\participant' );
    $age_group_class_name = lib::get_class_name( 'database\age_group' );
    $application_class_name = lib::get_class_name( 'database\application' );
    $site_class_name = lib::get_class_name( 'database\site' );
    $event_type_class_name = lib::get_class_name( 'database\event_type' );

    // start by checking for address duplicates in the same cohort
    $address = util::parse_address(
      $db_contact_form_entry->apartment_number,
      $db_contact_form_entry->street_number,
      $db_contact_form_entry->street_name,
      $db_contact_form_entry->box,
      $db_contact_form_entry->rural_route,
      $db_contact_form_entry->address_other );
    $postcode = 6 == strlen( $db_contact_form_entry->postcode )
              ? sprintf( '%s %s',
                         substr( $db_contact_form_entry->postcode, 0, 3 ),
                         substr( $db_contact_form_entry->postcode, 3, 3 ) )
              : $db_contact_form_entry->postcode;
    
    $address_mod = lib::create( 'database\modifier' );
    $address_mod->where( 'address1', '=', $address[0] );
    $address_mod->where( 'address2', '=', $address[1] );
    $address_mod->where( 'city', '=', $db_contact_form_entry->city );
    $address_mod->where( 'region_id', '=', $db_contact_form_entry->region_id );
    $address_mod->where( 'postcode', '=', $postcode );
    foreach( $address_class_name::select( $address_mod ) as $db_address )
    {
      $db_participant = $db_address->get_participant();
      if( $db_participant && $db_participant->cohort_id == $db_contact_form_entry->cohort_id )
      {
        throw lib::create( 'exception\notice',
          sprintf( 'Unable to import contact form because a %s participant already exists '.
                   'at the given address.',
                   $db_contact_form_entry->get_cohort()->name ),
          __METHOD__ );
      }
    }

    // link to the form
    $this->validated_contact_form_entry_id = $db_contact_form_entry->id;

    // all participants are from the ministry source
    $db_source = $source_class_name::get_unique_record( 'name', 'ministry' );

    // make sure there is a uid available
    $uid = $participant_class_name::get_new_uid();
    if( is_null( $uid ) ) throw lib::create( 'exception\runtime',
      'Tried to import a contact form but the participant UID pool is empty!',
      __METHOD__ );
    
    $year = date( 'Y' );
    $dob = NULL;
    $lower = NULL;
    if( '45-49' == $db_contact_form_entry->age_bracket )
    {
      $dob = sprintf( '%d-01-01', $year - 47 );
      $lower = 45;
    }
    else if( '50-54' == $db_contact_form_entry->age_bracket )
    {
      $dob = sprintf( '%d-01-01', $year - 52 );
      $lower = 45;
    }
    else if( '55-59' == $db_contact_form_entry->age_bracket )
    {
      $dob = sprintf( '%d-01-01', $year - 57 );
      $lower = 55;
    }
    else if( '60-64' == $db_contact_form_entry->age_bracket )
    {
      $dob = sprintf( '%d-01-01', $year - 62 );
      $lower = 55;
    }
    else if( '65-69' == $db_contact_form_entry->age_bracket )
    {
      $dob = sprintf( '%d-01-01', $year - 67 );
      $lower = 65;
    }
    else if( '70-74' == $db_contact_form_entry->age_bracket )
    {
      $dob = sprintf( '%d-01-01', $year - 72 );
      $lower = 65;
    }
    else if( '75-79' == $db_contact_form_entry->age_bracket )
    {
      $dob = sprintf( '%d-01-01', $year - 77 );
      $lower = 75;
    }
    else if( '80-85' == $db_contact_form_entry->age_bracket )
    {
      $dob = sprintf( '%d-01-01', $year - 82 );
      $lower = 75;
    }

    $db_age_group = !is_null( $lower )
                  ? $age_group_class_name::get_unique_record( 'lower', $lower )
                  : NULL;

    // import data to the participant table
    $db_participant = lib::create( 'database\participant' );
    $db_participant->active = true;
    $db_participant->uid = $uid;
    $db_participant->source_id = $db_source->id;
    $db_participant->cohort_id = $db_contact_form_entry->cohort_id;
    $db_participant->grouping = $db_contact_form_entry->code;
    $db_participant->first_name = $db_contact_form_entry->first_name;
    $db_participant->last_name = $db_contact_form_entry->last_name;
    $db_participant->gender = $db_contact_form_entry->gender;
    $db_participant->date_of_birth = util::get_datetime_obj( $dob );
    if( !is_null( $db_age_group ) ) $db_participant->age_group_id = $db_age_group->id;
    $db_participant->language_id = $db_contact_form_entry->language_id;
    $db_participant->email = $db_contact_form_entry->email;
    $db_participant->save();

    if( !is_null( $db_contact_form_entry->note ) )
    {
      // import data to the note table
      $db_note = lib::create( 'database\note' );
      $db_note->participant_id = $db_participant->id;
      $db_note->user_id = $db_contact_form_entry->user_id;
      $db_note->datetime = util::get_datetime_object();
      $db_note->note = $db_contact_form_entry->note;
      $db_note->save();
    }

    // add the consent to contact signed event to the participant
    $db_event_type =
      $event_type_class_name::get_unique_record( 'name', 'consent to contact signed' );
    if( !is_null( $db_event_type ) )
    {
      $db_event = lib::create( 'database\event' );
      $db_event->participant_id = $db_participant->id;
      $db_event->event_type_id = $db_event_type->id;
      $db_event->datetime = is_null( $db_contact_form_entry->participant_date )
                          ? util::get_datetime_object()
                          : $db_contact_form_entry->participant_date;
      $db_event->save();
    }

    // add the consent to contact stamped event to the participant
    $db_event_type =
      $event_type_class_name::get_unique_record( 'name', 'consent to contact stamped' );
    if( !is_null( $db_event_type ) && !is_null( $db_contact_form_entry->stamped_date ) )
    {
      $db_event = lib::create( 'database\event' );
      $db_event->participant_id = $db_participant->id;
      $db_event->event_type_id = $db_event_type->id;
      $db_event->datetime = $db_contact_form_entry->stamped_date;
      $db_event->save();
    }
    
    // import data to the address table
    $db_address = lib::create( 'database\address' );
    $db_address->participant_id = $db_participant->id;
    $db_address->active = true;
    $db_address->rank = 1;
    $db_address->address1 = $address[0];
    $db_address->address2 = $address[1];
    $db_address->city = $db_contact_form_entry->city;
    $db_address->region_id = $db_contact_form_entry->region_id;
    $db_address->postcode = $postcode;
    $db_address->source_postcode();
    $db_address->note = $db_contact_form_entry->address_note;
    $db_address->save();

    // import data to the phone table
    $db_home_phone = NULL;
    $db_mobile_phone = NULL;
    $rank = 1;
    if( !is_null( $db_contact_form_entry->home_phone ) )
    {
      $db_home_phone = lib::create( 'database\phone' );
      $db_home_phone->participant_id = $db_participant->id;
      $db_home_phone->address_id = $db_address->id;
      $db_home_phone->active = true;
      $db_home_phone->rank = $rank;
      $db_home_phone->type = 'home';
      $db_home_phone->number = $db_contact_form_entry->home_phone;
      $db_home_phone->note = $db_contact_form_entry->home_phone_note;
      $db_home_phone->save();
      $rank++;
    }
    if( !is_null( $db_contact_form_entry->mobile_phone ) )
    {
      $db_mobile_phone = lib::create( 'database\phone' );
      $db_mobile_phone->participant_id = $db_participant->id;
      $db_mobile_phone->active = true;
      $db_mobile_phone->rank = $rank;
      $db_mobile_phone->type = 'mobile';
      $db_mobile_phone->number = $db_contact_form_entry->mobile_phone;
      $db_mobile_phone->note = $db_contact_form_entry->mobile_phone_note;
      $db_mobile_phone->save();
    }

    // if mobile is favoured over home then rearange phone ranks
    if( 'mobile' == $db_contact_form_entry->phone_preference &&
        !is_null( $db_home_phone ) && !is_null( $db_mobile_phone ) )
    {
      $db_home_phone->rank = 0;
      $db_home_phone->save();
      $db_mobile_phone->rank = 1;
      $db_mobile_phone->save();
      $db_home_phone->rank = 2;
      $db_home_phone->save();
    }

    // save the new participant record to the form
    $this->complete = true;
    $this->participant_id = $db_participant->id;
    $this->save();
  }
}
