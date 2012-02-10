<?php
/**
 * quexf_manager.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\business
 * @filesource
 */

namespace mastodon\business;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Manages importing data from QUEXF
 * 
 * @package mastodon\business
 */
class quexf_manager extends \cenozo\singleton
{
  /**
   * Constructor.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function __construct()
  {
    $this->enabled = !is_null( QUEXF_PATH );

    $this->participant_count_list = array(
      'total' => array( 'title' => 'Participants queued for import',
                        'count' => NULL ),
      'comprehensive' => array( 'title' => 'Valid comprehensive participants ready for import',
                                'count' => 0 ),
      'tracking' => array( 'title' => 'Valid tracking participants ready for import',
                           'count' => 0 ),
      'data' => array( 'title' => 'Participants with missing information',
                       'count' => 0 ),
      'region' => array( 'title' => 'Participants without a valid province',
                         'count' => 0 ),
      'postcode' => array( 'title' => 'Participants without a valid postcode',
                           'count' => 0 ),
      'form' => array( 'title' => 'Participants with a missing contact form',
                       'count' => 0 ) );
  }

  /**
   * Determines if Quexf is enabled.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @return boolean
   * @access public
   */
  public function is_enabled()
  {
    return $this->enabled;
  }

  /**
   * Gets the number of participants ready for import.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function get_participant_count_list()
  {
    // always return 0 if quexf is disabled
    if( !$this->enabled ) return array();

    // generate the count if it hasn't been done yet
    if( is_null( $this->participant_count_list['total']['count'] ) )
    {
      $quexf_person_class_name = lib::get_class_name( 'database\quexf\person' );
      $region_class_name = lib::get_class_name( 'database\region' );

      $setting_manager = lib::create( 'business\setting_manager' );
      $form_path = $setting_manager->get_setting( 'quexf', 'processed_contact_path' );
  
      $modifier = lib::create( 'database\modifier' );
      $modifier->where( 'uid', '=', NULL );

      $db_address = lib::create( 'database\address' );
      
      $this->participant_count_list['total']['count'] =
        $quexf_person_class_name::count( $modifier );
      $modifier = static::get_valid_participant_modifier( $modifier );
      $person_list = $quexf_person_class_name::select( $modifier );
      $this->participant_count_list['data']['count'] =
        $this->participant_count_list['total']['count'] - count( $person_list );
      foreach( $person_list as $db_quexf_person )
      {
        $valid = true;

        $db_region = $region_class_name::get_unique_record(
          'abbreviation', $db_quexf_person->province );
        if( is_null( $db_region ) || 'Canada' != $db_region->country )
        {
          $this->participant_count_list['region']['count']++;
          $valid = false;
        }
  
        $contact_form_path = $form_path.'/'.$db_quexf_person->new_name;
        if( !is_file( $contact_form_path ) )
        {
          $this->participant_count_list['form']['count']++;
          $valid = false;
        }
  
        $db_address->address1 = $db_quexf_person->address;
        $db_address->city = $db_quexf_person->city;
        $db_address->region_id = $db_region->id;
        $db_address->postcode = $db_quexf_person->postal_code;
        if( !$db_address->is_valid() )
        {
          $this->participant_count_list['postcode']['count']++;
          $valid = false;
        }

        if( $valid )
        {
          $cohort = '05621101' == $db_quexf_person->barcode ? 'tracking' : 'comprehensive';
          $this->participant_count_list[$cohort]['count']++;
        }
      }
    }

    return $this->participant_count_list;
  }

  /**
   * Imports all valid participants, assigns them a UID from the pool and removes them from QUEXF
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function import_contact_data()
  {
    $setting_manager = lib::create( 'business\setting_manager' );
    $form_path = $setting_manager->get_setting( 'quexf', 'processed_contact_path' );
    $quexf_person_class_name = lib::get_class_name( 'database\quexf\person' );
    $participant_class_name = lib::get_class_name( 'database\participant' );
    $region_class_name = lib::get_class_name( 'database\region' );
    $source_class_name = lib::get_class_name( 'database\source' );

    // all participants are from the ministry source
    $db_source = $source_class_name::get_unique_record( 'name', 'ministry' );

    // loop through every valid participant ready for import
    $modifier = static::get_valid_participant_modifier();
    $modifier->where( 'uid', '=', NULL );
    foreach( $quexf_person_class_name::select( $modifier ) as $db_quexf_person )
    {
      // make sure the province is valid and in Canada
      $db_region = $region_class_name::get_unique_record(
        'abbreviation', $db_quexf_person->province );
      if( is_null( $db_region ) || 'Canada' != $db_region->country ) continue;

      // check that the contact form exists
      $contact_form_path = $form_path.'/'.$db_quexf_person->new_name;
      if( !is_file( $contact_form_path ) ) continue;

      // check that we know the participant's timezone offset using the postcode
      $db_address = lib::create( 'database\address' );
      $db_address->address1 = $db_quexf_person->address;
      $db_address->city = $db_quexf_person->city;
      $db_address->region_id = $db_region->id;
      $db_address->postcode = $db_quexf_person->postal_code;
      if( !$db_address->is_valid() ) continue;

      // build the time diff interval (note: date interval doesn't allow negative periods)
      $time_diff = $db_address->get_time_diff();
      if( 0 <= $time_diff )
      {
        $time_diff_interval = new \DateInterval( sprintf( 'PT%dM', round( 60 * $time_diff ) ) );
      }
      else
      {
        $time_diff_interval = new \DateInterval( sprintf( 'PT%dM', -round( 60 * $time_diff ) ) );
        $time_diff_interval->invert = true;
        log::debug( $db_quexf_person->id );
      }

      // make sure there is a uid available
      $uid = $participant_class_name::get_new_uid();
      if( is_null( $uid ) ) break;

      // create an entry into the person table
      $db_person = lib::create( 'database\person' );
      $db_person->save();

      // gather information to add to the participant table
      $cohort = '05621101' == $db_quexf_person->barcode ? 'tracking' : 'comprehensive';
      $gender = $db_quexf_person->male ? 'male' : 'female';
      $year = date( 'Y' );
      if( $db_quexf_person->a45_49 ) $dob = sprintf( '%d-01-01', $year - 47 );
      else if( $db_quexf_person->a50_54 ) $dob = sprintf( '%d-01-01', $year - 52 );
      else if( $db_quexf_person->a55_59 ) $dob = sprintf( '%d-01-01', $year - 57 );
      else if( $db_quexf_person->a60_64 ) $dob = sprintf( '%d-01-01', $year - 62 );
      else if( $db_quexf_person->a65_69 ) $dob = sprintf( '%d-01-01', $year - 67 );
      else if( $db_quexf_person->a70_74 ) $dob = sprintf( '%d-01-01', $year - 72 );
      else if( $db_quexf_person->a75_79 ) $dob = sprintf( '%d-01-01', $year - 77 );
      else if( $db_quexf_person->a80_85 ) $dob = sprintf( '%d-01-01', $year - 82 );
      else $dob = NULL;
      if( $db_quexf_person->french && !$db_quexf_person->english ) $language = 'fr';
      else if( !$db_quexf_person->french && $db_quexf_person->english ) $language = 'en';
      else $language = NULL;

      // create an entry into the participant table
      $db_participant = lib::create( 'database\participant' );
      $db_participant->person_id = $db_person->id;
      $db_participant->active = true;
      $db_participant->uid = $uid;
      $db_participant->source_id = $db_source->id;
      $db_participant->cohort = $cohort;
      $db_participant->first_name = $db_quexf_person->first_name;
      $db_participant->last_name = $db_quexf_person->last_name;
      $db_participant->gender = $gender;
      $db_participant->date_of_birth = $dob;
      $db_participant->eligible = true;
      $db_participant->status = NULL;
      $db_participant->language = $language;
      $db_participant->no_in_home = false;
      $db_participant->prior_contact_date = NULL;
      $db_participant->email = $db_quexf_person->email;
      $db_participant->save();

      // create an entry into the status table
      $db_status = lib::create( 'database\status' );
      $db_status->participant_id = $db_participant->id;
      $db_status->datetime = $db_quexf_person->date_filled;
      $db_status->event = 'consent to contact received';
      $db_status->save();

      // create an entry into the address table
      // (the address is created above during the timezone check)
      $db_address->person_id = $db_person->id;
      $db_address->active = true;
      $db_address->rank = 1;
      $db_address->save();

      // create entries into the phone table
      $rank = 1;
      if( !is_null( $db_quexf_person->home_phone ) &&
          preg_match( '/[0-9]{3}-[0-9]{3}-[0-9]{4}/', $db_quexf_person->home_phone ) )
      {
        $db_home_phone = lib::create( 'database\phone' );
        $db_home_phone->person_id = $db_person->id;
        $db_home_phone->address_id = $db_address->id;
        $db_home_phone->active = true;
        $db_home_phone->rank = $rank;
        $db_home_phone->type = 'home';
        $db_home_phone->number = $db_quexf_person->home_phone;
        $db_home_phone->save();
        $rank++;
      }
      if( !is_null( $db_quexf_person->cell_phone ) &&
          preg_match( '/[0-9]{3}-[0-9]{3}-[0-9]{4}/', $db_quexf_person->cell_phone ) )
      {
        $db_cell_phone = lib::create( 'database\phone' );
        $db_cell_phone->person_id = $db_person->id;
        $db_cell_phone->active = true;
        $db_cell_phone->rank = $rank;
        $db_cell_phone->type = 'cell';
        $db_cell_phone->number = $db_quexf_person->cell_phone;
        $db_cell_phone->save();
        $rank++;
      }

      // if mobile is favoured over home then rearange phone ranks
      if( isset( $db_home_phone ) && isset( $db_cell_phone ) &&
          $db_quexf_person->mobile > $db_quexf_person->home )
      {
        $db_home_phone->rank = 0;
        $db_home_phone->save();
        $db_cell_phone->rank = 1;
        $db_cell_phone->save();
        $db_home_phone->rank = 2;
        $db_home_phone->save();
      }

      // create entries into the availability table
      $all_days = ( 0 == $db_quexf_person->monday &&
                    0 == $db_quexf_person->tuesday &&
                    0 == $db_quexf_person->wednesday &&
                    0 == $db_quexf_person->thursday &&
                    0 == $db_quexf_person->friday &&
                    0 == $db_quexf_person->saturday ) ||
                  ( 1 == $db_quexf_person->monday &&
                    1 == $db_quexf_person->tuesday &&
                    1 == $db_quexf_person->wednesday &&
                    1 == $db_quexf_person->thursday &&
                    1 == $db_quexf_person->friday &&
                    1 == $db_quexf_person->saturday );
      $all_times = ( 0 == $db_quexf_person->a9_10 &&
                     0 == $db_quexf_person->a10_11 &&
                     0 == $db_quexf_person->a11_12 &&
                     0 == $db_quexf_person->a12_1 &&
                     0 == $db_quexf_person->a1_2 &&
                     0 == $db_quexf_person->a2_3 &&
                     0 == $db_quexf_person->a3_4 &&
                     0 == $db_quexf_person->a4_5 &&
                     0 == $db_quexf_person->a5_6 &&
                     0 == $db_quexf_person->a6_7 &&
                     0 == $db_quexf_person->a7_8 &&
                     0 == $db_quexf_person->a8_9 ) ||
                   ( 1 == $db_quexf_person->a9_10 &&
                     1 == $db_quexf_person->a10_11 &&
                     1 == $db_quexf_person->a11_12 &&
                     1 == $db_quexf_person->a12_1 &&
                     1 == $db_quexf_person->a1_2 &&
                     1 == $db_quexf_person->a2_3 &&
                     1 == $db_quexf_person->a3_4 &&
                     1 == $db_quexf_person->a4_5 &&
                     1 == $db_quexf_person->a5_6 &&
                     1 == $db_quexf_person->a6_7 &&
                     1 == $db_quexf_person->a7_8 &&
                     1 == $db_quexf_person->a8_9 );
  
      if( !$all_times )
      {
        $times = array();
        if( $db_quexf_person->a9_10 ) $times[] = 9;
        if( $db_quexf_person->a10_11 ) $times[] = 10;
        if( $db_quexf_person->a11_12 ) $times[] = 11;
        if( $db_quexf_person->a12_1 ) $times[] = 12;
        if( $db_quexf_person->a1_2 ) $times[] = 13;
        if( $db_quexf_person->a2_3 ) $times[] = 14;
        if( $db_quexf_person->a3_4 ) $times[] = 15;
        if( $db_quexf_person->a4_5 ) $times[] = 16;
        if( $db_quexf_person->a5_6 ) $times[] = 17;
        if( $db_quexf_person->a6_7 ) $times[] = 18;
        if( $db_quexf_person->a7_8 ) $times[] = 19;
        if( $db_quexf_person->a8_9 ) $times[] = 20;
  
        // find all connected times
        $time_slots = array();
        foreach( $times as $time )
        {
          $count = count( $time_slots );
          if( 0 < $count && $time == $time_slots[$count-1]['end'] + 1 )
            $time_slots[$count-1]['end'] = $time;
          else $time_slots[] = array( 'start' => $time, 'end' => $time );
        }
      }

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
        $db_availability->monday = $db_quexf_person->monday;
        $db_availability->tuesday = $db_quexf_person->tuesday;
        $db_availability->wednesday = $db_quexf_person->wednesday;
        $db_availability->thursday = $db_quexf_person->thursday;
        $db_availability->friday = $db_quexf_person->friday;
        $db_availability->saturday = $db_quexf_person->saturday;
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
          $db_availability->monday = $db_quexf_person->monday;
          $db_availability->tuesday = $db_quexf_person->tuesday;
          $db_availability->wednesday = $db_quexf_person->wednesday;
          $db_availability->thursday = $db_quexf_person->thursday;
          $db_availability->friday = $db_quexf_person->friday;
          $db_availability->saturday = $db_quexf_person->saturday;
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

      // now update the quexf person's uid and copy the contact form
      if( !copy( $contact_form_path, sprintf( '%s/%s.pdf', CONTACT_FORM_PATH, $uid ) ) )
        log::err( sprintf( 'Failed to copy contact form %s from quexf.', $contact_form_path ) );
      $db_quexf_person->uid = $db_participant->uid;
      $db_quexf_person->save();
    }
  }
  
  /**
   * Returns a modifier that restricts a quexf person select query to valid participants only.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param database\modifier $modifier A modifier to add the restrictions to.  If null this method
   *        will create and return new modifier object.
   * @access protected
   * @static
   */
  protected static function get_valid_participant_modifier( $modifier = NULL )
  {
    if( is_null( $modifier ) ) $modifier = lib::create( 'database\modifier' );
    $modifier->where( 'first_name', '!=', NULL );
    $modifier->where( 'first_name', '!=', '' );
    $modifier->where( 'last_name', '!=', NULL );
    $modifier->where( 'last_name', '!=', '' );
    $modifier->where( 'address', '!=', NULL );
    $modifier->where( 'address', '!=', '' );
    $modifier->where( 'city', '!=', NULL );
    $modifier->where( 'city', '!=', '' );
    $modifier->where( 'province', '!=', NULL );
    $modifier->where( 'province', '!=', '' );
    $modifier->where( 'postal_code', '!=', NULL );
    $modifier->where( 'postal_code', '!=', '' );
    // AND (
    $modifier->where_bracket( true );
    // (
    $modifier->where_bracket( true );
    $modifier->where( 'home_phone', '!=', NULL );
    $modifier->where( 'home_phone', 'LIKE', '___-___-____' );
    // )
    $modifier->where_bracket( false );
    // OR (
    $modifier->where_bracket( true, true );
    $modifier->where( 'cell_phone', '!=', NULL );
    $modifier->where( 'cell_phone', 'LIKE', '___-___-____' );
    // )
    $modifier->where_bracket( false );
    // )
    $modifier->where_bracket( false );
    $modifier->where( 'male + female', '=', 1 );
    $modifier->where(
      'a45_49 + a50_54 + a55_59 + a60_64 + a65_69 + a70_74 + a75_79 + a80_85', '=', 1 );
    $modifier->where( 'date_filled', '!=', NULL );
    $modifier->where( 'date_filled', '!=', '' );
    // AND (
    $modifier->where_bracket( true );
    $modifier->where( 'barcode', '=', '05621101' ); // tracking
    $modifier->or_where( 'barcode', '=', '09954401' ); // comprehensive
    // )
    $modifier->where_bracket( false );
    $modifier->where( 'new_name', '!=', NULL );
    $modifier->where( 'new_name', '!=', '' );
    return $modifier;
  }

  /**
   * Whether or not Quexf is enabled
   * @var boolean
   * @access protected
   */
  protected $enabled = false;

  /**
   * Keeps a cache of the valid participant count
   * @var integer
   * @access private
   */
  private $participant_count_list;
}
?>
