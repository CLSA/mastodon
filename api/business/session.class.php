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
   * This method overrides the parent method since sites have a service as well as a name
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
      // make sure there is a service name in the header
      if( !array_key_exists( 'HTTP_SERVICE_NAME', $_SERVER ) )
        throw lib::create( 'exception\runtime',
          'Application name missing, unable to process requested site and role', __METHOD__ );

      $service_class_name = lib::get_class_name( 'database\service' );
      $site_class_name = lib::get_class_name( 'database\site' );
      $db_service =
        $service_class_name::get_unique_record( 'name', $_SERVER['HTTP_SERVICE_NAME'] );
      $this->requested_site = $site_class_name::get_unique_record(
        array( 'service_id', 'name' ),
        array( $db_service->id, $site_name ) );

      $role_class_name = lib::get_class_name( 'database\role' );
      $this->requested_role = $role_class_name::get_unique_record( 'name', $role_name );
    }
  }
}
