<?php
/**
 * user_delete.class.php
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
 * push: user delete
 * 
 * @package mastodon\ui
 */
class user_delete extends base_delete
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

    parent::__construct( 'user', $args );
  }
}
?>
