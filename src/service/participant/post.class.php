<?php
/**
 * post.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\service\participant;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * The base class of all post services.
 */
class post extends \cenozo\service\service
{
  /**
   * Extends parent constructor
   */
  public function __construct( $path, $args, $file )
  {
    parent::__construct( 'POST', $path, $args, $file );
  }

  /**
   * Extends parent method
   */
  protected function validate()
  {
    parent::validate();

    if( 300 > $this->status->get_code() )
    {
      // If an appliaction id is provided then make sure it exists
      $file = $this->get_file_as_array();
      if( array_key_exists( 'application_id', $file ) )
      {
        try { $this->db_application = lib::create( 'database\application', $file['application_id'] ); }
        catch( \cenozo\exception\runtime $e ) { $this->status->set_code( 404 ); }
      }
    }
  }

  /**
   * Extends parent method
   */
  protected function execute()
  {
    parent::execute();

    $util_class_name = lib::get_class_name( 'util' );
    $participant_class_name = lib::get_class_name( 'database\participant' );
    $session = lib::create( 'business\session' );
    $db_site = $session->get_site();
    $db_role = $session->get_role();
    $db_user = $session->get_user();

    // This is a special service since participants cannot be added to the system through the web interface.
    // Instead, this service provides participant-based utility functions.
    $file = $this->get_file_as_array();
    if( array_key_exists( 'uid_list', $file ) && array_key_exists( 'application_id', $file ) )
    {
      // go through the list and remove invalid UIDs
      $select = lib::create( 'database\select' );
      $select->add_column( 'uid' );
      $select->from( 'participant' );
      $modifier = lib::create( 'database\modifier' );
      $modifier->where( 'uid', 'IN', $file['uid_list'] );
      $modifier->order( 'uid' );
      
      // restrict to participants in the given application
      /*
      $sub_mod = lib::create( 'database\modifier' );
      $sub_mod->where( 'participant.id', '=', 'application_has_participant.participant_id', false );
      $sub_mod->where( 'application_has_participant.application_id', '=', $this->db_application->id );
      $sub_mod->where( 'application_has_participant.datetime', '!=', NULL );
      $modifier->join_modifier(
        'application_has_participant', $sub_mod, $this->db_application->release_based ? '' : 'left' );

      // restrict by site
      if( !$db_role->all_sites )
      {
        $sub_mod = lib::create( 'database\modifier' );
        $sub_mod->where( 'participant.id', '=', 'participant_site.participant_id', false );
        $sub_mod->where( 'participant_site.application_id', '=', $this->db_application->id );
        $sub_mod->where( 'participant_site.site_id', '=', $db_site->id );
        $modifier->join_modifier( 'participant_site', $sub_mod );
      }
      */

      // prepare the select and modifier objects
      $uid_list = array();
      
      foreach( $participant_class_name::select( $select, $modifier ) as $row ) $uid_list[] = $row['uid'];

      /*
      $select = lib::create( 'database\select' );
      $select->from( 'participant' );
      $select->add_column( 'id', 'participant_id' );
      $modifier = lib::create( 'database\modifier' );
      $modifier->where( 'uid', 'IN', $uid_list );
      */

      if( array_key_exists( 'preferred_site_id', $file ) )
      { // change the participants' preferred site
      }
      else if( array_key_exists( 'release', $file ) )
      { // release the participants
      }
      else // return a list of all valid uids
      {
        $this->set_data( $uid_list );
      }
    }
    else $this->status->set_code( 400 ); // must provide a uid_list
  }

  /**
   * TODO: document
   */
  protected function create_resource( $index )
  {
    return NULL;
  }

  /**
   * TODO: document
   */
  protected $db_application = NULL;
}
