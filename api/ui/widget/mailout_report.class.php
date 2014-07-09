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
class mailout_report extends \cenozo\ui\widget\base_report
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

    $this->add_restriction( 'collection' );
    $this->add_parameter( 'mailed_to', 'boolean', 'Mailed To' );
    $this->add_restriction( 'cohort' );
    $this->add_restriction( 'service' );
    $this->add_parameter( 'released', 'enum', 'Released' );
    $this->add_restriction( 'source' );
    $this->add_parameter( 'mark_mailout', 'boolean', 'Mark mailouts as complete' );
    
    $this->set_variable( 'description',
      'This report provides a list of participants with respect to whether they have had a '.
      'package mailed out to them or not.  The "mailed to" parameter determines whether to '.
      'include participants who have ever been mailed to or not.  The "cohort" parameter, if set '.
      'to anything other than "all", restricts the list to a particular cohort.  The "service" '.
      'parameter, if set to anything other than "all", restricts the list to participants who '.
      'belong to a particular service.  The "released" parameter restricts the list to '.
      'participants who have been released, not released (or either) to the selected service '.
      '(this parameter is ignored if service is set to "all").  The "source" parameter, if set to '.
      'anything other than "all", restricts the list to a particular source.  Finally, checking '.
      'the "mark mailouts" option will mark all participants included in the report as having '.
      'been mailed out on today\'s date.' );
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

    $released_list = array( 'Either', 'Yes', 'No' );
    $released_list = array_combine( $released_list, $released_list );

    $this->set_parameter( 'mailed_to', false, true );
    $this->set_parameter( 'released', key( $released_list ), true, $released_list );
    $this->set_parameter( 'mark_mailout', false, true );
  }
}
