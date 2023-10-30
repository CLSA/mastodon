<?php
/**
 * query.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace mastodon\service\log_entry;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Special queue for handling the query meta-resource
 */
class query extends \cenozo\service\query
{
  /**
   * Override parent method
   */
  protected function execute()
  {
    $application_class_name = lib::get_class_name( 'database\application' );

    if( $this->get_argument( 'update', false ) )
    {
      // start by truncating the log
      $application_class_name::db()->execute( 'TRUNCATE log_entry' );
    }

    parent::execute();

    if( $this->get_argument( 'update', false ) )
    {
      // loop through all other applications and update their logs as well

      $db_application = lib::create( 'business\session' )->get_application();
      $application_mod = lib::create( 'database\modifier' );
      $application_mod->where( 'id', '!=', $db_application->id );
      foreach( $application_class_name::select_objects( $application_mod ) as $db_application )
      {
        $cenozo_manager = lib::create( 'business\cenozo_manager', $db_application );
        if( $cenozo_manager->exists() ) $cenozo_manager->get( 'log_entry?update=1' );
      }
    }
  }
}
