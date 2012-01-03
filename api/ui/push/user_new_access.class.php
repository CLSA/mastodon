<?php
/**
 * user_new_access.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: user new_access
 * 
 * @package mastodon\ui
 */
class user_new_access extends \cenozo\ui\push\user_new_access
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
          !array_key_exists( 'user.name', $noid ) ||
          !array_key_exists( 'role_name_list', $noid ) ||
          !is_array( $noid['role_name_list'] ) ||
          !array_key_exists( 'site_name_list', $noid ) ||
          !is_array( $noid['site_name_list'] ) )
        throw lib::create( 'exception\argument', 'noid', $noid, __METHOD__ );

      $db_user = db\user::get_unique_record( 'name', $noid['user.name'] );
      if( !$db_user ) throw lib::create( 'exception\argument', 'noid', $noid, __METHOD__ );
      $args['id'] = $db_user->id;

      // replace the arguments "role_name_list" and "site_name_list" with arrays containing ids
      foreach( $noid['role_name_list'] as $role_name )
      {
        $db_role = db\role::get_unique_record( 'name', $role_name );
        if( !$db_role ) throw exc\argument( 'role_name_list', $noid['role_name_list'], __METHOD__ );
        $args['role_id_list'][] = $db_role->id;
      }

      foreach( $noid['site_name_list'] as $site )
      {
        $db_site = db\site::get_unique_record( 
          array( 'name', 'cohort' ), 
          array( $site['name'], $site['cohort'] ) );
        if( !$db_site ) throw exc\argument( 'site_name_list', $noid['site_name_list'], __METHOD__ );
        $args['site_id_list'][] = $db_site->id;
      }
    }

    parent::__construct( $args );
  }
}
?>
