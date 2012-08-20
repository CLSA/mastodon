<?php
/**
 * quota_delete.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: quota delete
 */
class quota_delete extends \cenozo\ui\push\base_delete
{
  /**
   * Constructor.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Push arguments
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'quota', $args );
  }

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
    if( 'comprehensive' == $this->get_record()->cohort )
    {
      $this->set_machine_request_url( BEARTOOTH_URL );
      parent::send_machine_request();
    }

    if( 'tracking' == $this->get_record()->cohort )
    {
      $this->set_machine_request_url( SABRETOOTH_URL );
      parent::send_machine_request();
    }
  }
}
?>
