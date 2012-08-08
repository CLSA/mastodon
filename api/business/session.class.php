<?php
/**
 * session.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\business;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * session: handles all session-based information
 *
 * The session class is used to track all information from the time a user logs into the system
 * until they log out.
 * This class is a singleton, instead of using the new operator call the self() method.
 */
final class session extends \cenozo\business\session
{
  /**
   * Processes requested site and role and sets the session appropriately.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param string $site_name
   * @param string $role_name
   * @access protected
   */
  protected function process_requested_site_and_role( $site_name, $role_name )
  {
    // try and use the requested site and role, if necessary
    if( !is_null( $site_name ) && !is_null( $role_name ) )
    {
      // override the parent method since sites have a cohort as well as a name
      $site_class_name = lib::get_class_name( 'database\site' );
      $this->requested_site = $site_class_name::get_unique_record(
        array( 'cohort', 'name' ),
        explode( '////', $site_name ) );

      $role_class_name = lib::get_class_name( 'database\role' );
      $this->requested_role = $role_class_name::get_unique_record( 'name', $role_name );
    }
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
    // create the database if it doesn't exist yet
    if( is_null( $this->audit_database ) )
    {
      $setting_manager = lib::create( 'business\setting_manager' );
      if( $setting_manager->get_setting( 'audit_db', 'enabled' ) )
      {
        $this->audit_database = lib::create( 'database\database',
          $setting_manager->get_setting( 'audit_db', 'driver' ),
          $setting_manager->get_setting( 'audit_db', 'server' ),
          $setting_manager->get_setting( 'audit_db', 'username' ),
          $setting_manager->get_setting( 'audit_db', 'password' ),
          $setting_manager->get_setting( 'audit_db', 'database' ),
          $setting_manager->get_setting( 'audit_db', 'prefix' ) );
      }
    }

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
