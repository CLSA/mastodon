<?php
/**
 * withdraw_report.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\pull;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Productivity report data.
 * 
 * @abstract
 */
class withdraw_report extends \cenozo\ui\pull\base_report
{
  /**
   * Constructor
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param string $subject The subject to retrieve the primary information from.
   * @param array $args Pull arguments.
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'withdraw', $args );
  }

  /**
   * Builds the report.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function build()
  {
    $participant_class_name = lib::get_class_name( 'database\participant' );

    $data = array();

    $collection_id = $this->get_argument( 'restrict_collection_id' );
    $db_collection = $collection_id ? lib::create( 'database\collection', $collection_id ) : NULL;
    $db_cohort = lib::create( 'database\cohort', $this->get_argument( 'restrict_cohort_id' ) );
    $db_source = lib::create( 'database\source', $this->get_argument( 'restrict_source_id' ) );

    // create temporary table of last consent
    $participant_class_name::db()->execute(
      'CREATE TEMPORARY TABLE temp_last_consent '.
      'SELECT * FROM participant_last_consent' );
    $participant_class_name::db()->execute(
      'ALTER TABLE temp_last_consent '.
      'ADD INDEX dk_participant_id_consent_id ( participant_id, consent_id )' );

    // create temporary table of last written consent
    $participant_class_name::db()->execute(
      'CREATE TEMPORARY TABLE temp_last_written_consent '.
      'SELECT * FROM participant_last_written_consent' );
    $participant_class_name::db()->execute(
      'ALTER TABLE temp_last_written_consent '.
      'ADD INDEX dk_participant_id ( participant_id )' );

    // create temporary table of last written consent
    $participant_class_name::db()->execute(
      'CREATE TEMPORARY TABLE temp_primary_address '.
      'SELECT * FROM participant_primary_address' );
    $participant_class_name::db()->execute(
      'ALTER TABLE temp_primary_address '.
      'ADD INDEX dk_participant_id_address_id ( participant_id, address_id )' );

    $modifier = lib::create( 'database\modifier' );
    $modifier->where( 'temp_last_written_consent.accept', '=', true );
    if( $db_collection->id )
      $modifier->where( 'collection_has_participant.collection_id', '=', $db_collection->id );
    if( $db_cohort->id ) $modifier->where( 'participant.cohort_id', '=', $db_cohort->id );
    if( $db_source->id ) $modifier->where( 'participant.source_id', '=', $db_source->id );
    $modifier->where( 'temp_last_consent.accept', '=', false );
    $modifier->group( 'region.id' );
    $modifier->group( 'MONTH( consent.date )' );

    $modifier->join(
      'temp_last_consent', 'participant.id', 'temp_last_consent.participant_id' );
    $modifier->join(
      'consent', 'temp_last_consent.consent_id', 'consent.id' );
    $modifier->join(
      'temp_last_written_consent', 'participant.id', 'temp_last_written_consent.participant_id' );
    $modifier->join(
      'temp_primary_address', 'participant.id', 'temp_primary_address.participant_id' );
    $modifier->join(
      'address', 'temp_primary_address.address_id', 'address.id' );
    $modifier->join(
      'region', 'address.region_id', 'region.id' );
    if( !is_null( $db_collection ) )
      $modifier->join(
        'collection_has_participant',
        'participant.id',
        'collection_has_participant.participant_id' );

    $sql =
      'SELECT region.name AS region, '.
             'MONTHNAME( consent.date ) AS month, '.
             'COUNT( DISTINCT participant.id ) AS total '.
      'FROM participant '.$modifier->get_sql();
    
    // start by creating the header
    $header = array( '' );
    foreach( $participant_class_name::db()->get_all( $sql ) as $row )
      if( !in_array( $row['region'], $header ) ) $header[] = $row['region'];

    // now create the content, making sure to initialize numbers as 0
    $content = array();
    foreach( $participant_class_name::db()->get_all( $sql ) as $row )
    {
      if( !array_key_exists( $row['month'], $content ) )
      {
        $content[$row['month']] = array();
        foreach( $header as $region ) $content[$row['month']][$region] = 0;
        $content[$row['month']][''] = $row['month'];
      }

      // now set the value
      $content[$row['month']][$row['region']] = $row['total'];
    }

    $this->add_table( NULL, $header, $content );
  }
}
