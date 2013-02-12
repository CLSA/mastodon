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
   * Processes arguments, preparing them for the operation.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @throws exception\notice
   * @access protected
   */
  protected function prepare()
  {
    parent::prepare();

    // check to see if a cohort-specific template exists for this report
    $db_cohort = lib::create( 'database\cohort', $this->get_argument( 'restrict_cohort_id' ) );
    $filename = sprintf( '%s/report/%s_%s.xls',
                         DOC_PATH,
                         $this->get_full_name(),
                         $db_cohort->name );
    if( file_exists( $filename ) ) $this->report = lib::create( 'business\report', $filename );
  }

  /**
   * Builds the report.
   * @author Dean Inglis <inglisd@mcmaster.ca>
   * @access protected
   */
  protected function build()
  {
    $this->report->set_orientation( 'landscape' );

    $quota_class_name = lib::get_class_name( 'database\quota' );
    $site_class_name = lib::get_class_name( 'database\site' );
    $region_class_name = lib::get_class_name( 'database\region' );
    $age_group_class_name = lib::get_class_name( 'database\age_group' );
    $participant_class_name = lib::get_class_name( 'database\participant' );

    $db_cohort = lib::create( 'database\cohort', $this->get_argument( 'restrict_cohort_id' ) );
    $site_breakdown = 'comprehensive' == $db_cohort->name;
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
    $db_service = $db_cohort->get_service();
    $cenozo_manager = lib::create( 'business\cenozo_manager', $db_service->get_url() );
    $cenozo_manager->use_machine_credentials( true );

    // loop through all quotas by region or site (based on breakdown), age group and gender
    $quota_mod = lib::create( 'database\modifier' );
    $quota_mod->where( 'site.service_id', '=', $db_service->id );
    $quota_mod->order( $site_breakdown ? 'site.name' : 'region.name' );
    $quota_mod->order( 'age_group.lower' );
    $quota_mod->order( 'gender' );
    foreach( $quota_class_name::select( $quota_mod ) as $db_quota )
    {
      $column = 'B';

      // determine the unique key and id of the site or region (based on breakdown)
      $site_region_key = $site_breakdown
                       ? $site_class_name::get_unique_from_primary_key( $db_quota->site_id )
                       : $region_class_name::get_unique_from_primary_key( $db_quota->region_id );
      $site_region_id = $site_breakdown ? $db_quota->site_id : $db_quota->region_id;

      // modifier used for the pull operations
      $pull_mod = lib::create( 'database\modifier' );
      $pull_mod->where( 'age_group.lower', '=', $db_quota->get_age_group()->lower );
      $pull_mod->where( 'gender', '=', $db_quota->gender );
      if( !is_null( $start_datetime_obj ) )
        $pull_mod->where( 'participant.create_timestamp', '>=',
          $start_datetime_obj->format( 'Y-m-d 00:00:00' ) );
      if( !is_null( $end_datetime_obj ) )
        $pull_mod->where( 'participant.create_timestamp', '<=',
          $end_datetime_obj->format( 'Y-m-d 23:59:59' ) );
      if( !is_null( $db_source ) ) $pull_mod->where( 'source_id', '=', $db_source->id );

      // pre-recruit (total participants)
      $participant_mod = lib::create( 'database\modifier' );
      $participant_mod->where( 'cohort', '=', $cohort );
      $participant_mod->where(
        $site_breakdown ? 'participant_site.site_id' : 'address.region_id', '=', $site_region_id );
      $participant_mod->where( 'age_group_id', '=', $db_quota->age_group_id );
      $participant_mod->where( 'gender', '=', $db_quota->gender );
      if( !is_null( $start_datetime_obj ) )
        $participant_mod->where( 'participant.create_timestamp', '>=',
          $start_datetime_obj->format( 'Y-m-d 00:00:00' ) );
      if( !is_null( $end_datetime_obj ) )
        $participant_mod->where( 'participant.create_timestamp', '<=',
          $end_datetime_obj->format( 'Y-m-d 23:59:59' ) );
      if( !is_null( $start_datetime_obj ) )
      {
        $participant_mod->where_bracket( true );
        $participant_mod->where( 'participant.sync_datetime', '=', NULL );
        $participant_mod->or_where( 'participant.sync_datetime', '>=',
          $start_datetime_obj->format( 'Y-m-d 00:00:00' ) );
        $participant_mod->where_bracket( false );
      }
      if( !is_null( $end_datetime_obj ) )
      {
        $participant_mod->where_bracket( true );
        $participant_mod->where( 'participant.sync_datetime', '=', NULL );
        $participant_mod->or_where( 'participant.sync_datetime', '<=',
          $end_datetime_obj->format( 'Y-m-d 23:59:59' ) );
        $participant_mod->where_bracket( false );
      }

      if( !is_null( $db_source ) ) $participant_mod->where( 'source_id', '=', $db_source->id );
      $this->population_data
        [$site_region_id][$db_quota->age_group_id][$column][$db_quota->gender] =
          intval( $participant_class_name::count( $participant_mod ) );
      $column++;

      // open for access (synched participants)
      $participant_mod = lib::create( 'database\modifier' );
      $participant_mod->where( 'cohort', '=', $cohort );
      $participant_mod->where(
        $site_breakdown ? 'participant_site.site_id' : 'address.region_id', '=', $site_region_id );
      $participant_mod->where( 'age_group_id', '=', $db_quota->age_group_id );
      $participant_mod->where( 'gender', '=', $db_quota->gender );
      if( !is_null( $start_datetime_obj ) )
        $participant_mod->where( 'participant.sync_datetime', '>=',
          $start_datetime_obj->format( 'Y-m-d 00:00:00' ) );
      if( !is_null( $end_datetime_obj ) )
        $participant_mod->where( 'participant.sync_datetime', '<=',
          $end_datetime_obj->format( 'Y-m-d 23:59:59' ) );
      if( !is_null( $db_source ) ) $participant_mod->where( 'source_id', '=', $db_source->id );
      $participant_mod->where( 'sync_datetime', '!=', NULL );
      $this->population_data
        [$site_region_id][$db_quota->age_group_id][$column][$db_quota->gender] =
          intval( $participant_class_name::count( $participant_mod ) );
      $column++;

      // contact attempted (at least one call made)
      $result = $cenozo_manager->pull( 'participant', 'list',
          array( 'count' => true,
                 'modifier' => $pull_mod,
                 $site_breakdown ? 'site' : 'region' => $site_region_key,
                 'qnaire_rank' => 1, // quota only involves the first qnaire
                 'state' => 'contacted' ) );
      $this->population_data
        [$site_region_id][$db_quota->age_group_id][$column][$db_quota->gender] =
          intval( $result->data );
      $column++;

      // reached and viable
      if( 'sabretooth' == $db_service->name )
      {
        $result = $cenozo_manager->pull( 'participant', 'list',
            array( 'count' => true,
                   'modifier' => $pull_mod,
                   $site_breakdown ? 'site' : 'region' => $site_region_key,
                   'qnaire_rank' => 1, // quota only involves the first qnaire
                   'state' => 'reached' ) );
        $this->population_data
          [$site_region_id][$db_quota->age_group_id][$column][$db_quota->gender] =
            intval( $result->data );
        $column++;
      }

      // with appointment
      $result = $cenozo_manager->pull( 'participant', 'list',
          array( 'count' => true,
                 'modifier' => $pull_mod,
                 $site_breakdown ? 'site' : 'region' => $site_region_key,
                 'qnaire_rank' => 1, // quota only involves the first qnaire
                 'state' => 'appointment' ) );
      $this->population_data
        [$site_region_id][$db_quota->age_group_id][$column][$db_quota->gender] =
          intval( $result->data );
      $column++;

      // interview complete
      $result = $cenozo_manager->pull( 'participant', 'list',
          array( 'count' => true,
                 'modifier' => $pull_mod,
                 $site_breakdown ? 'site' : 'region' => $site_region_key,
                 'qnaire_rank' => 1, // quota only involves the first qnaire
                 'state' => 'completed' ) );
      $this->population_data
        [$site_region_id][$db_quota->age_group_id][$column][$db_quota->gender] =
          intval( $result->data );
      $column++;

      if( 'beartooth' == $db_service->name )
      {
        // interview complete (beartooth site interview)
        $result = $cenozo_manager->pull( 'participant', 'list',
            array( 'count' => true,
                   'modifier' => $pull_mod,
                   $site_breakdown ? 'site' : 'region' => $site_region_key,
                   'qnaire_rank' => 2, // comp quota also involves second qnaire
                   'state' => 'completed' ) );
        $this->population_data
          [$site_region_id][$db_quota->age_group_id][$column][$db_quota->gender] =
            intval( $result->data );
        $column++;
      }

      // with consent
      $result = $cenozo_manager->pull( 'participant', 'list',
          array( 'count' => true,
                 'modifier' => $pull_mod,
                 $site_breakdown ? 'site' : 'region' => $site_region_key,
                 'qnaire_rank' => 1, // quota only involves the first qnaire
                 'state' => 'consented' ) );
      $this->population_data
        [$site_region_id][$db_quota->age_group_id][$column][$db_quota->gender] =
          intval( $result->data );
      $column++;

      // grab the quota data itself
      $this->population_data
        [$site_region_id][$db_quota->age_group_id][$column][$db_quota->gender] =
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

    // the initial row is predefined by the report template
    $row = 16;

    foreach( $this->population_data as $site_region_data )
    {
      foreach( $site_region_data as $age_data )
      {
        foreach( $age_data as $column => $gender_data )
        {
          $this->report->set_cell( $column.$row, $gender_data['male'], false );
          $this->report->set_cell( $column.( $row + 1 ), $gender_data['female'], false );
        }
        $row += 2; // jump to the next age block
      }
      $row += 2; // jump to the next site/region block
    }
    
    // set the titles
    $db_cohort = lib::create( 'database\cohort', $this->get_argument( 'restrict_cohort_id' ) );
    $source_id = $this->get_argument( 'restrict_source_id' );
    $source = $source_id ? lib::create( 'database\source', $source_id )->name : 'all sources';
    $this->report->set_size( 16 );
    $this->report->set_bold( true );
    $this->report->set_horizontal_alignment( 'center' );
    $this->report->merge_cells( 'A1:M1' );
    $this->report->set_cell(
      'A1',
      sprintf( '%s Quota Report for %s',
               ucwords( $db_cohort->name ),
               ucwords( $source ) ),
      false );

    $now_datetime_obj = util::get_datetime_object();
    $this->report->merge_cells( 'A2:M2' );
    $this->report->set_cell( 'A2',
      sprintf( 'Generated on %s at %s',
               $now_datetime_obj->format( 'Y-m-d' ),
               $now_datetime_obj->format( 'H:i T' ) ),
      false );

    $restrict_start_date = $this->get_argument( 'restrict_start_date' );
    $restrict_end_date = $this->get_argument( 'restrict_end_date' );

    if( 0 < strlen( $restrict_start_date ) && is_null( $restrict_end_date ) )
      $this->report->set_cell( 'A3',
        sprintf( 'Restricted to participants imported from %s',
                 $restrict_start_date ),
        false );

    if( is_null( $restrict_start_date ) && 0 < strlen( $restrict_end_date ) )
      $this->report->set_cell( 'A3',
        sprintf( 'Restricted to participants imported up to %s',
                 $restrict_end_date ),
        false );

    if( 0 < strlen( $restrict_start_date ) && 0 < strlen( $restrict_end_date ) )
      $this->report->set_cell( 'A3',
        sprintf( 'Restricted to participants imported from %s to %s',
                 $restrict_start_date,
                 $restrict_end_date ),
        false );

    $this->data = $this->report->get_file( $this->get_argument( 'format' ) );
  }

  /**
   * An internal array which holds all of the data used by the report
   * @var array
   * @access private
   */
  private $population_data;
}
