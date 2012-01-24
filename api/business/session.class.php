<?php
/**
 * session.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\business
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
    if( $this->is_initialized() ) return;

    parent::initialize();
  }

  /**
   * Get the quexf database.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @return database
   * @access public
   */
  public function get_quexf_database()
  {
    // create the database if it doesn't exist yet
    if( is_null( $this->quexf_database ) )
    {
      $setting_manager = lib::create( 'business\setting_manager' );
      $this->quexf_database = lib::create( 'database\database',
        $setting_manager->get_setting( 'quexf_db', 'driver' ),
        $setting_manager->get_setting( 'quexf_db', 'server' ),
        $setting_manager->get_setting( 'quexf_db', 'username' ),
        $setting_manager->get_setting( 'quexf_db', 'password' ),
        $setting_manager->get_setting( 'quexf_db', 'database' ),
        $setting_manager->get_setting( 'quexf_db', 'prefix' ) );
    }

    return $this->quexf_database;
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
   * The quexf database object.
   * @var database
   * @access private
   */
  private $quexf_database = NULL;

  /**
   * The audit database object.
   * @var database
   * @access private
   */
  private $audit_database = NULL;
}
?>
