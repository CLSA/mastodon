<?php
/**
 * note_new.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Extends the parent class to send machine requests.
 */
class note_new
  extends \cenozo\ui\push\note_new
  implements base_participant_base
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

    if( 'participant' == $this->get_argument( 'category' ) )
    {
      $db_person_note = lib::create( 'database\person_note', $this->get_argument( 'id' ) );
      $this->set_participant_for_machine_requests(
        $db_person_note->get_person()->get_participant() );
    }
  }

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
      !is_null( $db_participant_for_machine_requests ) &&
      !is_null( $db_participant_for_machine_requests->sync_datetime ) );

    // send the request to the participant's primary site's service
    $this->set_machine_request_url(
      !is_null( $db_participant_for_machine_requests ) ?
      $db_participant_for_machine_requests->get_primary_site()->get_service()->get_url() : NULL );

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
