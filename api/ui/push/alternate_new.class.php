<?php
/**
 * alternate_new.class.php
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
 * push: alternate new
 *
 * Create a new alternate.
 * @package mastodon\ui
 */
class alternate_new extends base_new
{
  /**
   * Constructor.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Push arguments
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'alternate', $args );
  }

  /**
   * Executes the push.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function finish()
  {
    // make sure the name and association columns aren't blank
    $columns = $this->get_argument( 'columns' );
    if( !array_key_exists( 'first_name', $columns ) || 0 == strlen( $columns['first_name'] ) )
      throw new exc\notice( 'The alternate\'s first name cannot be left blank.', __METHOD__ );
    if( !array_key_exists( 'last_name', $columns ) || 0 == strlen( $columns['last_name'] ) )
      throw new exc\notice( 'The alternate\'s last name cannot be left blank.', __METHOD__ );
    if( !array_key_exists( 'association', $columns ) || 0 == strlen( $columns['association'] ) )
      throw new exc\notice( 'The alternate\'s association cannot be left blank.', __METHOD__ );
    
    // TODO: need to create the person record and link its ID

    parent::finish();
  }
}
?>
