<?php
/**
 * access.class.php
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
 * access: record
 *
 * @package mastodon\database
 */
class access extends record
{
  /**
   * Returns whether or not the access exists.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param user $db_user
   * @param site $db_site
   * @param role $db_role
   * @return boolean
   * @static
   * @access public
   */
  public static function exists( $db_user, $db_site, $db_role )
  {
    // validate arguments
    if( !is_object( $db_user ) || !is_a( $db_user, '\\mastodon\\database\\user' ) )
    {
      throw new exc\argument( 'user', $db_user, __METHOD__ );
    }
    else if( !is_object( $db_role ) || !is_a( $db_role, '\\mastodon\\database\\role' ) )
    {
      throw new exc\argument( 'role', $db_role, __METHOD__ );
    }
    else if( !is_object( $db_site ) || !is_a( $db_site, '\\mastodon\\database\\site' ) )
    {
      throw new exc\argument( 'site', $db_site, __METHOD__ );
    }

    $modifier = new modifier();
    $modifier->where( 'user_id', '=', $db_user->id );
    $modifier->where( 'role_id', '=', $db_role->id );
    $modifier->where( 'site_id', '=', $db_site->id );

    $id = static::db()->get_one(
      sprintf( 'SELECT id FROM access %s',
               $modifier->get_sql() ) );

    return !is_null( $id );
  }
  
  /**
   * Override parent save method by making sure that only admins can create admins
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @throws exception\permission
   * @access public
   */
  public function save()
  {
    if( 'administrator' != bus\session::self()->get_role()->name &&
        // we can't use $this->get_role() here since the record may not exist yet
        role::get_unique_record( 'name', 'administrator' )->id == $this->role_id )
      throw new exc\permission(
        // fake the operation
        operation::get_operation( 'action', 'user', 'new_access' ), __METHOD__ );

    parent::save();
  }
  
  /**
   * Override parent delete method by making sure that only admins can remove admins
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @throws exception\permission
   * @access public
   */
  public function delete()
  {
    if( 'administrator' != bus\session::self()->get_role()->name &&
        'administrator' == $this->get_role()->name )
      throw new exc\permission(
        // fake the operation
        operation::get_operation( 'action', 'access', 'delete' ), __METHOD__ );

    parent::delete();
  }
}
?>
