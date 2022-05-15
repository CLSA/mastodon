<?php
/**
 * application.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
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
    
    $participant_class_name = lib::get_class_name( 'database\participant' );

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

    // get a list of all participants who are being released (used to add to the study below)
    $idlist_select = lib::create( 'database\select' );
    $idlist_select->from( 'participant' );
    $idlist_select->add_column( 'id', 'participant_id' );

    $idlist_mod = clone $participant_mod;

    $id_list = array_reduce(
      $participant_class_name::select( $idlist_select, $participant_mod ),
      function( $id_list, $row ) { $id_list[] = $row['participant_id']; return $id_list; },
      array()
    );

    // used below
    $event_mod = clone $participant_mod;

    // make sure the participant hasn't already been exported
    $participant_mod->where( 'app_has_participant.datetime', '=', NULL );

    $sql = sprintf(
      "INSERT INTO application_has_participant( application_id, participant_id, create_timestamp, datetime )\n".
      "%s%s\n".
      'ON DUPLICATE KEY UPDATE datetime = IFNULL( application_has_participant.datetime, UTC_TIMESTAMP() )',
      $participant_sel->get_sql(),
      $participant_mod->get_sql() );

    static::db()->execute( $sql );

    // add the release event
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

    // add the participant to the application's study, if there is one
    if( 0 < count( $id_list ) )
    {
      $db_study_phase = $this->get_study_phase();
      if( !is_null( $db_study_phase ) )
      {
        $db_study = $db_study_phase->get_study();

        if( 0 < count( $id_list ) )
        {
          $db_study->add_participant( $id_list );
        }
      }
    }
  }
}
