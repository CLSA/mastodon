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
    $participant_class_name = lib::get_class_name( 'database\participant' );
    $postcode_class_name = lib::get_class_name( 'database\postcode' );
    
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

    // look for duplicates
    $participant_mod = lib::create( 'database\modifier' );
    $participant_mod->where( 'first_name', '=', $this->first_name );
    $participant_mod->where( 'last_name', '=', $this->last_name );
    foreach( $participant_class_name::select( $participant_mod ) as $db_participant )
    {
      foreach( $db_participant->get_phone_list() as $db_phone )
      {
        if( !is_null( $this->home_phone ) && $this->home_phone == $db_phone->number ||
            !is_null( $this->mobile_phone ) && $this->mobile_phone == $db_phone->number )
          $this->duplicate_error = true;
      }
    }

    // other obvious checks (because you can't trust anyone...)
    if( 0 == preg_match( '/^male|female$/', $this->gender ) )
      $this->gender_error = true;
    if( !util::validate_date( $this->date_of_birth ) )
      $this->date_of_birth_error = true;
    if( 0 == preg_match( '/^en|fr$/', $this->language ) )
      $this->language_error = true;
    if( 0 == preg_match( '/^comprehensive|tracking$/', $this->cohort ) )
      $this->cohort_error = true;
    if( !util::validate_date( $this->date ) )
      $this->date_error = true;

    return !$this->apartment_error &&
           !$this->address_error &&
           !$this->province_error &&
           !$this->postcode_error &&
           !$this->home_phone_error &&
           !$this->mobile_phone_error &&
           !$this->duplicate_error &&
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
    $source_class_name = lib::get_class_name( 'database\source' );
    $age_group_class_name = lib::get_class_name( 'database\age_group' );
    $region_class_name = lib::get_class_name( 'database\region' );
    $site_class_name = lib::get_class_name( 'database\site' );

    $db_french_site = $site_class_name::get_unique_record(
      array( 'name', 'cohort' ),
      array( 'Sherbrooke', 'tracking' ) );

    // all participants are from the rdd source
    $db_source = $source_class_name::get_unique_record( 'name', 'rdd' );

    // make sure there is a uid available
    $uid = $participant_class_name::get_new_uid();
    if( is_null( $uid ) ) throw lib::create( 'exception\notice',
      'There are no new UIDs available, please report this to an administrator immediately!',
      __METHOD__ );
    
    // get the age group based on the date of birth
    $interval = util::get_interval( $this->date_of_birth );
    $age_group_mod = lib::create( 'database\modifier' );
    $age_group_mod->where( 'lower', '<=', $interval->y );
    $age_group_mod->where( 'upper', '>', $interval->y );
    $age_group_list = $age_group_class_name::select( $age_group_mod );
    $db_age_group = current( $age_group_list );

    // import data to the person and participant tables
    $db_person = lib::create( 'database\person' );
    $db_person->save();

    $db_participant = lib::create( 'database\participant' );
    $db_participant->person_id = $db_person->id;
    $db_participant->active = true;
    $db_participant->uid = $uid;
    $db_participant->source_id = $db_source->id;
    $db_participant->cohort = $this->cohort;
    $db_participant->first_name = $this->first_name;
    $db_participant->last_name = $this->last_name;
    $db_participant->gender = $this->gender;
    $db_participant->date_of_birth = $this->date_of_birth;
    if( !is_null( $db_age_group ) ) $db_participant->age_group_id = $db_age_group->id;
    $db_participant->status = NULL;
    $db_participant->language = $this->language;
    $db_participant->no_in_home = false;
    $db_participant->prior_contact_date = NULL;
    $db_participant->email = $this->email;

    // make sure that all tracking participants whose preferred language is french have
    // their preferred site set to Sherbrooke
    // TODO: this custom code needs to be made more generic
    if( 'tracking' == $db_participant->cohort &&
        0 == strcasecmp( 'fr', $db_participant->language ) )
      $db_participant->site_id = $db_french_site->id;

    $db_participant->save();

    // import data to the status table
    $db_status = lib::create( 'database\status' );
    $db_status->participant_id = $db_participant->id;
    $db_status->datetime = $this->date;
    $db_status->event = 'imported by rdd';
    $db_status->save();
    
    // import data to the address table
    $address = $this->street;
    if( !is_null( $this->apartment ) ) $address = $this->apartment.' '.$address;

    $db_region = $region_class_name::get_unique_record( 'abbreviation', $this->province );
    $db_address = lib::create( 'database\address' );
    $db_address->person_id = $db_person->id;
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
      $db_home_phone->person_id = $db_person->id;
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
      $db_mobile_phone->person_id = $db_person->id;
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

    // import data to the availability table
    $all_days = ( 0 == $this->monday &&
                  0 == $this->tuesday &&
                  0 == $this->wednesday &&
                  0 == $this->thursday &&
                  0 == $this->friday &&
                  0 == $this->saturday ) ||
                ( 1 == $this->monday &&
                  1 == $this->tuesday &&
                  1 == $this->wednesday &&
                  1 == $this->thursday &&
                  1 == $this->friday &&
                  1 == $this->saturday );
    $all_times = ( 0 == $this->time_9_10 &&
                   0 == $this->time_10_11 &&
                   0 == $this->time_11_12 &&
                   0 == $this->time_12_13 &&
                   0 == $this->time_13_14 &&
                   0 == $this->time_14_15 &&
                   0 == $this->time_15_16 &&
                   0 == $this->time_16_17 &&
                   0 == $this->time_17_18 &&
                   0 == $this->time_18_19 &&
                   0 == $this->time_19_20 &&
                   0 == $this->time_20_21 ) ||
                 ( 1 == $this->time_9_10 &&
                   1 == $this->time_10_11 &&
                   1 == $this->time_11_12 &&
                   1 == $this->time_12_13 &&
                   1 == $this->time_13_14 &&
                   1 == $this->time_14_15 &&
                   1 == $this->time_15_16 &&
                   1 == $this->time_16_17 &&
                   1 == $this->time_17_18 &&
                   1 == $this->time_18_19 &&
                   1 == $this->time_19_20 &&
                   1 == $this->time_20_21 );

    $time_slots = array();
    if( !$all_times )
    {
      $times = array();
      if( $this->time_9_10 ) $times[] = 9;
      if( $this->time_10_11 ) $times[] = 10;
      if( $this->time_11_12 ) $times[] = 11;
      if( $this->time_12_13 ) $times[] = 12;
      if( $this->time_13_14 ) $times[] = 13;
      if( $this->time_14_15 ) $times[] = 14;
      if( $this->time_15_16 ) $times[] = 15;
      if( $this->time_16_17 ) $times[] = 16;
      if( $this->time_17_18 ) $times[] = 17;
      if( $this->time_18_19 ) $times[] = 18;
      if( $this->time_19_20 ) $times[] = 19;
      if( $this->time_20_21 ) $times[] = 20;

      // find all connected times
      foreach( $times as $time )
      {
        $count = count( $time_slots );
        if( 0 < $count && $time == $time_slots[$count-1]['end'] + 1 )
          $time_slots[$count-1]['end'] = $time;
        else $time_slots[] = array( 'start' => $time, 'end' => $time );
      }
    }

    // build the time diff interval (note: date interval doesn't allow negative periods)
    $time_diff = $db_address->get_time_diff();
    $time_diff_interval = new \DateInterval(
      sprintf( 'PT%dM', ( 0 <= $time_diff ? 1 : -1 )*round( 60 * $time_diff ) ) );
    if( 0 > $time_diff ) $time_diff_interval->invert = true;

    if( $all_days && !$all_times )
    {
      foreach( $time_slots as $time_slot )
      {
        // create datetime objects and adjust for timezone
        $start_datetime_obj =
          util::get_datetime_object( sprintf( '2000-01-02 %d:00', $time_slot['start'] ) );
        $start_datetime_obj->sub( $time_diff_interval );
        $end_datetime_obj =
          util::get_datetime_object( sprintf( '2000-01-02 %d:00', $time_slot['end'] + 1 ) );
        $end_datetime_obj->sub( $time_diff_interval );

        $db_availability = lib::create( 'database\availability' );
        $db_availability->participant_id = $db_participant->id;
        $db_availability->monday = true;
        $db_availability->tuesday = true;
        $db_availability->wednesday = true;
        $db_availability->thursday = true;
        $db_availability->friday = true;
        $db_availability->saturday = true;
        $db_availability->sunday = false;
        $db_availability->start_time = $start_datetime_obj->format( 'H:i:s' );
        $db_availability->end_time = $end_datetime_obj->format( 'H:i:s' );
        $db_availability->save();
      }
    }
    else if( $all_times && !$all_days )
    {
      // create datetime objects and adjust for timezone
      $start_datetime_obj = util::get_datetime_object( '2000-01-02 9:00' );
      $start_datetime_obj->sub( $time_diff_interval );
      $end_datetime_obj = util::get_datetime_object( '2000-01-02 21:00' );
      $end_datetime_obj->sub( $time_diff_interval );

      $db_availability = lib::create( 'database\availability' );
      $db_availability->participant_id = $db_participant->id;
      $db_availability->monday = $this->monday;
      $db_availability->tuesday = $this->tuesday;
      $db_availability->wednesday = $this->wednesday;
      $db_availability->thursday = $this->thursday;
      $db_availability->friday = $this->friday;
      $db_availability->saturday = $this->saturday;
      $db_availability->sunday = false;
      $db_availability->start_time = $start_datetime_obj->format( 'H:i:s' );
      $db_availability->end_time = $end_datetime_obj->format( 'H:i:s' );
      $db_availability->save();
    }
    else if( !$all_days && !$all_times )
    {
      foreach( $time_slots as $time_slot )
      {
        // create datetime objects and adjust for timezone
        $start_datetime_obj =
          util::get_datetime_object( sprintf( '2000-01-02 %d:00', $time_slot['start'] ) );
        $start_datetime_obj->sub( $time_diff_interval );
        $end_datetime_obj =
          util::get_datetime_object( sprintf( '2000-01-02 %d:00', $time_slot['end'] + 1 ) );
        $end_datetime_obj->sub( $time_diff_interval );

        $db_availability = lib::create( 'database\availability' );
        $db_availability->participant_id = $db_participant->id;
        $db_availability->monday = $this->monday;
        $db_availability->tuesday = $this->tuesday;
        $db_availability->wednesday = $this->wednesday;
        $db_availability->thursday = $this->thursday;
        $db_availability->friday = $this->friday;
        $db_availability->saturday = $this->saturday;
        $db_availability->sunday = false;
        $db_availability->start_time = $start_datetime_obj->format( 'H:i:s' );
        $db_availability->end_time = $end_datetime_obj->format( 'H:i:s' );
        $db_availability->save();
      }
    }
    else if( $all_days && $all_times )
    {
      // do nothing, all availability is the same as having no availability entries
    }

    // save the new participant record to the form
    $this->participant_id = $db_participant->id;
    $this->save();
  }
}
?>
