<?php
/**
 * site_delete_access.class.php
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
 * push: site delete_access
 * 
 * @package mastodon\ui
 */
class site_delete_access extends base_delete_record
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
    { // replace the argument "site", and "cohort with the site's id
      $site_mod = new db\modifier();
      $site_mod->where( 'name', '=', $args['site'] );
      $site_mod->where( 'cohort', '=', $args['cohort'] );
      $db_site = current( db\site::select( $site_mod ) );
      if( !$db_site ) throw exc\argument( 'args', $args, __METHOD__ );
      $args['id'] = $db_site->id;

      // replace the arguments role, site and cohort with an access id
      $access_mod = new db\modifier();
      $access_mod->where( 'user.name', '=', $args['user'] );
      $access_mod->where( 'role.name', '=', $args['role'] );
      $access_mod->where( 'site_id', '=', $args['id'] );
      $db_access = current( db\access::select( $access_mod ) );
      if( !$db_access ) throw exc\argument( 'args', $args, __METHOD__ );
      $args['remove_id'] = $db_access->id;
    }

    parent::__construct( 'site', 'access', $args );
  }
}
?>
