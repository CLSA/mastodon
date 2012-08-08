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
    $cohort = $this->machine_arguments['columns']['cohort'];
    unset( $this->machine_arguments['columns']['cohort'] );

    if( 'comprehensive' == $cohort && 'beartooth' != $this->get_machine_application_name() )
    {
      $this->set_machine_request_url( BEARTOOTH_URL );
      parent::send_machine_request();
    }

    if( 'tracking' == $cohort && 'sabretooth' != $this->get_machine_application_name() )
    {   
      $this->set_machine_request_url( SABRETOOTH_URL );
      parent::send_machine_request();
    }
  }
}
?>
