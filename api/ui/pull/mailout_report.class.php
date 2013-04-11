<?php
/**
 * mailout_report.class.php
 * 
 * @author Dean Inglis <inglisd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\pull;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Mailout required report data.
 * 
 * @abstract
 */
class mailout_report extends \cenozo\ui\pull\base_report
{
  /**
   * Constructor
   * 
   * @author Dean Inglis <inglisd@mcmaster.ca>
   * @param array $args Pull arguments.
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'mailout', $args );
  }

  /**
   * Builds the report.
   * @author Dean Inglis <inglisd@mcmaster.ca>
   * @access protected
   */
  protected function build()
  {
    $participant_class_name = lib::get_class_name( 'database\participant' );
    $event_type_class_name = lib::get_class_name( 'database\event_type' );

    // get the report arguments
    $mailed_to = $this->get_argument( 'mailed_to' );
    $db_cohort = lib::create( 'database\cohort', $this->get_argument( 'restrict_cohort_id' ) );
    $source_id = $this->get_argument( 'restrict_source_id' );
    $db_source = $source_id ? lib::create( 'database\source', $source_id ) : NULL;
    $mark_mailout = $this->get_argument( 'mark_mailout' );
    $db_event_type = $event_type_class_name::get_unique_record( 'name', 'package mailed' );

    if( is_null( $db_source ) )
    {
      $this->add_title( 
        sprintf( $mailed_to ?
                 'List of all unsynched %s participants who have been mailed to.' :
                 'List of all %s participants who require a package mailed out.',
                 $db_cohort->name ) );
    }
    else
    {
      $this->add_title( 
        sprintf( $mailed_to ?
                 'List of all unsynched %s participants whose source is %s who have been mailed to.' :
                 'List of all %s participants whose source is %s and require a package mailed out.',
                 $db_cohort->name,
                 $db_source->name ) );
    }
    
    // modifiers common to each iteration of the following loops
    $participant_mod = lib::create( 'database\modifier' );
    if( $mailed_to )
    {
      $participant_mod->order_desc( 'event.datetime' );
      $participant_mod->where( 'sync_datetime', '=', NULL );
    }
    $participant_mod->where( 'cohort_id', '=', $db_cohort->id );
    if( !is_null( $db_source ) ) $participant_mod->where( 'source_id', '=', $db_source->id );

    $contents = array();
    $participant_list =
      $participant_class_name::select_for_event( $db_event_type, $mailed_to, $participant_mod );
    foreach( $participant_list as $db_participant )
    {
      $db_address = $db_participant->get_first_address();
      if( is_null( $db_address ) ) continue;
      $db_region = $db_address->get_region();

      $address = $db_address->address1;
      if( !is_null( $db_address->address2 ) ) $address .= ' '.$db_address->address2;

      $age = '';
      if( !is_null( $db_participant->date_of_birth ) )
      {
        $dob_datetime_obj = util::get_datetime_object( $db_participant->date_of_birth );
        $age = util::get_interval( $dob_datetime_obj )->y;
      }

      $row = array(
        'fr' == $db_participant->language ? 'fr' : 'en', // english if not set
        $db_participant->uid,
        $db_participant->first_name,
        $db_participant->last_name,
        $address,
        $db_address->city,
        $db_region->name,
        $db_address->postcode,
        $age );
      
      if( $mailed_to )
      { // remove the age column and include the mailout date and site columns
        $event_datetime_list = $db_participant->get_event_datetime_list( $db_event_type );
        $db_site = $db_participant->get_effective_site();
        $site_name = is_null( $db_site ) ? 'None' : $db_site->name;
        array_unshift( $row, $site_name );
        array_unshift( $row, strstr( end( $event_datetime_list ), ' ', true ) );
        array_pop( $row );
      }

      $contents[] = $row;

      // add packaged mailed event if requested to
      if( $mark_mailout )
      {
        $db_event = lib::create( 'database\event' );
        $db_event->participant_id = $db_participant->id;
        $db_event->event_type_id = $db_event_type->id;
        $db_event->datetime = util::get_datetime_object()->format( 'Y-m-d H:i:s' );
        $db_event->save();
      }
    }
    
    $header = array(
      'Language',
      'CLSA ID',
      'First Name',
      'Last Name',
      'Address',
      'City',
      'Province',
      'Postal Code',
      'Age' );
    
    if( $mailed_to )
    { // include the mailout date and site columns
      array_unshift( $header, 'Site' );
      array_unshift( $header, 'Mailout Date' );
      array_pop( $header );
    }

    $this->add_table( NULL, $header, $contents, NULL );
  }
}
