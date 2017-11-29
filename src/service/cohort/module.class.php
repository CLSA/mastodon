<?php
/**
 * module.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace mastodon\service\cohort;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Performs operations which effect how this module is used in a service
 */
class module extends \cenozo\service\cohort\module
{
  /**
   * Extend parent method
   */
  public function prepare_read( $select, $modifier )
  {
    parent::prepare_read( $select, $modifier );

    // remove the requirement for rows in application_has_cohort (mastodon gets all cohorts)
    $modifier->remove_where( 'application_has_cohort.application_id' );
    $modifier->remove_join( 'application_has_cohort' );
  }
}
