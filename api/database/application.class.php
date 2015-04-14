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
   * Call parent method without restricting records by application.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param database\modifier $modifier Modifications to the selection.
   * @param boolean $count If true the total number of records instead of a list
   * @param boolean $distinct Whether to use the DISTINCT sql keyword
   * @param enum $format Whether to return an object, column data or only the record id
   * @param boolean $full Do not use, parameter ignored.
   * @access public
   * @static
   */
  public static function select(
    $modifier = NULL, $count = false, $distinct = true, $format = 0, $full = false )
  {
    return parent::select( $modifier, $count, $distinct, $format, true );
  }

  /** 
   * Call parent method without restricting records by application.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param string|array $column A column with the unique key property (or array of columns)
   * @param string|array $value The value of the column to match (or array of values)
   * @return database\record
   * @static
   * @access public
   */
  public static function get_unique_record( $column, $value, $full = false )
  {
    return parent::get_unique_record( $column, $value, true );
  }

  /**
   * Override parent method so that records are not restricted by application.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param string $record_type The type of record.
   * @param modifier $modifier A modifier to apply to the list or count.
   * @param boolean $inverted Whether to invert the count (count records NOT in the joining table).
   * @param boolean $count If true then this method returns the count instead of list of records.
   * @param boolean $distinct Whether to use the DISTINCT sql keyword
   * @param enum $format Whether to return an object, column data or only the record id
   * @return array( record ) | array( int ) | int
   * @access protected
   */
  public function get_record_list(
    $record_type,
    $modifier = NULL,
    $inverted = false,
    $count = false,
    $distinct = true,
    $format = 0 )
  {
    $grand_parent = get_parent_class( get_parent_class( get_class() ) );
    return $grand_parent::get_record_list(
      $record_type, $modifier, $inverted, $count, $distinct, $format );
  }

  /**
   * Returns the application's release event-type
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @return database\event_type
   * @access public
   */
  public function get_release_event_type()
  {
    // check the primary key value
    if( is_null( $this->id ) )
    {
      log::warning( 'Tried to get release entry_type from application with no id.' );
      return;
    }
    
    return lib::create( 'database\event_type', $this->release_event_type_id );
  }

  /**
   * Update this application's release event_type based on the application's name and title
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function update_release_event_type()
  {
    // check the primary key value
    if( is_null( $this->id ) )
    {
      log::warning( 'Tried to get update release entry_type for application with no id.' );
      return;
    }
    
    $db_release_event_type = $this->get_release_event_type();
    $db_release_event_type->name = sprintf( 'released to %s', $this->name );
    $db_release_event_type->description = sprintf( 'Released the participant to %s', $this->title );
    $db_release_event_type->save();
  }

  /**
   * Releases participants to this application according to the provided modifier or,
   * if the $get_unreleased paramter is set to true, returns a list of participants who have
   * not yet been released.
   * If no modifier is provided then all unreleased participants will be released.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param database\modifier $modifier Modifications to the selection.
   * @param boolean $get_unreleased Whether to return the unreleased participants instead of
   *                releasing them.
   * @access public
   */
  public function release_participant( $modifier = NULL, $get_unreleased = false )
  {
    $database_class_name = lib::get_class_name( 'database\database' );

    if( is_null( $modifier ) ) $modifier = lib::create( 'database\modifier' );
    $modifier->where( 'application_has_participant.datetime', '=', NULL );

    $select_sql = 'SELECT DISTINCT participant.id ';

    $insert_sql = sprintf(
      'INSERT INTO application_has_participant'.
      '( application_id, participant_id, create_timestamp, datetime ) '.
      'SELECT %s, participant.id, NULL, UTC_DATETIME() ',
      static::db()->format_string( $this->id ) );

    $event_sql = sprintf(
      'INSERT INTO event( participant_id, event_type_id, datetime ) '.
      'SELECT DISTINCT participant.id, %s, UTC_DATETIME() ',
      static::db()->format_string( $this->release_event_type_id ) );

    $table_sql = sprintf(
      'FROM participant '.
      'JOIN application_has_cohort '.
      'ON application_has_cohort.cohort_id = participant.cohort_id '.
      'AND application_has_cohort.application_id = %s '.
      'LEFT JOIN application_has_participant '.
      'ON application_has_participant.application_id = %s '.
      'AND application_has_participant.participant_id = participant.id %s',
      static::db()->format_string( $this->id ),
      static::db()->format_string( $this->id ),
      $modifier->get_sql() );

    $select_sql .= $table_sql;
    $insert_sql .= $table_sql.' ON DUPLICATE KEY UPDATE datetime = UTC_DATETIME()';
    $event_sql .= $table_sql;

    if( $get_unreleased )
    {
      $id_list = static::db()->get_col( $select_sql );
      $records = array();
      foreach( $id_list as $id ) $records[] = lib::create( 'database\participant', $id );
      return $records;
    }
    else
    {
      // add the release event to each participant
      static::db()->execute( $event_sql );

      // insert them into the application_has_participant table
      static::db()->execute( $insert_sql );
    }
  }
}
