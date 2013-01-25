<?php
/**
 * access_delete.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: access delete
 */
class access_delete extends \cenozo\ui\push\access_delete
{
  /**
   * Processes arguments, preparing them for the operation.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function prepare()
  {
    parent::prepare();

    $this->set_machine_request_enabled( true );
  }

  /** 
   * Validate the operation.  If validation fails this method will throw a notice exception.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @throws excpetion\argument, exception\permission
   * @access protected
   */
  protected function validate()
  {
    parent::validate();

    // only delete roles which mastodon can create (admin and typist)
    // UNLESS we recevied this operation from another application
    if( is_null( $this->get_machine_application_name() ) ||
        'mastodon' == $this->get_machine_application_name() )
    {
      $db_access = lib::create( 'database\access', $this->get_argument( 'id' ) );
      $db_role = $db_access->get_role();
      if( 'administrator' != $db_role->name && 'typist' != $db_role->name )
        throw lib::create( 'exception\notice',
          'Cannot delete that access since it is not an administrator or typist role.',
          __METHOD__ );
    }
  }

  /**
   * Override the parent method to send a request to both Beartooth and Sabretooth
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function send_machine_request()
  {
    $this->set_machine_request_url( $this->get_record()->get_site()->get_service()->get_url() );
    parent::send_machine_request();
  }
}
