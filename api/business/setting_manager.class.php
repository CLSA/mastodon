<?php
/**
 * setting_manager.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\business;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Manages software settings
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

    // have the audit settings mirror the main database, if necessary
    foreach( $this->static_settings['audit_db'] as $key => $value )
    {
      if( false === $value && 'enabled' != $key )
        $this->static_settings['audit_db'][$key] =
          $this->static_settings['db'][$key];
    }
  }
}
