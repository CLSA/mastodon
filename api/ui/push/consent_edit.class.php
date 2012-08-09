<?php
/**
 * consent_edit.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: consent edit
 *
 * Edit a consent.
 */
class consent_edit extends \cenozo\ui\push\base_edit
{
  /**
   * Constructor.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Push arguments
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'consent', $args );
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

    // only send a machine request if the participant has been synched
    $db_participant = $this->get_record()->get_participant();
    $this->set_machine_request_enabled( !is_null( $db_participant->sync_datetime ) );
    $this->set_machine_request_url( !is_null( $db_participant )
         ? ( 'comprehensive' == $db_participant->cohort ? BEARTOOTH_URL : SABRETOOTH_URL )
         : NULL );
  }
}
?>
