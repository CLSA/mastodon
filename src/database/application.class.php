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
    if( is_null( $this->id ) )
      throw lib::create( 'database\runtime',
        'Tried to release participants to application with no primary key.',
        __METHOD__ );

    if( !is_a( $participant_mod, lib::get_class_name( 'database\modifier' ) ) )
      throw lib::create( 'exception\argument', 'participant_mod', $participant_mod, __METHOD__ );
    
    $participant_sel = lib::create( 'database\select' );
    $participant_sel->from( 'participant' );
    $participant_sel->add_table_column( 'application', 'id', 'application_id' );
    $participant_sel->add_column( 'id', 'participant_id' );
    $participant_sel->add_constant( NULL, 'create_timestamp' );
    $participant_sel->add_constant( 'UTC_TIMESTAMP()', 'datetime', NULL, false );

    $participant_mod->join(
      'application_has_cohort', 'participant.cohort_id', 'application_has_cohort.cohort_id' );
    $participant_mod->join( 'application', 'application_has_cohort.application_id', 'application.id' );
    $participant_mod->where( 'application.id', '=', $this->id );
    $sub_mod = lib::create( 'database\modifier' );
    $sub_mod->where( 'participant.id', '=', 'app_has_participant.participant_id', false );
    $sub_mod->where( 'app_has_participant.application_id', '=', 'application.id', false );
    $participant_mod->join_modifier( 'application_has_participant', $sub_mod, 'left', 'app_has_participant' );
    $participant_mod->where( 'app_has_participant.datetime', '=', NULL );

    // used below
    $event_mod = clone $participant_mod;

    $sql = sprintf(
      "INSERT INTO application_has_participant( application_id, participant_id, create_timestamp, datetime )\n".
      "%s%s\n".
      'ON DUPLICATE KEY UPDATE datetime = IFNULL( application_has_participant.datetime, UTC_TIMESTAMP() )',
      $participant_sel->get_sql(),
      $participant_mod->get_sql() );

    static::db()->execute( $sql );

    $event_sel = lib::create( 'database\select' );
    $event_sel->from( 'participant' );
    $event_sel->add_column( 'id', 'participant_id' );
    $event_sel->add_constant( $this->release_event_type_id, 'event_type_id' );
    $event_sel->add_constant( 'UTC_TIMESTAMP()', 'datetime', NULL, false );

    $event_sql = sprintf(
      "INSERT IGNORE INTO event( participant_id, event_type_id, datetime )\n".
      '%s%s',
      $event_sel->get_sql(),
      $event_mod->get_sql() );

    static::db()->execute( $event_sql );
  }
}
