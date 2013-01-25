<?php
/**
 * cohort.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\database;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * cohort: record
 */
class cohort extends \cenozo\database\record
{
  /**
   * Returns the url for this service as defined in the local settings file
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function get_service()
  {
    // the cohort_id column in the service table is unique, so return this cohort's service
    $service_class_name = lib::get_class_name( 'database\service' );
    return $service_class_name::get_unique_record( 'cohort_id', $this->id );
  }
}
