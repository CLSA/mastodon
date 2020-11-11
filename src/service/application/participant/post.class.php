<?php
/**
 * post.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace mastodon\service\application\participant;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * The base class of all post services.
 */
class post extends \cenozo\service\write
{
  /**
   * Extends parent constructor
   */
  public function __construct( $path, $args, $file )
  {
    parent::__construct( 'POST', $path, $args, $file );
  }

  /**
   * Override parent method
   */
  protected function execute()
  {
    parent::execute();

    // adding relationship between application and participant is special
    $participant_id_list = $this->get_file_as_object();
    if( !is_int( $participant_id_list ) && !is_array( $participant_id_list ) )
    {
      $this->status->set_code( 400 );
      throw lib::create( 'exception\argument', 'participant_id_list', $participant_id_list, __METHOD__ );
    }

    if( !is_array( $participant_id_list ) ) $participant_id_list = array( $participant_id_list );
    $modifier = lib::create( 'database\modifier' );
    $modifier->where( 'participant.id', 'IN', $participant_id_list );
    $this->get_parent_record()->release_participants( $modifier );

    // update the application's queue if necessary
    $db_application = $this->get_parent_record();
    if( $db_application->update_queue )
    {
      // we need to complete any transactions before continuing
      lib::create( 'business\session' )->get_database()->complete_transaction();

      try
      {
        $cenozo_manager = lib::create( 'business\cenozo_manager', $db_application );
        foreach( $participant_id_list as $participant_id )
          $cenozo_manager->patch( sprintf( 'participant/%s?repopulate=1', $participant_id ) );
      }
      catch( \cenozo\exception\runtime $e )
      {
        // note runtime errors but keep processing anyway
        log::error( $e->get_message() );
      }
    }
  }
}
