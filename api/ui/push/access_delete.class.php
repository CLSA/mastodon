<?php
/**
 * access_delete.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: access delete
 * 
 * @package mastodon\ui
 */
class access_delete extends \cenozo\ui\push\access_delete
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
   * Override the parent method to send a request to both Beartooth and Sabretooth
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function send_machine_request()
  {
    // determine which application to send the user request to
    $db_site = lib::create( 'database\site', $this->get_record()->site_id );
    $db_role = lib::create( 'database\role', $this->get_record()->role_id );

    if( 'typist' != $db_role->name )
    {
      if( 'comprehensive' == $db_site->cohort )
      {
        $this->set_machine_request_url( BEARTOOTH_URL );
        parent::send_machine_request();
      }
      else if( 'tracking' == $db_site->cohort )
      {
        $this->set_machine_request_url( SABRETOOTH_URL );
        parent::send_machine_request();
      }
    }
  }
}
?>
