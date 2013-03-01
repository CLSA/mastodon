<?php
/**
 * quota_report.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget quota report
 */
class quota_report extends \cenozo\ui\widget\base_report
{
  /**
   * Constructor
   * 
   * Defines all variables which need to be set for the associated template.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args An associative array of arguments to be processed by the widget
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'quota', $args );
    $this->use_cache = true;
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

    $this->add_parameter( 'cohort_id', 'enum', 'Cohort' );
    $this->add_restriction( 'source' );
    $this->add_restriction( 'dates' );

    $this->set_variable( 'description',
      'This report provides a list of all age and sex quotas broken down by province.  '.
      'The date options will restrict participants based on when they were imported into '.
      'the system.' );
  }

  /**
   * Sets up the operation with any pre-execution instructions that may be necessary.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function setup()
  {
    parent::setup();

    $cohort_class_name = lib::get_class_name( 'database\cohort' );
    $session = lib::create( 'business\session' );

    // restrict cohort list for non-admins
    $cohort_list = 3 >= $session->get_role()->tier
                 ? $session->get_site()->get_service()->get_cohort_list()
                 : $cohort_class_name::select();
    $cohorts = array();
    foreach( $cohort_list as $db_cohort ) $cohorts[$db_cohort->id] = $db_cohort->name;
    $this->set_parameter( 'cohort_id', key( $cohorts ), true, $cohorts );
  }
}
