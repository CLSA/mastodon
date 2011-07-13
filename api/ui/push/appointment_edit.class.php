<?php
/**
 * appointment_edit.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\push;
use mastodon\log, mastodon\util;
use mastodon\business as bus;
use mastodon\database as db;
use mastodon\exception as exc;

/**
 * push: appointment edit
 *
 * Edit a appointment.
 * @package mastodon\ui
 */
class appointment_edit extends base_edit
{
  /**
   * Constructor.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Push arguments
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'appointment', $args );
  }
  
  /**
   * Overrides the parent method to check for appointment slots
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @throws exception\notice
   * @access public
   */
  public function finish()
  {
    // make sure there is a slot available for the appointment
    $columns = $this->get_argument( 'columns', array() );

    if( array_key_exists( 'datetime', $columns ) )
    {
      $this->get_record()->datetime = $columns['datetime'];
      if( !$this->get_record()->validate_date() )
        throw new exc\notice( 'There are no operators available during that time.', __METHOD__ );
    }
    
    // no errors, go ahead and make the change
    parent::finish();
  }
}
?>
