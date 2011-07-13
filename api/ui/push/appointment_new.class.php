<?php
/**
 * appointment_new.class.php
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
 * push: appointment new
 *
 * Create a new appointment.
 * @package mastodon\ui
 */
class appointment_new extends base_new
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
   * Overrides the parent method to make sure the datetime isn't blank and that check for
   * appointment slots
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @throws exception\notice
   * @access public
   */
  public function finish()
  {
    // make sure the datetime column isn't blank
    $columns = $this->get_argument( 'columns' );
    if( !array_key_exists( 'datetime', $columns ) || 0 == strlen( $columns['datetime'] ) )
      throw new exc\notice( 'The date/time cannot be left blank.', __METHOD__ );
    
    // make sure there is a slot available for the appointment
    $columns = $this->get_argument( 'columns', array() );
    foreach( $columns as $column => $value ) $this->get_record()->$column = $value;
    if( !$this->get_record()->validate_date() )
      throw new exc\notice( 'There are no operators available during that time.', __METHOD__ );
    
    // no errors, go ahead and make the change
    parent::finish();
  }
}
?>
