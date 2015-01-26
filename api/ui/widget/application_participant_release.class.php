<?php
/**
 * application_participant_release.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget application participant_release
 */
class application_participant_release extends \cenozo\ui\widget\base_participant_multi
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
    // the parent class assumes that the subject is always "participant"
    $grand_parent = get_parent_class( get_parent_class( get_class() ) );
    $grand_parent::__construct( 'application', 'participant_release', $args );
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

    $this->add_parameter( 'application_id', 'enum', 'Application' );
    $this->add_parameter( 'start_date', 'date', 'Start Date',
      'Restricts the operation to participants who were imported on or after the given date.' );
    $this->add_parameter( 'end_date', 'date', 'End Date',
      'Restricts the operation to participants who were imported on or before the given date.' );
  }

  /**
   * Sets up necessary site-based variables.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function setup()
  {
    parent::setup();

    $application_class_name = lib::get_class_name( 'database\application' );

    // define all enum values
    $application_list = array();
    $application_mod = lib::create( 'database\modifier' );
    $application_mod->where( 'id', '!=', lib::create( 'business\session' )->get_application()->id );
    $application_mod->where( 'release_based', '=', true );
    $application_mod->order( 'title' );
    foreach( $application_class_name::select( $application_mod ) as $db_application )
      $application_list[$db_application->id] = $db_application->title;

    $this->set_parameter( 'application_id', current( $application_list ), true, $application_list );
    $this->set_parameter( 'start_date', '', false );
    $this->set_parameter( 'end_date', '', false );
  }
}
