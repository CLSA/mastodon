<?php
/**
 * post.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace mastodon\service\participant;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * The base class of all post services.
 */
class post extends \cenozo\service\participant\post
{
  /**
   * Extends parent method
   */
  protected function validate()
  {
    parent::validate();

    if( $this->may_continue() )
    {
      $session = lib::create( 'business\session' );
      $file = $this->get_file_as_object();
      if( is_object( $file ) ) // participant import sends an array
      {
        if( property_exists( $file, 'application_id' ) )
        {
          // only tier 3 can set the application
          if( 3 > $session->get_role()->tier )
          {
            $this->status->set_code( 403 );
          }
          else
          {
            // if an appliaction id is provided then make sure it exists
            try { $this->db_application = lib::create( 'database\application', $file->application_id ); }
            catch( \cenozo\exception\runtime $e ) { $this->status->set_code( 404 ); }
          }
        }

        if( property_exists( $file, 'site_id' ) )
        {
          // only all-site roles set the site
          if( !$session->get_role()->all_sites )
          {
            $this->status->set_code( 403 );
          }
          else
          {
            // if an appliaction id is provided then make sure it exists and belongs to the specific application
            if( !is_null( $file->site_id ) )
            {
              try { $this->db_site = lib::create( 'database\site', $file->site_id ); }
              catch( \cenozo\exception\runtime $e ) { $this->status->set_code( 404 ); }

              $site_mod = lib::create( 'database\modifier' );
              $site_mod->where( 'site.id', '=', $this->db_site->id );
              if( is_null( $this->db_application ) || 0 == $this->db_application->get_site_count( $site_mod ) )
                $this->status->set_code( 400 );
            }
          }
        }

        if( !property_exists( $file, 'mode' ) || 'release' != $file->mode  )
        {
          // if not in release mode then there cannot be a site_id argument
          if( property_exists( $file, 'site_id' ) ) $this->status->set_code( 400 );
        }
      }
    }
  }

  /**
   * Extends parent method
   */
  protected function execute()
  {
    $file = $this->get_file_as_object();
    if( is_array( $file ) || !property_exists( $file, 'mode' ) || is_null( $file->mode ) )
    {
      parent::execute();
    }
    else
    {
      $participant_class_name = lib::get_class_name( 'database\participant' );

      // This is a special service since participants cannot be added to the system through the web interface.
      // Instead, this service provides participant-based utility functions.
      if( property_exists( $file, 'identifier_list' ) )
      {
        $identifier_id = property_exists( $file, 'identifier_id' ) ? $file->identifier_id : NULL;
        $db_identifier = is_null( $identifier_id ) ? NULL : lib::create( 'database\identifier', $identifier_id );
        $identifier_list = $participant_class_name::get_valid_identifier_list_by_application(
          $db_identifier,
          $file->identifier_list,
          $this->db_application,
          'unreleased_only' == $file->mode || 'release' == $file->mode
        );

        if( 'release' == $file->mode )
        { // release the participants
          if( 0 < count( $identifier_list ) )
          {
            $modifier = lib::create( 'database\modifier' );
            if( is_null( $db_identifier ) )
            {
              $modifier->where( 'participant.uid', 'IN', $identifier_list );
            }
            else
            {
              $modifier->join( 'participant_identifier', 'participant.id', 'participant_identifier.participant_id' );
              $modifier->where( 'participant_identifier.identifier_id', '=', $identifier_id );
              $modifier->where( 'participant_identifier.value', 'IN', $identifier_list );
            }

            $this->db_application->release_participants( $modifier, $this->db_site );

            // update the application's queue if necessary
            if( $this->db_application->update_queue )
            {
              // we need to complete any transactions before continuing
              lib::create( 'business\session' )->get_database()->complete_transaction();

              try
              {
                $cenozo_manager = lib::create( 'business\cenozo_manager', $this->db_application );
                $cenozo_manager->get( 'queue/1?repopulate=full' );
              }
              catch( \cenozo\exception\runtime $e )
              {
                // note runtime errors but keep processing anyway
                log::error( $e->get_message() );
              }
            }
          }
        }
        else // any other mode
        { // return a list of all valid identifiers and count by cohort and site
          $site_list = array();

          if( 0 < count( $identifier_list ) )
          {
            $site_sel = lib::create( 'database\select' );
            $site_sel->from( 'participant' );
            $site_sel->add_table_column( 'cohort', 'name', 'cohort' );
            $site_sel->add_column( 'IFNULL( site.name, "(none)" )', 'site', false );
            $site_sel->add_column( 'COUNT(*)', 'total', false );
            $site_mod = lib::create( 'database\modifier' );
            $site_mod->join( 'cohort', 'participant.cohort_id', 'cohort.id' );
            $join_mod = lib::create( 'database\modifier' );
            $join_mod->where( 'participant.id', '=', 'participant_site.participant_id', false );
            $join_mod->where( 'participant_site.application_id', '=', $this->db_application->id );
            $site_mod->join_modifier( 'participant_site', $join_mod );
            $site_mod->left_join( 'site', 'participant_site.site_id', 'site.id' );
            
            if( is_null( $db_identifier ) )
            {
              $site_mod->where( 'participant.uid', 'IN', $identifier_list );
            }
            else
            {
              $site_mod->join( 'participant_identifier', 'participant.id', 'participant_identifier.participant_id' );
              $site_mod->where( 'participant_identifier.identifier_id', '=', $identifier_id );
              $site_mod->where( 'participant_identifier.value', 'IN', $identifier_list );
            }
            
            $site_mod->group( 'cohort.id' );
            $site_mod->group( 'site.id' );

            foreach( $participant_class_name::select( $site_sel, $site_mod ) as $row )
            {
              if( !array_key_exists( $row['cohort'], $site_list ) ) $site_list[$row['cohort']] = array();
              $site_list[$row['cohort']][$row['site']] = $row['total'];
            }
          }

          $this->set_data( array(
            'identifier_list' => $identifier_list,
            'site_list' => $site_list
          ) );
        }
      }
      else $this->status->set_code( 400 ); // must provide an identifier_list
    } 
  }

  /**
   * Overrides the parent method (this service not meant for creating resources)
   */
  protected function create_resource( $index )
  {
    return NULL;
  }

  /**
   * A cache of the application used by this service
   * @var database\application $db_application
   * @access protected
   */
  protected $db_application = NULL;

  /**
   * A cache of the site used by this service
   * @var database\site $db_site
   * @access protected
   */
  protected $db_site = NULL;
}
