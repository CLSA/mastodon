<?php
/**
 * consent_form.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\database
 * @filesource
 */

namespace mastodon\database;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * consent_form: record
 *
 * @package mastodon\database
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
    $db_consent = lib::create( 'database\consent' );
    $db_consent->participant_id = $db_participant->id;
    $db_consent->event =
      sprintf( 'written %s', $db_consent_form_entry->option_1 ? 'accept' : 'deny' );
    $db_consent->date = util::get_datetime_object()->format( 'Y-m-d' );
    $db_consent->note = 'Imported by data entry system.';
    $db_consent->save();

    // import the data to the hin table
    static::db()->execute( sprintf(
      'INSERT INTO hin SET uid = %s, access = %s '.
      'ON DUPLICATE KEY '.
      'UPDATE uid = VALUES( uid ), access = VALUES( access )',
      $database_class_name::format_string( $db_consent_form_entry->uid ),
      $database_class_name::format_string( $db_consent_form_entry->option_2 ) ) );

    $this->consent_id = $db_consent->id;
    $this->save();
  }
}
?>
