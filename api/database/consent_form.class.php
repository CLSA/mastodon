<?php
/**
 * consent_form.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\database;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * consent_form: record
 */
class consent_form extends base_form
{
  /**
   * Implements the parent's abstract import method.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param database\form_entry $db_base_form_entry The entry to be used as the valid data.
   * @access public
   */
  public function import( $db_consent_form_entry )
  {
    if( is_null( $db_consent_form_entry ) || !$db_consent_form_entry->id )
    {
      throw lib::create( 'exception\runtime',
        'Tried to import invalid consent form entry.', __METHOD__ );
    }

    $event_type_class_name = lib::get_class_name( 'database\event_type' );
    $database_class_name = lib::get_class_name( 'database\database' );
    $participant_class_name = lib::get_class_name( 'database\participant' );
    $hin_class_name = lib::get_class_name( 'database\hin' );

    $db_participant =
      $participant_class_name::get_unique_record( 'uid', $db_consent_form_entry->uid );

    // link to the form
    $this->validated_consent_form_entry_id = $db_consent_form_entry->id;

    // add the consent signed event to the participant
    $db_event_type =
      $event_type_class_name::get_unique_record( 'name', 'consent signed' );
    $db_participant->add_event(
      $db_event_type, !is_null( $db_consent_form_entry->date ) ? $db_consent_form_entry->date : $now );

    // import the data to the consent table
    $accept = $db_consent_form_entry->option_1;
    $date = !is_null( $db_consent_form_entry->date )
          ? $db_consent_form_entry->date
          : util::get_datetime_object()->format( 'Y-m-d' );

    // look for duplicates
    $db_consent = NULL;
    $consent_mod = lib::create( 'database\modifier' );
    $consent_mod->where( 'accept', '=', $accept );
    $consent_mod->where( 'written', '=', true );
    $consent_mod->where( 'date', '=', $date );
    $consent_list = $db_participant->get_consent_list( $consent_mod );
    if( 0 < count( $consent_list ) )
    { // found a duplicate, link the form to it
      $db_consent = current( $consent_list );
    }
    else
    { // no duplicate, create a new consent record
      $db_consent = lib::create( 'database\consent' );
      $db_consent->participant_id = $db_participant->id;
      $db_consent->accept = $accept;
      $db_consent->written = true;
      $db_consent->date = $date;
      $db_consent->note = 'Imported by data entry system.';
      $db_consent->save();

      // now find that new consent so we can link to its ID
      $consent_mod = lib::create( 'database\modifier' );
      $consent_mod->where( 'accept', '=', $accept );
      $consent_mod->where( 'written', '=', true );
      $consent_mod->where( 'date', '=', $date );
      $consent_list = $db_participant->get_consent_list( $consent_mod );
      if( 0 < count( $consent_list ) )
      {
        $db_consent = current( $consent_list );
      }
      else
      {
        log::warning( 'Consent entry not found after importing consent form.' );
      }
    }

    // import the data to the hin table
    $db_hin = $hin_class_name::get_unique_record( 'participant_id', $db_participant->id );
    if( is_null( $db_hin ) ) $db_hin = lib::create( 'database\hin' );
    $db_hin->access = $db_consent_form_entry->option_2;
    $db_hin->save();

    // save the new consent record to the form
    $this->complete = true;
    if( !is_null( $db_consent ) ) $this->consent_id = $db_consent->id;
    $this->save();
  }
}
