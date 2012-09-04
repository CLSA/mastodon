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
class quota_report extends base_report
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

    $this->add_restriction( 'cohort' );
    $this->add_restriction( 'source' );
    $this->add_restriction( 'dates' );

    $this->set_variable( 'description',
      'This report provides a list of all age and sex quotas broken down by province.  '.
      'The date options will restrict participants based on when they were imported into '.
      'the system.' );
  }
}
?>
