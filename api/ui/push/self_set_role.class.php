<?php
/**
 * self_set_role.class.php
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
 * push: self set_role
 * 
 * Changes the current user's role.
 * Arguments must include 'role'.
 * @package mastodon\ui
 */
class self_set_role extends \mastodon\ui\push
{
  /**
   * Constructor.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Push arguments
   * @access public
   */
  public function __construct( $args )
  {
    // if the name is provided instead of the id then fetch the role id
    if( isset( $args['name'] ) )
    {
      $db_site = db\role::get_unique_record( 'name', $args['name'] );
      if( !$db_site ) throw new exc\argument( 'name', $args['name'], __METHOD__ );
      $args['id'] = $db_site->id;
    }

    parent::__construct( 'self', 'set_role', $args );
  }
  
  /**
   * Executes the push.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @throws exception\runtime
   * @access public
   */
  public function finish()
  {
    try
    {
      $db_role = new db\role( $this->get_argument( 'id' ) );
    }
    catch( exc\runtime $e )
    {
      throw new exc\argument( 'id', $this->get_argument( 'id' ), __METHOD__, $e );
    }
    
    $session = bus\session::self();
    $session->set_site_and_role( $session->get_site(), $db_role );
  }
}
?>
