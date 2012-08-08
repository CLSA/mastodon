<?php
/**
 * mailout_report.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget mailout report
 */
class mailout_report extends base_report
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
    parent::__construct( 'mailout', $args );
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

    $this->add_parameter( 'mailed_to', 'boolean', 'Mailed To' );
    $this->add_restriction( 'cohort' );
    $this->add_restriction( 'source' );
    $this->add_parameter( 'mark_mailout', 'boolean', 'Mark mailouts as complete' );
    
    $this->set_variable( 'description',
      'This report provides a list of participants with respect to whether they have had a '.
      'package mailed out to them or not.  When used in the "mailed to = no" mode the list will '.
      'include all participants who have never been mailed to (whether they have been synched or '.
      'not).  When used in the "mailed to = yes" mode the list will include all unsynched '.
      'participants who have been mailed to.  Checking the "mark mailouts" option will mark all '.
      'participants included in the report as having been mailed out on today\'s date.' );
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

    $this->set_parameter( 'mailed_to', false, true );
    $this->set_parameter( 'mark_mailout', false, true );
  }
}
?>
