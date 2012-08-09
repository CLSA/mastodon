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

    $database_class_name = lib::get_class_name( 'database\database' );
    $participant_class_name = lib::get_class_name( 'database\participant' );

    // link to the form
    $this->validated_consent_form_entry_id = $db_consent_form_entry->id;

    // import the data to the consent table
    $db_participant =
      $participant_class_name::get_unique_record( 'uid', $db_consent_form_entry->uid );
    $event = sprintf( 'written %s', $db_consent_form_entry->option_1 ? 'accept' : 'deny' );
    $date = util::get_datetime_object()->format( 'Y-m-d' );

    // look for duplicates
    $db_consent = NULL;
    $consent_mod = lib::create( 'database\modifier' );
    $consent_mod->where( 'event', '=', $event );
    $consent_mod->where( 'date', '=', $date );
    $consent_list = $db_participant->get_consent_list( $consent_mod );
    if( 0 < count( $consent_list ) )
    { // found a duplicate, link the form to it
      $db_consent = current( $consent_list );
    }
    else
    { // no duplicate, create a new consent record
      $event = sprintf( 'written %s', $db_consent_form_entry->option_1 ? 'accept' : 'deny' );
      $columns = array( 'participant_id' => $db_participant->id,
                        'event' => $event,
                        'date' => $date,
                        'note' => 'Imported by data entry system.' );
      $args = array( 'columns' => $columns );
      $db_operation = lib::create( 'ui\push\consent_new', $args );
      $db_operation->process();

      // now find that new consent so we can link to its ID
      $consent_mod = lib::create( 'database\modifier' );
      $consent_mod->where( 'event', '=', $event );
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
    static::db()->execute( sprintf(
      'INSERT INTO hin SET uid = %s, access = %s '.
      'ON DUPLICATE KEY '.
      'UPDATE uid = VALUES( uid ), access = VALUES( access )',
      $database_class_name::format_string( $db_consent_form_entry->uid ),
      $database_class_name::format_string( $db_consent_form_entry->option_2 ) ) );

    // save the new consent record to the form
    $this->complete = true;
    if( !is_null( $db_consent ) ) $this->consent_id = $db_consent->id;
    $this->save();
  }
}
?>
