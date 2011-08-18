<?php
/**
 * user_new_access.class.php
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
 * push: user new_access
 * 
 * @package mastodon\ui
 */
class user_new_access extends base_new_record
{
  /**
   * Constructor.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Push arguments
   * @access public
   */
  public function __construct( $args )
  {
    if( isset( $args['user'] ) )
    { // replace the argument "user" with that user's id
      $db_user = db\user::get_unique_record( 'name', $args['user'] );
      if( !$db_user ) throw exc\argument( 'user', $args['user'], __METHOD__ );
      $args['id'] = $db_user->id;
    }

    if( isset( $args['role_name_list'] ) && isset( $args['site_name_list'] ) )
    { // replace the arguments "role_name_list" and "site_name_list" with arrays containing ids
      foreach( $args['role_name_list'] as $index => $role_name )
      {
        $db_role = db\role::get_unique_record( 'name', $role_name );
        if( !$db_role ) throw exc\argument( 'role_name_list['.$index.']', $role_name, __METHOD__ );
        $args['role_id_list'][] = $db_role->id;
      }

      foreach( $args['site_name_list'] as $index => $site )
      {
        $site_mod = new db\modifier();
        $site_mod->where( 'name', '=', $site['name'] );
        $site_mod->where( 'cohort', '=', $site['cohort'] );
        $db_site = current( db\site::select( $site_mod ) );
        if( !$db_site ) throw exc\argument( 'site_name_list['.$index.']', $site, __METHOD__ );
        $args['site_id_list'][] = $db_site->id;
      }
    }

    parent::__construct( 'user', 'access', $args );
  }

  /**
   * Executes the push.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function finish()
  {
    foreach( $this->get_argument( 'role_id_list' ) as $role_id )
    {
      $this->get_record()->add_access( $this->get_argument( 'site_id_list' ), $role_id );
    }
  }
}
?>
