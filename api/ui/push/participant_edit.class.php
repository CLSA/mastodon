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
class participant_edit extends \cenozo\ui\push\base_edit
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

  /**
   * Overrides the parent method to prevent some columns from being sent in machine requests
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function finish()
  {
    // don't send information 
    $columns = $this->get_argument( 'columns', array() );
    if( array_key_exists( 'cohort', $columns ) ||
        array_key_exists( 'gender', $columns ) ||
        array_key_exists( 'date_of_birth', $columns ) ||
        array_key_exists( 'eligible', $columns ) ||
        array_key_exists( 'no_in_home', $columns ) ||
        array_key_exists( 'use_informant', $columns ) ||
        array_key_exists( 'email', $columns ) )
      $this->set_machine_request_enabled( false );

    parent::finish();
  }
}
?>
