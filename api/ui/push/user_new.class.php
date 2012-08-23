<?php
/**
 * user_new.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: user new
 *
 * Edit a user.
 */
class user_new extends \cenozo\ui\push\user_new
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
   * Sets up the operation with any pre-execution instructions that may be necessary.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function setup()
  {
    parent::setup();

    if( $this->get_machine_request_received() && $this->get_machine_request_enabled() )
      $this->machine_arguments = $this->convert_to_noid( $this->arguments );
  }

  /**
   * Override the parent method to send a machine request even if the request was
   * received by a machine.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function finish()
  {
    parent::finish();

    if( $this->get_machine_request_received() && $this->get_machine_request_enabled() )
      $this->send_machine_request();
  }

  /**
   * Override the parent method to remove mastodon-only roles.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args An argument list, usually those passed to the push operation.
   * @return array
   * @access protected
   */
  protected function convert_to_noid( $args )
  {
    $args = parent::convert_to_noid( $args );

    // remove typist from the role list, if it exists
    if( array_key_exists( 'columns', $args['noid'] ) &&
        array_key_exists( 'role', $args['noid']['columns'] ) &&
        'typist' == $args['noid']['columns']['role']['name'] ) unset( $args['noid']['columns'] );

    return $args;
  }

  /**
   * Override the parent method to send a request to both Beartooth and Sabretooth
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function send_machine_request()
  {
    $columns = NULL;

    // only send the initial site/role to the appropriate application
    if( array_key_exists( 'columns', $this->machine_arguments['noid'] ) )
    {
      $columns = $this->machine_arguments['noid']['columns'];
      unset( $this->machine_arguments['noid']['columns'] );
    }

    if( 'beartooth' != $this->get_machine_application_name() )
    {
      $this->machine_arguments['noid']['columns'] =
        !is_null( $columns ) && 'comprehensive' == $columns['site']['cohort'] ? $columns : NULL;
      $this->set_machine_request_url( BEARTOOTH_URL );
      $this->use_machine_credentials( true );
      parent::send_machine_request();
    }

    if( 'sabretooth' != $this->get_machine_application_name() )
    {
      $this->machine_arguments['noid']['columns'] =
        !is_null( $columns ) && 'tracking' == $columns['site']['cohort'] ? $columns : NULL;
      $this->set_machine_request_url( SABRETOOTH_URL );
      $this->use_machine_credentials( true );
      parent::send_machine_request();
    }
  }
}
?>
