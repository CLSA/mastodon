<?php
/**
 * site_new_access.class.php
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
 * push: site new_access
 * 
 * @package mastodon\ui
 */
class site_new_access extends base_new_record
{
  /**
   * Constructor.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Push arguments
   * @access public
   */
  public function __construct( $args )
  {
    if( isset( $args['site'] ) && isset( $args['cohort'] ) )
    { // replace the argument "site", and "cohort" with that site's id
      $site_mod = new db\modifier();
      $site_mod->where( 'name', '=', $args['site'] );
      $site_mod->where( 'cohort', '=', $args['cohort'] );
      $db_site = current( db\site::select( $site_mod ) );
      if( !$db_site ) throw exc\argument( 'args', $args, __METHOD__ );
      $args['id'] = $db_site->id;
    }

    if( isset( $args['role_name_list'] ) && isset( $args['user_name_list'] ) )
    { // replace the arguments "role_name_list" and "user_name_list" with arrays containing ids
      foreach( $args['role_name_list'] as $index => $role_name )
      {
        $db_role = db\role::get_unique_record( 'name', $role_name );
        if( !$db_role ) throw exc\argument( 'role_name_list['.$index.']', $role_name, __METHOD__ );
        $args['role_id_list'][] = $db_role->id;
      }

      foreach( $args['user_name_list'] as $index => $user_name )
      {
        $db_user = db\user::get_unique_record( 'name', $user_name );
        if( !$db_user ) throw exc\argument( 'user_name_list['.$index.']', $user_name, __METHOD__ );
        $args['user_id_list'][] = $db_user->id;
      }
    }

    parent::__construct( 'site', 'access', $args );
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
      $this->get_record()->add_access( $this->get_argument( 'user_id_list' ), $role_id );
    }
  }
}
?>
