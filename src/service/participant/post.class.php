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
      $session = lib::create( 'business\session' );

      $file = $this->get_file_as_array();
      if( array_key_exists( 'application_id', $file ) )
      {
        // only tier 3 can set the application
        if( 3 > $session->get_role()->tier )
          $this->status->set_code( 403 );
        else
        {
          // If an appliaction id is provided then make sure it exists
          try { $this->db_application = lib::create( 'database\application', $file['application_id'] ); }
          catch( \cenozo\exception\runtime $e ) { $this->status->set_code( 404 ); }
        }
      }
      else
      {
        $this->db_application = $session->get_application();
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
    if( array_key_exists( 'uid_list', $file ) && is_array( $file['uid_list'] ) )
    {
      $mode = array_key_exists( 'mode', $file ) ? $file['mode'] : NULL;

      $uid_list = array();
      
      if( 0 < count( $file['uid_list'] ) )
      {
        // go through the list and remove invalid UIDs
        $select = lib::create( 'database\select' );
        $select->add_column( 'uid' );
        $select->from( 'participant' );
        $modifier = lib::create( 'database\modifier' );
        $modifier->where( 'uid', 'IN', $file['uid_list'] );
        $modifier->order( 'uid' );
        
        if( !$db_role->all_sites || !is_null( $mode ) )
        {
          // restrict to participant cohorts in the given application
          $modifier->join( 'application_has_cohort', 'participant.cohort_id', 'application_has_cohort.cohort_id' );
          $modifier->join( 'application', 'application_has_cohort.application_id', 'application.id' );
          $modifier->where( 'application.id', '=', $this->db_application->id );
        }

        // restrict by site
        if( !$db_role->all_sites )
        {
          $sub_mod = lib::create( 'database\modifier' );
          $sub_mod->where( 'participant.id', '=', 'participant_site.participant_id', false );
          $sub_mod->where( 'participant_site.application_id', '=', 'application.id', false );
          $sub_mod->where( 'participant_site.site_id', '=', $db_site->id );
          $modifier->join_modifier( 'participant_site', $sub_mod );
        }

        // restrict if in mode
        if( !is_null( $mode ) )
        {
          $sub_mod = lib::create( 'database\modifier' );
          $sub_mod->where( 'participant.id', '=', 'application_has_participant.participant_id', false );
          $sub_mod->where( 'application_has_participant.application_id', '=', 'application.id', false );

          if( 'released_only' == $mode )
            $sub_mod->where( 'application_has_participant.datetime', '!=', NULL );
          else // unreleased_only or release
            $modifier->where( 'application_has_participant.datetime', '=', NULL );

          $modifier->join_modifier(
            'application_has_participant', $sub_mod, 'released_only' == $mode ? '' : 'left' );
        }

        foreach( $participant_class_name::select( $select, $modifier ) as $row ) $uid_list[] = $row['uid'];
      }

      if( 'release' == $mode )
      { // release the participants
        if( 0 < count( $uid_list ) )
        {
          $modifier = lib::create( 'database\modifier' );
          $modifier->where( 'participant.uid', 'IN', $uid_list );
          $this->db_application->release_participants( $modifier );
        }
      }
      else if( !is_null( $mode ) ) // any other release mode
      { // return a list of all valid uids and count by cohort and site
        $site_list = array();

        if( 0 < count( $uid_list ) )
        {
          $site_sel = lib::create( 'database\select' );
          $site_sel->from( 'participant' );
          $site_sel->add_table_column( 'cohort', 'name', 'cohort' );
          $site_sel->add_table_column( 'site', 'name', 'site' );
          $site_sel->add_column( 'COUNT(*)', 'total', false );
          $site_mod = lib::create( 'database\modifier' );
          $site_mod->join( 'cohort', 'participant.cohort_id', 'cohort.id' );
          $join_mod = lib::create( 'database\modifier' );
          $join_mod->where( 'participant.id', '=', 'participant_site.participant_id', false );
          $join_mod->where( 'participant_site.application_id', '=', $this->db_application->id );
          $site_mod->join_modifier( 'participant_site', $join_mod );
          $site_mod->join( 'site', 'participant_site.site_id', 'site.id' );
          $site_mod->where( 'participant.uid', 'IN', $uid_list );
          $site_mod->group( 'cohort.id' );
          $site_mod->group( 'site.id' );

          foreach( $participant_class_name::select( $site_sel, $site_mod ) as $row )
          {
            if( !array_key_exists( $row['cohort'], $site_list ) ) $site_list[$row['cohort']] = array();
            $site_list[$row['cohort']][$row['site']] = $row['total'];
          }
        }

        $this->set_data( array(
          'uid_list' => $uid_list,
          'site_list' => $site_list
        ) );
      }
      else if( array_key_exists( 'preferred_site_id', $file ) )
      { // change the participants' preferred site
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
