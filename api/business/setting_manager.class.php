<?php
/**
 * setting_manager.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\business
 * @filesource
 */

namespace mastodon\business;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Manages software settings
 * 
 * @package mastodon\business
 */
class setting_manager extends \cenozo\business\setting_manager
{
  /**
   * Constructor.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @throws exception\argument
   * @access protected
   */
  protected function __construct( $arguments )
  {
    parent::__construct( $arguments );

    $static_settings = $arguments[0];

    // add the audit_db category to the manager
    // make sure the category exists
    if( !array_key_exists( 'audit_db', $static_settings ) )
      throw lib::create( 'exception\argument',
        'static_settings[audit_db]', NULL, __METHOD__ );

    $this->static_settings['audit_db'] = $static_settings['audit_db'];

    // get the survey database settings from the quexf config file
    if( !is_null( QUEXF_PATH ) )
    {   
      $file = QUEXF_PATH.'/config_vars.php';
      if( !file_exists( $file ) )
        throw lib::create( 'exception\runtime',
          'Cannot find quexf config_vars.php file.', __METHOD__ );

      include $file;
      $this->static_settings['quexf'] =
        array( 'processed_contact_path' => $m_processed_contact_path,
               'processed_consent_path' => $m_processed_consent_path );
      $this->static_settings['quexf_db'] =
        array( 'driver' => 'mysqlt',
               'server' => $db_host,
               'username' => $db_user,
               'password' => $db_pass,
               'database' => $db_name,
               'prefix' => '' );
    }

    // have the audit settings mirror the main database, if necessary
    foreach( $this->static_settings['audit_db'] as $key => $value )
    {
      if( false === $value && 'enabled' != $key )
        $this->static_settings['audit_db'][$key] =
          $this->static_settings['db'][$key];
    }
  }
}
