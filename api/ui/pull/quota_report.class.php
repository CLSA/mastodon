<?php
/**
 * quota_report.class.php
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
class quota_report extends \cenozo\ui\pull\base_report
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
    parent::__construct( 'quota', $args );
  }

  /**
   * Builds the report.
   * @author Dean Inglis <inglisd@mcmaster.ca>
   * @access protected
   */
  protected function build()
  {
    $quota_class_name = lib::get_class_name( 'database\quota' );
    $region_class_name = lib::get_class_name( 'database\region' );
    $age_group_class_name = lib::get_class_name( 'database\age_group' );
    $participant_class_name = lib::get_class_name( 'database\participant' );

    $cohort = $this->get_argument( 'restrict_cohort' );
    $source_id = $this->get_argument( 'restrict_source_id' );
    $db_source = $source_id ? lib::create( 'database\source', $source_id ) : NULL;
    $restrict_start_date = $this->get_argument( 'restrict_start_date' );
    $restrict_end_date = $this->get_argument( 'restrict_end_date' );
    $start_datetime_obj = NULL;
    $end_datetime_obj = NULL;

    if( 0 < strlen( $restrict_start_date ) )
      $start_datetime_obj = util::get_datetime_object( $restrict_start_date );
    if( 0 < strlen( $restrict_end_date ) )
      $end_datetime_obj = util::get_datetime_object( $restrict_end_date );
    if( 0 < strlen( $restrict_start_date ) && 0 < strlen( $restrict_end_date ) &&
        $end_datetime_obj < $start_datetime_obj )
    {   
      $temp_datetime_obj = clone $start_datetime_obj;
      $start_datetime_obj = clone $end_datetime_obj;
      $end_datetime_obj = clone $temp_datetime_obj;
    }   

    // admin user may not actually have access to Beartooth/Sabretooth, use machine credentials
    $url = 'tracking' == $cohort ? SABRETOOTH_URL : BEARTOOTH_URL;
    $cenozo_manager = lib::create( 'business\cenozo_manager', $url );
    $cenozo_manager->use_machine_credentials( true );

    // loop through all quotas by region, age group and gender
    $quota_mod = lib::create( 'database\modifier' );
    $quota_mod->where( 'cohort', '=', $cohort );
    $quota_mod->order( 'region.name' );
    $quota_mod->order( 'age_group.lower' );
    $quota_mod->order( 'gender' );
    foreach( $quota_class_name::select( $quota_mod ) as $db_quota )
    {
      $column = 'B';

      $region_key = $region_class_name::get_unique_from_primary_key( $db_quota->region_id );

      // modifier used for the pull operations
      $pull_mod = lib::create( 'database\modifier' );
      $pull_mod->where( 'age_group.lower', '=', $db_quota->get_age_group()->lower );
      $pull_mod->where( 'gender', '=', $db_quota->gender );
      if( !is_null( $start_datetime_obj ) )
        $pull_mod->where( 'DATE( participant.create_timestamp )', '>=',
          $start_datetime_obj->format( 'Y-m-d' ) );
      if( !is_null( $end_datetime_obj ) )
        $pull_mod->where( 'DATE( participant.create_timestamp )', '<=',
          $end_datetime_obj->format( 'Y-m-d' ) );
      if( !is_null( $db_source ) ) $pull_mod->where( 'source_id', '=', $db_source->id );

      // pre-recruit (total participants)
      $participant_mod = lib::create( 'database\modifier' );
      $participant_mod->where( 'address.region_id', '=', $db_quota->region_id );
      $participant_mod->where( 'age_group_id', '=', $db_quota->age_group_id );
      $participant_mod->where( 'gender', '=', $db_quota->gender );
      $participant_mod->where( 'cohort', '=', $db_quota->cohort );
      if( !is_null( $start_datetime_obj ) )
        $participant_mod->where( 'DATE( participant.create_timestamp )', '>=',
          $start_datetime_obj->format( 'Y-m-d' ) );
      if( !is_null( $end_datetime_obj ) )
        $participant_mod->where( 'DATE( participant.create_timestamp )', '<=',
          $end_datetime_obj->format( 'Y-m-d' ) );
      if( !is_null( $start_datetime_obj ) )
      {
        $participant_mod->where_bracket( true );
        $participant_mod->where( 'participant.sync_datetime', '=', NULL );
        $participant_mod->or_where( 'DATE( participant.sync_datetime )', '>=',
          $start_datetime_obj->format( 'Y-m-d' ) );
        $participant_mod->where_bracket( false );
      }
      if( !is_null( $end_datetime_obj ) )
      {
        $participant_mod->where_bracket( true );
        $participant_mod->where( 'participant.sync_datetime', '=', NULL );
        $participant_mod->or_where( 'DATE( participant.sync_datetime )', '<=',
          $end_datetime_obj->format( 'Y-m-d' ) );
        $participant_mod->where_bracket( false );
      }
      if( !is_null( $db_source ) ) $participant_mod->where( 'source_id', '=', $db_source->id );
      $this->population_data
        [$db_quota->region_id][$db_quota->age_group_id][$column][$db_quota->gender] =
          intval( $participant_class_name::count( $participant_mod ) );
      $column++;

      // open for access (synched participants)
      $participant_mod = lib::create( 'database\modifier' );
      $participant_mod->where( 'address.region_id', '=', $db_quota->region_id );
      $participant_mod->where( 'age_group_id', '=', $db_quota->age_group_id );
      $participant_mod->where( 'gender', '=', $db_quota->gender );
      $participant_mod->where( 'cohort', '=', $db_quota->cohort );
      if( !is_null( $start_datetime_obj ) )
        $participant_mod->where( 'DATE( participant.sync_datetime )', '>=',
          $start_datetime_obj->format( 'Y-m-d' ) );
      if( !is_null( $end_datetime_obj ) )
        $participant_mod->where( 'DATE( participant.sync_datetime )', '<=',
          $end_datetime_obj->format( 'Y-m-d' ) );
      if( !is_null( $db_source ) ) $participant_mod->where( 'source_id', '=', $db_source->id );
      $participant_mod->where( 'sync_datetime', '!=', NULL );
      $this->population_data
        [$db_quota->region_id][$db_quota->age_group_id][$column][$db_quota->gender] =
          intval( $participant_class_name::count( $participant_mod ) );
      $column++;

      // contact attempted (at least one call made)
      $result = $cenozo_manager->pull( 'participant', 'list',
          array( 'count' => true,
                 'modifier' => $pull_mod,
                 'region' => $region_key,
                 'qnaire_rank' => 1, // TODO: constant needs to be made a report paramter
                 'state' => 'contacted' ) );
      $this->population_data
        [$db_quota->region_id][$db_quota->age_group_id][$column][$db_quota->gender] =
          intval( $result->data );
      $column++;

      // reached and viable
      if( 'tracking' == $cohort )
      {
        $result = $cenozo_manager->pull( 'participant', 'list',
            array( 'count' => true,
                   'modifier' => $pull_mod,
                   'region' => $region_key,
                   'qnaire_rank' => 1, // TODO: constant needs to be made a report paramter
                   'state' => 'reached' ) );
        $this->population_data
          [$db_quota->region_id][$db_quota->age_group_id][$column][$db_quota->gender] =
            intval( $result->data );
        $column++;
      }

      // with appointment
      $result = $cenozo_manager->pull( 'participant', 'list',
          array( 'count' => true,
                 'modifier' => $pull_mod,
                 'region' => $region_key,
                 'qnaire_rank' => 1, // TODO: constant needs to be made a report paramter
                 'state' => 'appointment' ) );
      $this->population_data
        [$db_quota->region_id][$db_quota->age_group_id][$column][$db_quota->gender] =
          intval( $result->data );
      $column++;

      // interview complete
      $result = $cenozo_manager->pull( 'participant', 'list',
          array( 'count' => true,
                 'modifier' => $pull_mod,
                 'region' => $region_key,
                 'qnaire_rank' => 1, // TODO: constant needs to be made a report paramter
                 'state' => 'completed' ) );
      $this->population_data
        [$db_quota->region_id][$db_quota->age_group_id][$column][$db_quota->gender] =
          intval( $result->data );
      $column++;

      if( 'comprehensive' == $cohort )
      {
        // interview complete (comprehensive site interview)
       $result = $cenozo_manager->pull( 'participant', 'list',
            array( 'count' => true,
                   'modifier' => $pull_mod,
                   'region' => $region_key,
                   'qnaire_rank' => 2, // TODO: constant needs to be made a report paramter
                   'state' => 'completed' ) );
        $this->population_data
          [$db_quota->region_id][$db_quota->age_group_id][$column][$db_quota->gender] =
            intval( $result->data );
        $column++;
      }

      // with consent
      $result = $cenozo_manager->pull( 'participant', 'list',
          array( 'count' => true,
                 'modifier' => $pull_mod,
                 'region' => $region_key,
                 'qnaire_rank' => 1, // TODO: constant needs to be made a report paramter
                 'state' => 'consented' ) );
      $this->population_data
        [$db_quota->region_id][$db_quota->age_group_id][$column][$db_quota->gender] =
          intval( $result->data );
      $column++;

      // grab the quota data itself
      $this->population_data
        [$db_quota->region_id][$db_quota->age_group_id][$column][$db_quota->gender] =
          intval( $db_quota->population );
      $column++;
    }
  }

  /**
   * This method creates the report based on work done in the build() method.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function generate()
  {
    // replace the parent method (parent generate() method isn't called on purpose)

    // need to modify the report for the comprehensive cohort
    $cohort = $this->get_argument( 'restrict_cohort' );
    if( 'comprehensive' == $cohort )
    {
      // column headings
      $this->report->set_size( 11 );
      $this->report->set_bold( true );
      $this->report->set_horizontal_alignment( 'center' );
      $this->report->set_cell( 'E4', 'With Appoint', false );
      $this->report->set_cell( 'F4', 'Home Interview', false );
      $this->report->set_cell( 'G4', 'Site Interview', false );

      // heading descriptions title
      $this->report->set_size( 10 );
      $this->report->set_horizontal_alignment( 'left' );
      $this->report->set_cell( 'A118', 'With Appoint', false );
      $this->report->set_cell( 'A119', 'Home Interview', false );
      $this->report->set_cell( 'A120', 'Site Interview', false );

      // heading descriptions text
      $this->report->set_bold( false );
      $this->report->set_cell( 'B118',
        'Participants included in "Open for Access" who have an appointment booked.' );
      $this->report->set_cell( 'B119',
        'Participants included in "With Appoint" who have completed the baseline home interview.' );
      $this->report->set_cell( 'B120',
        'Participants included in "With Appoint" who have completed the baseline site interview.' );

      // change equation in column L
      $this->report->set_horizontal_alignment( 'center' );
      $top_row_list = array( 6, 16, 26, 36, 46, 56, 66, 76, 86 );
      foreach( $top_row_list as $top_row )
      {
        for( $offset = 0; $offset < 8; $offset++ )
        {
          $row = $top_row + $offset;
          $this->report->set_cell( 'L'.$row, sprintf( '=I%s-E%s', $row, $row ), false );
        }
      }

      // remove NB and PE
      $this->report->remove_row( 85, 10 );
      $this->report->remove_row( 45, 10 );

      // fix formulas broken by the above remove_row calls (PHPExcel bug)
      for( $col = 'B'; $col <= 'I'; $col++ )
      {
        for( $row = 6; $row <= 13; $row++ )
        {
          $eq = sprintf( '=SUM(%s%s,%s%s,%s%s,%s%s,%s%s,%s%s,%s%s,%s%s)',
                         $col, $row + 10,
                         $col, $row + 20,
                         $col, $row + 30,
                         $col, $row + 40,
                         $col, $row + 50,
                         $col, $row + 60,
                         $col, $row + 70,
                         $col, $row + 80 );
          $this->report->set_cell( $col.$row, $eq );
        }
      }
    }

    // the initial row is predefined by the report template
    $row = 16;

    foreach( $this->population_data as $region_data )
    {
      foreach( $region_data as $key => $age_data )
      {
        foreach( $age_data as $column => $gender_data )
        {
          $this->report->set_cell( $column.$row, $gender_data['male'], false );
          $this->report->set_cell( $column.( $row + 1 ), $gender_data['female'], false );
        }
        $row += 2; // jump to the next age block
      }
      $row += 2; // jump to the next region block
    }
    
    // set the titles
    $source_id = $this->get_argument( 'restrict_source_id' );
    $source = $source_id ? lib::create( 'database\source', $source_id )->name : 'all sources';
    $this->report->set_size( 16 );
    $this->report->set_bold( true );
    $this->report->set_horizontal_alignment( 'center' );
    $this->report->merge_cells( 'A1:M1' );
    $this->report->set_cell( 'A1', 'Quota Report for '.ucwords( $source ) );

    $now_datetime_obj = util::get_datetime_object();
    $this->report->merge_cells( 'A2:M2' );
    $this->report->set_cell( 'A2',
      sprintf( 'Generated on %s at %s',
               $now_datetime_obj->format( 'Y-m-d' ),
               $now_datetime_obj->format( 'H:i T' ) ) );

    $restrict_start_date = $this->get_argument( 'restrict_start_date' );
    $restrict_end_date = $this->get_argument( 'restrict_end_date' );

    if( 0 < strlen( $restrict_start_date ) && is_null( $restrict_end_date ) )
      $this->report->set_cell( 'A3',
        sprintf( 'Restricted to participants imported from %s',
                 $restrict_start_date ) );

    if( is_null( $restrict_start_date ) && 0 < strlen( $restrict_end_date ) )
      $this->report->set_cell( 'A3',
        sprintf( 'Restricted to participants imported up to %s',
                 $restrict_end_date ) );

    if( 0 < strlen( $restrict_start_date ) && 0 < strlen( $restrict_end_date ) )
      $this->report->set_cell( 'A3',
        sprintf( 'Restricted to participants imported from %s to %s',
                 $restrict_start_date,
                 $restrict_end_date ) );

    $this->data = $this->report->get_file( $this->get_argument( 'format' ) );
  }

  /**
   * An internal array which holds all of the data used by the report
   * @var array
   * @access private
   */
  private $population_data;
}
?>
