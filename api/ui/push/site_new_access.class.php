<?php
/**
 * site_new_access.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: site new_access
 */
class site_new_access extends \cenozo\ui\push\site_new_access
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
    foreach( $args['noid']['role_list'] as $index => $value )
      if( 'typist' == $value['name'] ) unset( $args['noid']['role_list'][$index] );

    return $args;
  }

  /**
   * Override the parent method to send a request to the appropriate application
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function send_machine_request()
  {
    // there's a chance that the role list is empty, skip if it is
    if( 0 == count( $this->machine_arguments['noid']['role_list'] ) ) return;

    if( 'comprehensive' == $this->get_record()->cohort )
    {
      $this->set_machine_request_url( BEARTOOTH_URL );
      parent::send_machine_request();
    }
    else if( 'tracking' == $this->get_record()->cohort )
    {   
      $this->set_machine_request_url( SABRETOOTH_URL );
      parent::send_machine_request();
    }
  }
}
?>
