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
   * Validate the operation.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @throws exception\notice
   * @access protected
   */
  protected function validate()
  {
    parent::validate();

    $site_id_list = $this->get_argument( 'site_id_list' );
    $role_id_list = $this->get_argument( 'role_id_list' );

    // get a list of which cohorts we are adding access to
    $cohort_list = array();
    foreach( $site_id_list as $site_id )
    {
      $db_site = lib::create( 'database\site', $site_id );
      $cohort_list[] = $db_site->cohort;
    }
    $cohort_list = array_unique( $cohort_list );

    // are we adding an admin role?
    $role_class_name = lib::get_class_name( 'database\role' );
    $db_role = $role_class_name::get_unique_record( 'name', 'administrator' );
    foreach( $role_id_list as $role_id )
    {
      if( $role_id == $db_role->id )
      { // admin role being added, check the user for admin access to the cohort
        foreach( $cohort_list as $cohort )
        {
          $access_mod = lib::create( 'database\modifier' );
          $access_mod->where( 'role_id', '=', $db_role->id );
          $access_mod->where( 'site.cohort', '=', $cohort );
          if( 0 == lib::create( 'business\session' )->get_user()->get_access_count( $access_mod ) )
            throw lib::create( 'exception\notice',
              sprintf( 'You require administrator access to a %s site in order to grant '.
                       'administrator access to any %s site.',
                       $cohort,
                       $cohort ),
              __METHOD__ );
        }
        break; // no need to keep looping through roles
      }
    }
    
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
