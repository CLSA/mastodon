<?php
/**
 * user_delete.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: user delete
 * 
 * @package mastodon\ui
 */
class user_delete extends \cenozo\ui\push\user_delete
{
  /**
   * Constructor.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Push arguments
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( $args );
    $this->set_machine_request_enabled( true );
  }

  /**
   * Override the parent method to send a request to both Beartooth and Sabretooth
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function send_machine_request()
  {
    // send the request to both beartooth and sabretooth
    $this->set_machine_request_url( BEARTOOTH_URL );
    parent::send_machine_request();
    $this->set_machine_request_url( SABRETOOTH_URL );
    parent::send_machine_request();
  }
}
?>
