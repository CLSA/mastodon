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
    parent::__construct( 'self', 'set_role', $args );
    $this->role_name = $this->get_argument( 'role' ); // must exist
  }
  
  /**
   * Executes the push.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @throws exception\runtime
   * @access public
   */
  public function finish()
  {
    $db_role = db\role::get_unique_record( 'name', $this->role_name );
    if( NULL == $db_role )
      throw new exc\runtime(
        'Invalid role name "'.$this->role_name.'"', __METHOD__ );

    // get the first role associated with the role
    $session = bus\session::self();
    $db_site = $session->get_site();
    $modifier = new db\modifier();
    $modifier->where( 'site_id', '=', $db_site->id );
    $db_role_list = $session->get_user()->get_role_list( $modifier );
    if( !in_array( $db_role, $db_role_list ) )
      log::err( 'User has no access to role name "'.$this->role_name. '"' );

    $session::self()->set_site_and_role( $db_site, $db_role );
  }
  
  /**
   * The name of the role to set.
   * @var string
   * @access protected
   */
  protected $role_name = NULL;
}
?>
