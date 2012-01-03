<?php
/**
 * user_new.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: user new
 *
 * Create a new user.
 * @package mastodon\ui
 */
class user_new extends \cenozo\ui\push\user_new
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
          !array_key_exists( 'role.name', $noid ) ||
          !array_key_exists( 'site.name', $noid ) ||
          !array_key_exists( 'site.cohort', $noid ) )
        throw lib::create( 'exception\argument', 'noid', $noid, __METHOD__ );

      $db_role = db\role::get_unique_record( 'name', $noid['role.name'] );
      if( !$db_role ) throw lib::create( 'exception\argument', 'noid', $noid, __METHOD__ );
      $this->role_id = $db_role->id;

      $db_site = db\site::get_unique_record(
        array( 'name', 'cohort' ),
        array( $noid['site.name'], $noid['site.cohort'] ) );
      if( !$db_site ) throw lib::create( 'exception\argument', 'noid', $noid, __METHOD__ );
      $this->site_id = $db_site->id;
    }

    parent::__construct( $args );
  }
}
?>
