<?php
/**
 * access_delete.class.php
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
 * push: access delete
 * 
 * @package mastodon\ui
 */
class access_delete extends base_delete
{
  /**
   * Constructor.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Push arguments
   * @access public
   */
  public function __construct( $args )
  {
    if( isset( $args['user'] ) && isset( $args['role'] ) &&
        isset( $args['site'] ) && isset( $args['cohort'] ) )
    { // replace the arguments user, role, site and cohort with an access id
      $access_mod = new db\modifier();
      $access_mod->where( 'user.name', '=', $args['user'] );
      $access_mod->where( 'role.name', '=', $args['role'] );
      $access_mod->where( 'site.name', '=', $args['site'] );
      $access_mod->where( 'site.cohort', '=', $args['cohort'] );
      $db_access = current( db\access::select( $access_mod ) );
      if( !$db_access ) throw exc\argument( 'args', $args, __METHOD__ );
      $args['id'] = $db_access->id;
    }

    parent::__construct( 'access', $args );
  }
}
?>
