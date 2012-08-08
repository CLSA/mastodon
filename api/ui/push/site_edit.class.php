<?php
/**
 * site_edit.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: site edit
 *
 * Edit a site.
 */
class site_edit extends \cenozo\ui\push\site_edit
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
    if( 'comprehensive' == $this->get_record()->cohort )
    {
      $this->set_machine_request_url( BEARTOOTH_URL );
      parent::send_machine_request();
    }
    else if( 'tracking' == $this->get_record()->cohort )
    {   
      $this->set_machine_request_url( SABRETOOTH_URL );
      parent::send_machine_request();
    }
  }
}
?>
