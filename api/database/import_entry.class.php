<?php
/**
 * import_entry.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\database;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * import_entry: record
 */
class import_entry extends \cenozo\database\record
{
  /**
   * Validates the entry and returns true if there are no errors found.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @return boolean
   * @access public
   */
  public function validate()
  {
    $region_class_name = lib::get_class_name( 'database\region' );
    $postcode_class_name = lib::get_class_name( 'database\postcode' );
    $participant_class_name = lib::get_class_name( 'database\participant' );
    $cohort_class_name = lib::get_class_name( 'database\cohort' );
    $phone_class_name = lib::get_class_name( 'database\phone' );
    $address_class_name = lib::get_class_name( 'database\address' );
    $language_class_name = lib::get_class_name( 'database\language' );
    
    if( 0 != preg_match( '/apt|apartment|#/i', $this->apartment ) )
      $this->apartment_error = true;

    // check that the address is valid
    if( is_null( $this->street ) && is_null( $this->address_other ) )
      $this->address_error = true;

    // check that the province exists and is in Canada
    $db_region = $region_class_name::get_unique_record( 'abbreviation', $this->province );
    if( is_null( $db_region ) || 'Canada' != $db_region->country ) $this->province_error = true;
    else
    {
      // check that the postal code is valid
      $db_postcode = $postcode_class_name::get_match( $this->postcode );
      if( is_null( $db_postcode ) || $db_postcode->region_id != $db_region->id )
        $this->postcode_error = true;
    }

    if( !is_null( $this->home_phone ) && !util::validate_phone_number( $this->home_phone ) )
      $this->home_phone_error = true;

    if( !is_null( $this->mobile_phone ) && !util::validate_phone_number( $this->mobile_phone ) )
      $this->mobile_phone_error = true;

    // look for duplicate participants (same number and cohort)
    $phone_mod = lib::create( 'database\modifier' );
    if( !is_null( $this->home_phone ) )
      $phone_mod->where( 'number', '=', $this->home_phone );
    if( !is_null( $this->mobile_phone ) )
      $phone_mod->or_where( 'number', '=', $this->mobile_phone );
    foreach( $phone_class_name::select( $phone_mod ) as $db_phone )
    {
      $db_participant = $db_phone->get_participant();
      if( $db_participant && $db_participant->get_cohort()->name == $this->cohort )
      {
        $this->duplicate_participant_error = true;
        break;
      }
    }

    // other obvious checks (because you can't trust anyone...)
    $cohort_names = array();
    foreach( $cohort_class_name::select() as $db_cohort ) $cohort_names[] = $db_cohort->name;
    if( 0 == preg_match( '/^male|female$/', $this->gender ) ) $this->gender_error = true;
    if( !is_null( $this->language ) &&
        is_null( $language_class_name::get_unique_record( 'code', $this->language ) ) )
      $this->language_error = true;
    if( !in_array( $this->cohort, $cohort_names ) ) $this->cohort_error = true;

    // look for duplicate addresses (same address and cohort)
    if( !$this->apartment_error &&
        !$this->address_error &&
        !$this->province_error &&
        !$this->postcode_error &&
        !$this->duplicate_participant_error )
    {
      // determine the address and postcode as it would appear in the address table and compare
      $address = $this->street;
      if( !is_null( $this->apartment ) ) $address = $this->apartment.' '.$address;
      $postcode = 6 == strlen( $this->postcode )
                ? sprintf( '%s %s',
                           substr( $this->postcode, 0, 3 ),
                           substr( $this->postcode, 3, 3 ) )
                : $this->postcode;

      $address_mod = lib::create( 'database\modifier' );
      $address_mod->where( 'address1', '=', $address );
      $address_mod->where( 'address2', '=', $this->address_other );
      $address_mod->where( 'city', '=', $this->city );
      $address_mod->where( 'region_id', '=', $db_region->id );
      $address_mod->where( 'postcode', '=', $postcode );
      foreach( $address_class_name::select( $address_mod ) as $db_address )
      {
        $db_participant = $db_address->get_participant();
        if( $db_participant && $db_participant->get_cohort()->name == $this->cohort )
        {
          $this->duplicate_address_error = true;
          break;
        }
      }
    }

    // TODO: date_of_birth_error and date_error are no longer needed
    return !$this->apartment_error &&
           !$this->address_error &&
           !$this->province_error &&
           !$this->postcode_error &&
           !$this->home_phone_error &&
           !$this->mobile_phone_error &&
           !$this->duplicate_participant_error &&
           !$this->duplicate_address_error &&
           !$this->gender_error &&
           !$this->date_of_birth_error &&
           !$this->language_error &&
           !$this->cohort_error &&
           !$this->date_error;
  }

  /**
   * Imports the entry into the system.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function import()
  {
    // check the primary key value
    if( is_null( $this->id ) )
    {
      log::warning( 'Tried to import import_entry with no id.' );
      return;
    }
    
    $participant_class_name = lib::get_class_name( 'database\participant' );
    $age_group_class_name = lib::get_class_name( 'database\age_group' );
    $cohort_class_name = lib::get_class_name( 'database\cohort' );
    $application_class_name = lib::get_class_name( 'database\application' );
    $site_class_name = lib::get_class_name( 'database\site' );
    $event_type_class_name = lib::get_class_name( 'database\event_type' );
    $region_class_name = lib::get_class_name( 'database\region' );
    $language_class_name = lib::get_class_name( 'database\language' );

    // make sure there is a uid available
    $uid = $participant_class_name::get_new_uid();
    if( is_null( $uid ) ) throw lib::create( 'exception\notice',
      'There are no new UIDs available, please report this to an administrator immediately!',
      __METHOD__ );
    
    $db_language = is_null( $this->language )
                 ? NULL
                 : $language_class_name::get_unique_record( 'code', $this->language );

    // get the age group based on the date of birth
    $interval = util::get_interval( $this->date_of_birth );
    $age_group_mod = lib::create( 'database\modifier' );
    $age_group_mod->where( 'lower', '<=', $interval->y );
    $age_group_mod->where( 'upper', '>', $interval->y );
    $age_group_list = $age_group_class_name::select( $age_group_mod );
    $db_age_group = current( $age_group_list );

    // import data to the participant table
    $db_participant = lib::create( 'database\participant' );
    $db_participant->active = true;
    $db_participant->uid = $uid;
    $db_participant->source_id = $this->source_id;
    $db_cohort = $cohort_class_name::get_unique_record( 'name', $this->cohort );
    $db_participant->cohort_id = $db_cohort->id;
    $db_participant->first_name = $this->first_name;
    $db_participant->last_name = $this->last_name;
    $db_participant->gender = $this->gender;
    $db_participant->date_of_birth = $this->date_of_birth;
    if( $db_age_group ) $db_participant->age_group_id = $db_age_group->id;
    $db_participant->language_id = is_null( $db_language ) ? NULL : $db_language->id;
    $db_participant->low_education = true == $this->low_education;
    $db_participant->email = $this->email;
    $db_participant->save();

    // add the imported event to the participant
    $db_event_type = $event_type_class_name::get_unique_record( 'name', 'imported' );
    if( !is_null( $db_event_type ) )
    {
      $db_event = lib::create( 'database\event' );
      $db_event->participant_id = $db_participant->id;
      $db_event->event_type_id = $db_event_type->id;
      $db_event->datetime = $this->date;
      $db_event->save();
    }
    
    // import data to the address table
    $address = $this->street;
    if( !is_null( $this->apartment ) ) $address = $this->apartment.' '.$address;

    $db_region = $region_class_name::get_unique_record( 'abbreviation', $this->province );
    $db_address = lib::create( 'database\address' );
    $db_address->participant_id = $db_participant->id;
    $db_address->active = true;
    $db_address->rank = 1;
    $db_address->address1 = $address;
    $db_address->address2 = $this->address_other;
    $db_address->city = $this->city;
    $db_address->region_id = $db_region->id;
    $postcode = 6 == strlen( $this->postcode )
              ? sprintf( '%s %s',
                         substr( $this->postcode, 0, 3 ),
                         substr( $this->postcode, 3, 3 ) )
              : $this->postcode;
    $db_address->postcode = $postcode;
    $db_address->source_postcode();
    $db_address->save();

    // import data to the phone table
    $db_home_phone = NULL;
    $db_mobile_phone = NULL;
    $rank = 1;
    if( !is_null( $this->home_phone ) )
    {
      $db_home_phone = lib::create( 'database\phone' );
      $db_home_phone->participant_id = $db_participant->id;
      $db_home_phone->address_id = $db_address->id;
      $db_home_phone->active = true;
      $db_home_phone->rank = $rank;
      $db_home_phone->type = 'home';
      $db_home_phone->number = $this->home_phone;
      $db_home_phone->save();
      $rank++;
    }
    if( !is_null( $this->mobile_phone ) )
    {
      $db_mobile_phone = lib::create( 'database\phone' );
      $db_mobile_phone->participant_id = $db_participant->id;
      $db_mobile_phone->active = true;
      $db_mobile_phone->rank = $rank;
      $db_mobile_phone->type = 'mobile';
      $db_mobile_phone->number = $this->mobile_phone;
      $db_mobile_phone->save();
    }

    // if mobile is favoured over home then rearange phone ranks
    if( 'mobile' == $this->phone_preference &&
        !is_null( $db_home_phone ) && !is_null( $db_mobile_phone ) )
    {
      $db_home_phone->rank = 0;
      $db_home_phone->save();
      $db_mobile_phone->rank = 1;
      $db_mobile_phone->save();
      $db_home_phone->rank = 2;
      $db_home_phone->save();
    }

    // import data to the note table
    if( !is_null( $this->note ) )
    {
      $db_note = lib::create( 'database\note' );
      $db_note->participant_id = $db_participant->id;
      $db_note->user_id = lib::create( 'business\session' )->get_user()->id;
      $db_note->datetime = util::get_datetime_object();
      $db_note->note = $this->note;
      $db_note->save();
    }

    // save the new participant record to the form
    $this->participant_id = $db_participant->id;
    $this->save();
  }
}
