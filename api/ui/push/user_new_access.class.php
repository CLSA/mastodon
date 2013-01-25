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

    // get a list of which services we are adding access to
    $service_id_list = array();
    foreach( $site_id_list as $site_id )
    {
      $db_site = lib::create( 'database\site', $site_id );
      $service_id_list[] = $db_site->service_id;
    }
    $service_id_list = array_unique( $service_id_list );

    // are we adding an admin role?
    $role_class_name = lib::get_class_name( 'database\role' );
    $db_administrator_role = $role_class_name::get_unique_record( 'name', 'administrator' );
    foreach( $role_id_list as $role_id )
    {
      if( $role_id == $db_administrator_role->id )
      { // admin role being added, check the user for admin access to the service
        foreach( $service_id_list as $service_id )
        {
          $access_mod = lib::create( 'database\modifier' );
          $access_mod->where( 'role_id', '=', $db_administrator_role->id );
          $access_mod->where( 'site.service_id', '=', $service_id );
          if( 0 == lib::create( 'business\session' )->get_user()->get_access_count( $access_mod ) )
          {
            $db_service = lib::create( 'database\service', $service_id );
            throw lib::create( 'exception\notice',
              sprintf( 'You require administrator access to a %s site in order to grant '.
                       'administrator access to any %s site.',
                       $db_service->name,
                       $db_service->name ),
              __METHOD__ );
          }
        }
        break; // no need to keep looping through roles
      }
    }
    
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

    $service_class_name = lib::get_class_name( 'database\service' );

    // separate the sites by service name, then send machine requests to each service
    $site_list = $this->machine_arguments['noid']['site_list'];
    $service_list = array();
    foreach( $site_list as $site )
    {
      $service_name = $site['service_id']['name'];
      if( !array_key_exists( $service_name, $service_list ) )
        $service_list[$service_name] = array();
      $service_list[$service_name][] = $site;
    }

    // now send site/role list as a group, one service at a time
    foreach( $service_list as $service_name => $site_list )
    {
      $db_service = $service_class_name::get_unique_record( 'name', $service_name );
      $this->machine_arguments['noid']['site_list'] = $site_list;
      $this->set_machine_request_url( $db_service->get_url() );
      parent::send_machine_request();
    }
  }
}
