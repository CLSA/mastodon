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
    $session = lib::create( 'business\session' );
    $db = $session->get_database();
    $event_type_class_name = lib::get_class_name( 'database\event_type' );
    $participant_class_name = lib::get_class_name( 'database\participant' );

    // get the report arguments
    $mailed_to = $this->get_argument( 'mailed_to' );
    $collection_id = $this->get_argument( 'restrict_collection_id' );
    $db_collection = $collection_id ? lib::create( 'database\collection', $collection_id ) : NULL;
    $cohort_id = $this->get_argument( 'restrict_cohort_id' );
    $db_cohort = $cohort_id ? lib::create( 'database\cohort', $cohort_id ) : NULL;
    $application_id = $this->get_argument( 'restrict_application_id' );
    $db_application = $application_id ? lib::create( 'database\application', $application_id ) : NULL;
    $released = $this->get_argument( 'released' );
    $source_id = $this->get_argument( 'restrict_source_id' );
    $db_source = $source_id ? lib::create( 'database\source', $source_id ) : NULL;
    $mark_mailout = $this->get_argument( 'mark_mailout' );
    $db_event_type = $event_type_class_name::get_unique_record( 'name', 'package mailed' );

    $title = 'List of all ';
    if( !is_null( $db_cohort ) )
    {
      $title .= sprintf( '%s ', $db_cohort->name );
    }
    $title .= 'participants ';
    if( !is_null( $db_collection ) )
    {
      $title .= sprintf( 'who belong to the "%s" collection', $db_collection->name );
    }
    if( !is_null( $db_application ) )
    {
      if( 0 == strcasecmp( 'either', $released ) )
      {
        $title .= sprintf( 'belonging to %s', $db_application->title );
      }
      else
      {
        $title .= 0 == strcasecmp( 'yes', $released ) ? '' : 'not ';
        $title .= sprintf( 'released to %s ', $db_application->title );
      }
    }
    if( !is_null( $db_source ) )
    {
      $title .= sprintf( 'whose source is %s and ', $db_source->name );
    }
    $title .= sprintf( 'who have %shad a package mailed to', $mailed_to ? '' : ' not' );
    $this->add_title( $title );

    $modifier = lib::create( 'database\modifier' );
    if( !is_null( $db_collection ) )
      $modifier->where( 'collection_has_participant.collection_id', '=', $db_collection->id );
    if( !is_null( $db_cohort ) )
      $modifier->where( 'participant.cohort_id', '=', $db_cohort->id );
    if( !is_null( $db_source ) )
      $modifier->where( 'participant.source_id', '=', $db_source->id );

    $sql = 'SELECT DISTINCT participant.id FROM participant ';

    // this will be used in either the following if or else blocks
    $join_mod = lib::create( 'database\modifier' );
    $join_mod->where( 'participant.id', '=', 'event.participant_id', false );
    $join_mod->where( 'event.event_type_id', '=', $db_event_type->id );

    if( $mailed_to )
    {
      $modifier->join_modifier( 'event', $join_mod );
      $modifier->order_desc( 'event.datetime' );
    }
    else // invert the query
    {
      $temp_mod = lib::create( 'database\modifier' );
      $temp_mod->join_modifier( 'event', $join_mod );
      $modifier->where( 'id', 'NOT IN', sprintf( '( %s )', $sql.$temp_mod->get_sql() ), false );
    }

    if( !is_null( $db_collection ) )
      $modifier->join( 'collection_has_participant',
        'participant.id', 'collection_has_participant.participant_id' );

    if( !is_null( $db_application ) )
    {
      $join_mod = lib::create( 'database\modifier' );
      $join_mod->where( 'application_has_cohort.cohort_id', '=', 'participant.cohort_id', false );
      $join_mod->where( 'application_has_cohort.application_id', '=', $db_application->id );
      $modifier->join_modifier( 'application_has_cohort', $join_mod );
      $join_mod = lib::create( 'database\modifier' );
      $join_mod->where( 'application_has_participant.participant_id', '=', 'participant.id', false );
      $join_mod->where( 'application_has_participant.application_id', '=', $db_application->id );
      $modifier->left_join_modifier( 'application_has_participant', $join_mod );

      if( 0 == strcasecmp( 'yes', $released ) )
        $modifier->where( 'application_has_participant.datetime', '!=', NULL );
      else if( 0 == strcasecmp( 'no', $released ) )
        $modifier->where( 'application_has_participant.datetime', '=', NULL );
    }

    $sql .= $modifier->get_sql();

    $contents = array();
    $participant_id_list = $participant_class_name::db()->get_col( $sql );
    foreach( $participant_id_list as $participant_id )
    {
      $db_participant = lib::create( 'database\participant', $participant_id );
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

      $high_school = '';
      $post_secondary = '';
      $db_contact_form = $db_participant->get_contact_form();
      if( !is_null( $db_contact_form ) &&
          !is_null( $db_contact_form->validated_contact_form_entry_id ) )
      {
        $db_contact_form_entry =
          lib::create( 'database\contact_form_entry',
                       $db_contact_form->validated_contact_form_entry_id );
        if( !is_null( $db_contact_form_entry->high_school ) )
          $high_school = $db_contact_form_entry->high_school ? 'yes' : 'no';
        if( !is_null( $db_contact_form_entry->post_secondary ) )
          $post_secondary = $db_contact_form_entry->post_secondary ? 'yes' : 'no';
      }

      // get default language if participant doesn't have a preference
      $db_language = $db_participant->get_language();
      if( is_null( $db_language ) ) $db_language = $session->get_application()->get_language();
      $row = array(
        $db_language->code,
        $db_participant->uid,
        $db_participant->first_name,
        $db_participant->last_name,
        $address,
        $db_address->city,
        $db_region->name,
        $db_address->postcode,
        $age,
        $high_school,
        $post_secondary,
        $db_participant->low_education ? 'yes' : 'no' );
      
      if( $mailed_to )
      { // include the mailout date and site columns
        if( !is_null( $db_application ) )
        {
          $db_site = $db_participant->get_effective_site( $db_application );
          $site_name = is_null( $db_site ) ? 'None' : $db_site->name;
          array_unshift( $row, $site_name );
        }

        $event_datetime_list = $db_participant->get_event_datetime_list( $db_event_type );
        array_unshift( $row, strstr( end( $event_datetime_list ), ' ', true ) );
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
      'Age',
      'High School',
      'Post Secondary',
      'Low Education' );
    
    if( $mailed_to )
    { // include the mailout date and site columns
      if( !is_null( $db_application ) ) array_unshift( $header, 'Site' );
      array_unshift( $header, 'Mailout Date' );
    }

    $this->add_table( NULL, $header, $contents, NULL );
  }
}
