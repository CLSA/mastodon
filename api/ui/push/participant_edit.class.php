<?php
/**
 * participant_edit.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: participant edit
 *
 * Edit a participant.
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
    $this->set_machine_request_enabled(
      !is_null( $this->get_record()->sync_datetime ) );
    $this->set_machine_request_url(
      'comprehensive' == $this->get_record()->cohort ? BEARTOOTH_URL : SABRETOOTH_URL );
  }

  /**
   * Sets up the operation with any pre-execution instructions that may be necessary.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function setup()
  {
    parent::setup();

    if( $this->get_machine_request_enabled() )
    {
      $columns = $this->get_argument( 'columns', array() );

      // don't send certain information
      if( array_key_exists( 'cohort', $columns ) ||
          array_key_exists( 'no_in_home', $columns ) ||
          array_key_exists( 'use_informant', $columns ) )
        $this->set_machine_request_enabled( false );
    }
  }
}
?>
