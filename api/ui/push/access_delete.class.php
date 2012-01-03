<?php
/**
 * access_delete.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: access delete
 * 
 * @package mastodon\ui
 */
class access_delete extends \cenozo\ui\push\access_delete
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
          !array_key_exists( 'role.name', $noid ) ||
          !array_key_exists( 'site.name', $noid ) ||
          !array_key_exists( 'site.cohort', $noid ) )
        throw lib::create( 'exception\argument', 'noid', $noid, __METHOD__ );
      
      $access_mod = lib::create( 'database\modifier' );
      $access_mod->where( 'user.name', '=', $noid['user.name'] );
      $access_mod->where( 'role.name', '=', $noid['role.name'] );
      $access_mod->where( 'site.name', '=', $noid['site.name'] );
      $access_mod->where( 'site.cohort', '=', $noid['site.cohort'] );
      $db_access = current( db\access::select( $access_mod ) );
      if( !$db_access ) throw lib::create( 'exception\argument', 'noid', $noid, __METHOD__ );
      $args['id'] = $db_access->id;
    }

    parent::__construct( $args );
  }
}
?>
