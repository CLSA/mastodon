<?php
/**
 * phone_new.class.php
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
 * push: phone new
 *
 * Create a new phone.
 * @package mastodon\ui
 */
class phone_new extends base_new
{
  /**
   * Constructor.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Push arguments
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'phone', $args );
  }

  /**
   * Overrides the parent method to make sure the number isn't blank and is a valid number.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @throws exception\notice
   * @access public
   */
  public function finish()
  {
    // make sure the datetime column isn't blank
    $columns = $this->get_argument( 'columns' );
    if( !array_key_exists( 'number', $columns ) )
      throw new exc\notice( 'The number cannot be left blank.', __METHOD__ );

    // validate the phone number
    if( 10 != strlen( preg_replace( '/[^0-9]/', '', $columns['number'] ) ) )
      throw new exc\notice(
        'Phone numbers must have exactly 10 digits.', __METHOD__ );

    // no errors, go ahead and make the change
    parent::finish();
  }
}
?>
