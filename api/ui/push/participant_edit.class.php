<?php
/**
 * participant_edit.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: participant edit
 *
 * Edit a participant.
 */
class participant_edit extends base_participant_edit
{
  /**
   * Constructor.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Push arguments
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'participant', $args );
  }

  /**
   * Processes arguments, preparing them for the operation.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function prepare()
  {
    parent::prepare();

    $this->set_participant_for_machine_requests( $this->get_record() );
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

    if( $this->get_machine_request_enabled() )
    {
      $columns = $this->get_argument( 'columns', array() );

      // don't bother sending a machine request if we are only changing columns which do not exist
      // in external applications
      if( 1 == count( $columns ) && (
          array_key_exists( 'cohort_id', $columns ) ||
          array_key_exists( 'use_informant', $columns ) ) )
        $this->set_machine_request_enabled( false );
    }
  }

  /**
   * Allow site_id as an argument as a way to set the participant's preferred site
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function execute()
  {
    parent::execute();

    $columns = $this->get_argument( 'columns', array() );
    if( array_key_exists( 'site_id', $columns ) )
    {
      // this may be a service which is updating the participant's preferred site
      // if it is then we need to define that service's preferred site, if not then
      // we change the default servide preferred site
      $service_class_name = lib::get_class_name( 'database\service' );
      $service_name = $this->get_machine_application_name();
      $db_service = $service_name
                  ? $service_class_name::get_unique_record( 'name', $service_name )
                  : NULL;

      $db_site = $columns['site_id']
               ? lib::create( 'database\site', $columns['site_id'] )
               : NULL;
      $this->get_record()->set_preferred_site( $db_site, $db_service );
    }
  }
}
