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
    if( array_key_exists( 'noid', $args ) )
    {
      // use the noid argument and remove it from the args input
      $noid = $args['noid'];
      unset( $args['noid'] );

      // make sure there is sufficient information
      if( !is_array( $noid ) ||
          !array_key_exists( 'site.name', $noid ) ||
          !array_key_exists( 'site.cohort', $noid ) ||
          !array_key_exists( 'role_name_list', $noid ) ||
          !is_array( $noid['role_name_list'] ) ||
          !array_key_exists( 'user_name_list', $noid ) ||
          !is_array( $noid['user_name_list'] ) )
        throw new exc\argument( 'noid', $noid, __METHOD__ );

      $db_site = db\site::get_unique_record(
        array( 'name', 'cohort' ),
        array( $noid['site.name'], $noid['site.cohort'] ) );
      if( !$db_site ) throw new exc\argument( 'noid', $noid, __METHOD__ );
      $args['id'] = $db_site->id;

      // replace the arguments "role_name_list" and "user_name_list" with arrays containing ids
      foreach( $noid['role_name_list'] as $role_name )
      {
        $db_role = db\role::get_unique_record( 'name', $role_name );
        if( !$db_role ) throw exc\argument( 'role_name_list', $noid['role_name_list'], __METHOD__ );
        $args['role_id_list'][] = $db_role->id;
      }

      foreach( $noid['user_name_list'] as $user_name )
      {
        $db_user = db\user::get_unique_record( 'name', $user_name );
        if( !$db_user ) throw exc\argument( 'user_name_list', $noid['user_name_list'], __METHOD__ );
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
