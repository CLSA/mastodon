<?php
/**
 * session.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\business
 * @filesource
 */

namespace mastodon\business;
use mastodon\log, mastodon\util;
use mastodon\database as db;
use mastodon\exception as exc;

/**
 * session: handles all session-based information
 *
 * The session class is used to track all information from the time a user logs into the system
 * until they log out.
 * This class is a singleton, instead of using the new operator call the self() method.
 * @package mastodon\business
 */
final class session extends \cenozo\business\session
{
  /**
   * Initializes the session.
   * 
   * This method should be called immediately after initial construct of the session.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @throws exception\runtime
   * @access public
   */
  public function initialize()
  {
    // don't initialize more than once
    if( $this->initialized ) return;

    $setting_manager = setting_manager::self();

    // create the databases
    $this->database = lib::create( 'database\database',
      $setting_manager->get_setting( 'db', 'driver' ),
      $setting_manager->get_setting( 'db', 'server' ),
      $setting_manager->get_setting( 'db', 'username' ),
      $setting_manager->get_setting( 'db', 'password' ),
      $setting_manager->get_setting( 'db', 'database' ),
      $setting_manager->get_setting( 'db', 'prefix' ) );
    if( $setting_manager->get_setting( 'audit_db', 'enabled' ) )
    {
      // If not set then the audit database settings use the same as the standard db,
      // with the exception of the prefix
      $this->audit_database = lib::create( 'database\database',
        $setting_manager->get_setting( 'audit_db', 'driver' ),
        $setting_manager->get_setting( 'audit_db', 'server' ),
        $setting_manager->get_setting( 'audit_db', 'username' ),
        $setting_manager->get_setting( 'audit_db', 'password' ),
        $setting_manager->get_setting( 'audit_db', 'database' ),
        $setting_manager->get_setting( 'audit_db', 'prefix' ) );
    }

    // determine the user (setting the user will also set the site and role)
    $user_name = $_SERVER[ 'PHP_AUTH_USER' ];
    $this->set_user( db\user::get_unique_record( 'name', $user_name ) );
    if( NULL == $this->user )
      throw lib::create( 'exception\runtime', 'User "'.$user_name.'" not found.', __METHOD__ );

    $this->initialized = true;
  }

  /**
   * Get the audit database.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @return database
   * @access public
   */
  public function get_audit_database()
  {
    return $this->audit_database;
  }

  /**
   * The audit database object.
   * @var database
   * @access private
   */
  private $audit_database = NULL;
}
?>
