<?php
/**
 * withdraw_mailout_report.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget withdraw_mailout report
 */
class withdraw_mailout_report extends \cenozo\ui\widget\base_report
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
    parent::__construct( 'withdraw_mailout', $args );
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

    $this->add_parameter( 'mark_mailout', 'boolean', 'Mark mailouts as complete' );
    
    $this->set_variable( 'description',
      'This report provides a list of all participants who have withdrawn and require a withdraw '.
      'letter mailed to them.  Additionally, checking the "mark mailouts" option will mark all '.
      'participants included in the report as having been mailed a withdraw letter out on '.
      'today\'s date.' );
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

    $this->set_parameter( 'mark_mailout', false, true );
  }
}
