<?php
/**
 * site_new_access.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: site new_access
 * 
 * @package mastodon\ui
 */
class site_new_access extends \cenozo\ui\push\site_new_access
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
        throw lib::create( 'exception\argument', 'noid', $noid, __METHOD__ );

      $class_name = lib::get_class_name( 'database\site' );
      $db_site = $site_class_name::get_unique_record(
        array( 'name', 'cohort' ),
        array( $noid['site.name'], $noid['site.cohort'] ) );
      if( !$db_site ) throw lib::create( 'exception\argument', 'noid', $noid, __METHOD__ );
      $args['id'] = $db_site->id;

      // replace the arguments "role_name_list" and "user_name_list" with arrays containing ids
      foreach( $noid['role_name_list'] as $role_name )
      {
        $class_name = lib::get_class_name( 'database\role' );
        $db_role = $role_class_name::get_unique_record( 'name', $role_name );
        if( !$db_role ) throw lib::create( 'exception\argument', 'role_name_list', $noid['role_name_list'], __METHOD__ );
        $args['role_id_list'][] = $db_role->id;
      }

      foreach( $noid['user_name_list'] as $user_name )
      {
        $class_name = lib::get_class_name( 'database\user' );
        $db_user = $user_class_name::get_unique_record( 'name', $user_name );
        if( !$db_user ) throw lib::create( 'exception\argument', 'user_name_list', $noid['user_name_list'], __METHOD__ );
        $args['user_id_list'][] = $db_user->id;
      }
    }

    parent::__construct( $args );
  }
}
?>
