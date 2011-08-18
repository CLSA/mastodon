<?php
/**
 * user_delete_access.class.php
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
 * push: user delete_access
 * 
 * @package mastodon\ui
 */
class user_delete_access extends base_delete_record
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
    { // replace the argument "user" with that user's id
      $db_user = db\user::get_unique_record( 'name', $args['user'] );
      if( !$db_user ) throw exc\argument( 'user', $args['user'], __METHOD__ );
      $args['id'] = $db_user->id;

      // replace the arguments role, site and cohort with an access id
      $access_mod = new db\modifier();
      $access_mod->where( 'user_id', '=', $db_user->id );
      $access_mod->where( 'role.name', '=', $args['role'] );
      $access_mod->where( 'site.name', '=', $args['site'] );
      $access_mod->where( 'site.cohort', '=', $args['cohort'] );
      $db_access = current( db\access::select( $access_mod ) );
      if( !$db_user ) throw exc\argument( 'args', $args, __METHOD__ );
      $args['remove_id'] = $db_access->id;
    }

    parent::__construct( 'user', 'access', $args );
  }
}
?>
