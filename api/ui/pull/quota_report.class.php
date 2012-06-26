<?php
/**
 * quota_report.class.php
 * 
 * @author Dean Inglis <inglisd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\pull;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Mailout required report data.
 * 
 * @abstract
 * @package mastodon\ui
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
   * Sets up the operation with any pre-execution instructions that may be necessary.
   * 
   * @author Dean Inglis <inglisd@mcmaster.ca>
   * @access protected
   */
  protected function setup()
  {
    parent::setup();

    $quota_class_name = lib::get_class_name( 'database\quota' );
    $region_class_name = lib::get_class_name( 'database\region' );
    $age_group_class_name = lib::get_class_name( 'database\age_group' );
    $participant_class_name = lib::get_class_name( 'database\participant' );
    $sabretooth_manager = lib::create( 'business\cenozo_manager', SABRETOOTH_URL );

    $source_id = $this->get_argument( 'restrict_source_id' );
    $db_source = $source_id ? lib::create( 'database\source', $source_id ) : NULL;

    // loop through all quotas by region, age group and gender
    $quota_mod = lib::create( 'database\modifier' );
    $quota_mod->where( 'cohort', '=', 'tracking' );
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
      if( !is_null( $db_source ) ) $pull_mod->where( 'source_id', '=', $db_source->id );

      // pre-recruit (total participants)
      $participant_mod = lib::create( 'database\modifier' );
      $participant_mod->where( 'address.region_id', '=', $db_quota->region_id );
      $participant_mod->where( 'age_group_id', '=', $db_quota->age_group_id );
      $participant_mod->where( 'gender', '=', $db_quota->gender );
      $participant_mod->where( 'cohort', '=', $db_quota->cohort );
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
      if( !is_null( $db_source ) ) $participant_mod->where( 'source_id', '=', $db_source->id );
      $participant_mod->where( 'sync_datetime', '!=', NULL );
      $this->population_data
        [$db_quota->region_id][$db_quota->age_group_id][$column][$db_quota->gender] =
          intval( $participant_class_name::count( $participant_mod ) );
      $column++;

      // contact attempted (at least one call made)
      $result = $sabretooth_manager->pull( 'participant', 'list',
          array( 'count' => true,
                 'modifier' => $pull_mod,
                 'region' => $region_key,
                 'qnaire_rank' => 1, // TODO: constant needs to be made a report paramter
                 'contacted' => true ) );
      $this->population_data
        [$db_quota->region_id][$db_quota->age_group_id][$column][$db_quota->gender] =
          intval( $result->data );
      $column++;

      // reached and viable
      $result = $sabretooth_manager->pull( 'participant', 'list',
          array( 'count' => true,
                 'modifier' => $pull_mod,
                 'region' => $region_key,
                 'qnaire_rank' => 1, // TODO: constant needs to be made a report paramter
                 'reached' => true ) );
      $this->population_data
        [$db_quota->region_id][$db_quota->age_group_id][$column][$db_quota->gender] =
          intval( $result->data );
      $column++;

      // interview complete
      $result = $sabretooth_manager->pull( 'participant', 'list',
          array( 'count' => true,
                 'modifier' => $pull_mod,
                 'region' => $region_key,
                 'qnaire_rank' => 1, // TODO: constant needs to be made a report paramter
                 'completed' => true ) );
      $this->population_data
        [$db_quota->region_id][$db_quota->age_group_id][$column][$db_quota->gender] =
          intval( $result->data );
      $column++;

      // with consent
      $result = $sabretooth_manager->pull( 'participant', 'list',
          array( 'count' => true,
                 'modifier' => $pull_mod,
                 'region' => $region_key,
                 'qnaire_rank' => 1, // TODO: constant needs to be made a report paramter
                 'consented' => true ) );
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
   * This method executes the operation's purpose.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function execute()
  {
    // skip the parent method
    // php doesn't allow parent::parent::method() so we have to do the less safe code below
    $pull_class_name = lib::get_class_name( 'ui\pull' );
    $pull_class_name::execute();

    // the initial row is predefined by the report template
    $row = 16;

    foreach( $this->population_data as $region_data )
    {
      foreach( $region_data as $key => $age_data )
      {
        foreach( $age_data as $column => $gender_data )
        {
          log::debug( $column );
          log::debug( $gender_data );
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
