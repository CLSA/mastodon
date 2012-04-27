<?php
/**
 * participant_edit.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: participant edit
 *
 * Edit a participant.
 * @package mastodon\ui
 */
class participant_edit extends base_edit
{
  /**
   * Constructor.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Push arguments
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'participant', $args );

    // only send a machine request if the participant has been synched
    $this->set_machine_request_enabled(
      !is_null( $this->get_record()->sync_datetime ) );
    $this->set_machine_request_url(
      'comprehensive' == $this->get_record()->cohort ? BEARTOOTH_URL : SABRETOOTH_URL );
  }
}
?>
