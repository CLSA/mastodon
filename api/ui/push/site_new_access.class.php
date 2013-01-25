<?php
/**
 * site_new_access.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: site new_access
 */
class site_new_access extends \cenozo\ui\push\site_new_access
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
    // there's a chance that the role list is empty, skip if it is
    if( 0 < count( $this->machine_arguments['noid']['role_list'] ) )
    {
      $this->set_machine_request_url( $this->get_record()->get_service()->get_url() );
      parent::send_machine_request();
    }
  }
}
