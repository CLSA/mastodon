<?php
/**
 * application.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\database;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * application: record
 */
class application extends \cenozo\database\application
{
  /**
   * Releases participants to the application.
   * 
   * If any of the participants have already been release they will be ignored.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $modifier A modifier identifying which participants to release
   * @access public
   */
  public function release_participants( $participant_mod )
  {
    if( !is_a( $participant_mod, lib::get_class_name( 'database\modifier' ) ) )
      throw lib::create( 'exception\argument', 'participant_mod', $participant_mod, __METHOD__ );
    
    // used below
    $event_mod = clone $participant_mod;

    $participant_sel = lib::create( 'database\select' );
    $participant_sel->from( 'participant' );
    $participant_sel->add_constant( $this->id );
    $participant_sel->add_column( 'id' );
    $participant_sel->add_constant( NULL );
    $participant_sel->add_constant( 'UTC_TIMESTAMP()', NULL, NULL, false );

    $sql = sprintf(
      "INSERT INTO application_has_participant( application_id, participant_id, create_timestamp, datetime )\n".
      "%s%s\n".
      'ON DUPLICATE KEY UPDATE datetime = IFNULL( datetime, UTC_TIMESTAMP() )',
      $participant_sel->get_sql(),
      $participant_mod->get_sql() );

    static::db()->execute( $sql );

    $event_sel = lib::create( 'database\select' );
    $event_sel->from( 'participant' );
    $event_sel->add_column( 'id' );
    $event_sel->add_constant( $this->release_event_type_id );
    $event_sel->add_constant( 'UTC_TIMESTAMP()', NULL, NULL, false );

    $event_sql = sprintf(
      "INSERT IGNORE INTO event( participant_id, event_type_id, datetime )\n".
      '%s%s',
      $event_sel->get_sql(),
      $event_mod->get_sql() );

    static::db()->execute( $event_sql );
  }
}
