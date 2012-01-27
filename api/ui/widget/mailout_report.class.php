<?php
/**
 * mailout_report.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget mailout report
 * 
 * @package mastodon\ui
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

    $this->add_restriction( 'cohort' );
    $this->add_restriction( 'source' );
    $this->add_parameter( 'mark_mailout', 'boolean', 'Mark mailouts as complete' );
    
    $this->set_variable( 'description',
      'This report provides a list of all participants who have not yet had a packaged mailed '.
      'to them.  Checking the "mark mailouts" option will mark all participants included in the '.
      'report as having been mailed out on today\'s date (so they will no longer be included the '.
      'next time this report is run).' );
  }

  /**
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function finish()
  {
    parent::finish();
    $this->set_parameter( 'mark_mailout', false, true );
    $this->finish_setting_parameters();
  }
}
?>
