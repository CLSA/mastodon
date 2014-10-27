<?php
/**
 * service_participant_release.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget service participant_release
 */
class service_participant_release extends \cenozo\ui\widget\base_participant_multi
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
    $grand_parent::__construct( 'service', 'participant_release', $args );
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

    $this->add_parameter( 'service_id', 'enum', 'Service' );
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

    $service_class_name = lib::get_class_name( 'database\service' );

    // define all enum values
    $service_list = array();
    $service_mod = lib::create( 'database\modifier' );
    $service_mod->where( 'id', '!=', lib::create( 'business\session' )->get_service()->id );
    $service_mod->where( 'release_based', '=', true );
    $service_mod->order( 'title' );
    foreach( $service_class_name::select( $service_mod ) as $db_service )
      $service_list[$db_service->id] = $db_service->title;

    $this->set_parameter( 'service_id', current( $service_list ), true, $service_list );
    $this->set_parameter( 'start_date', '', false );
    $this->set_parameter( 'end_date', '', false );
  }
}
