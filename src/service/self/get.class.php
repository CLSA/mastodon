<?php
/**
 * get.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace mastodon\service\self;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Special service for handling the get meta-resource
 */
class get extends \cenozo\service\self\get
{
  /**
   * Override parent method since self is a meta-resource
   */
  protected function create_resource( $index )
  {
    $cohort_class_name = lib::get_class_name( 'database\cohort' );

    $resource = parent::create_resource( $index );

    // add a list of all cohorts who have participant_data
    $cohort_list = [];
    foreach( $cohort_class_name::select_objects() as $db_cohort )
      if( $db_cohort->get_participant_data_count() ) $cohort_list[] = $db_cohort->name;
    $resource['application']['participant_data_cohort_list'] = $cohort_list;

    return $resource;
  }
}
