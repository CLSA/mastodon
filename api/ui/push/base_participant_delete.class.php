<?php
/**
 * base_delete.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Extends Cenozo's base_delete push class to facilitate participant-based records.
 */
abstract class base_participant_delete
  extends \cenozo\ui\push\base_delete
  implements base_participant_base
{
  /**
   * Sets up the machine request url before calling the parent class' setup() method
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function setup()
  {
    // only send a machine request if the participant has been synched
    $this->set_machine_request_enabled(
      !is_null( $this->db_participant_for_machine_requests ) &&
      !is_null( $this->db_participant_for_machine_requests->sync_datetime ) );

    // send the request to the participant's primary site's service
    $this->set_machine_request_url(
      !is_null( $this->db_participant_for_machine_requests ) ?
      $this->db_participant_for_machine_requests->get_primary_site()->get_service()->get_url() :
      NULL );

    parent::setup();
  }

  /**
   * Define the participant record which should be used for determining whether to
   * sync the data and with which external services.
   * In order for data to be passed to external services this method must be called
   * in the implementing class' prepare() method.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  public function set_participant_for_machine_requests( $db_participant )
  {
    $this->db_participant_for_machine_requests = $db_participant;
  }

  /**
   * The participant record used to determine whether to sync the data and with which
   * external services
   * @var database\participant
   * @access private
   */
  private $db_participant_for_machine_requests = NULL;
}
?>
