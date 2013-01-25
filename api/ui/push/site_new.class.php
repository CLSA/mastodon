<?php
/**
 * site_new.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: site new
 * 
 * Create a new site.
 */
class site_new extends \cenozo\ui\push\site_new
{
  /**
   * Processes arguments, preparing them for the operation.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function prepare()
  {
    parent::prepare();

    $this->set_machine_request_enabled( true );
  }

  /**
   * Override the parent method to send a request to the appropriate application
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function send_machine_request()
  {
    $db_service = lib::create( 'database\service', $this->arguments['columns']['service_id'] );
    unset( $this->machine_arguments['noid']['columns']['service'] );

    if( $this->get_machine_application_name() != $db_service->name )
    {
      $this->set_machine_request_url( $db_service->get_url() );
      parent::send_machine_request();
    }
  }
}
