<?php
/**
 * role.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\database
 * @filesource
 */

namespace mastodon\database;
use mastodon\log, mastodon\util;
use mastodon\business as bus;
use mastodon\exception as exc;

/**
 * role: record
 *
 * @package mastodon\database
 */
class role extends base_access
{
  /**
   * Returns whether the user has the role for the given site.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param database\operation $db_operation
   * @return bool
   */
  public function has_operation( $db_operation )
  {
    if( is_null( $this->id ) )
    {
      log::warning( 'Tried to determine operation for role with no id.' );
      return false;
    }

    $modifier = new modifier();
    $modifier->where( 'operation_id', '=', $db_operation->id );
    return 0 < $this->get_operation_count( $modifier );
  }
}
?>
