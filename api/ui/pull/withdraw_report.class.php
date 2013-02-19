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
    $region_class_name = lib::get_class_name( 'database\region' );
    $consent_class_name = lib::get_class_name( 'database\consent' );

    $data = array();

    $db_cohort = lib::create( 'database\cohort', $this->get_argument( 'restrict_cohort_id' ) );
    $db_source = lib::create( 'database\source', $this->get_argument( 'restrict_source_id' ) );

    // loop through all canadian provinces
    $region_mod = lib::create( 'database\modifier' );
    $region_mod->where( 'country', '=', 'canada' );
    $region_mod->order( 'abbreviation' );
    foreach( $region_class_name::select( $region_mod ) as $db_region )
    {
      $data[$db_region->abbreviation] = array();

      // loop through all months of the year
      $datetime_obj = util::get_datetime_object( '2000-01-01' );
      while( '2000' == $datetime_obj->format( 'Y' ) )
      {
        $consent_mod = lib::create( 'database\modifier' );
        if( $db_cohort->id ) $consent_mod->where( 'participant.cohort_id', '=', $db_cohort->id );
        if( $db_source->id ) $consent_mod->where( 'participant.source_id', '=', $db_source->id );
        $consent_mod->where( 'region.id', '=', $db_region->id );
        $consent_mod->where( 'MONTH( consent.date )', '=', $datetime_obj->format( 'n' ) );
        
        $data[$db_region->abbreviation][$datetime_obj->format( 'F' )] = 
          $consent_class_name::get_withdraw_count( $consent_mod );
        $datetime_obj->add( new \DateInterval( 'P1M' ) );
      }
    }

    // create the content and header arrays using the data
    $header = array( '' );
    $content = array();
    foreach( $data as $region => $subdata )
    {
      $header[] = $region;
      foreach( $subdata as $month => $value )
      {
        if( !array_key_exists( $month, $content ) ) $content[$month] = array();
        $content[$month][0] = $month;
        $content[$month][$region] = $value;
      }
    }

    $this->add_table( NULL, $header, $content );
  }
}
