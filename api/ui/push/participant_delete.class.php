<?php
/**
 * participant_delete.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: participant delete
 */
class participant_delete extends \cenozo\ui\push\base_delete
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
