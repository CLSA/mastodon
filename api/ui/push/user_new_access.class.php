<?php
/**
 * user_new_access.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: user new_access
 */
class user_new_access extends \cenozo\ui\push\user_new_access
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
   * Override the parent method to send a request to Beartooth or Sabretooth
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function send_machine_request()
  {
    // there's a chance that the role list is empty, skip if it is
    if( 0 == count( $this->machine_arguments['noid']['role_list'] ) ) return;

    // separate the sites by cohort, then send machine requests to each app that applies
    $site_list = $this->machine_arguments['noid']['site_list'];
    $comprehensive_site_list = array();
    $tracking_site_list = array();
    foreach( $site_list as $site )
    {
      if( 'tracking' == $site['cohort'] ) $tracking_site_list[] = $site;
      else $comprehensive_site_list[] = $site;
    }

    if( 0 < count( $comprehensive_site_list ) )
    {
      $this->machine_arguments['noid']['site_list'] = $comprehensive_site_list;
      $this->set_machine_request_url( BEARTOOTH_URL );
      parent::send_machine_request();
    }
    
    if( 0 < count( $tracking_site_list ) )
    {
      $this->machine_arguments['noid']['site_list'] = $tracking_site_list;
      $this->set_machine_request_url( SABRETOOTH_URL );
      parent::send_machine_request();
    }
  }
}
?>
