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
class service_participant_release extends \cenozo\ui\widget
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
    parent::__construct( 'service', 'participant_release', $args );
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

    // create a list of services with each of that service's sites
    $services = array();
    foreach( $service_class_name::select() as $db_service )
    {
      $service = array( 'id' => $db_service->id,
                        'name' => $db_service->name,
                        'sites' => array() );

      $site_mod = lib::create( 'database\modifier' );
      $site_mod->order( 'name' );
      foreach( $db_service->get_site_list( $site_mod ) as $db_site )
        $service['sites'][] = array( 'id' => $db_site->id, 'name' => $db_site->name );

      if( count( $service['sites'] ) ) $services[] = $service;
    }

    $this->set_variable( 'services', $services );
  }
}
