<?php
/**
 * service_participant_release.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: service participant_release
 *
 * Syncs service information between Sabretooth and Mastodon
 */
class service_participant_release extends \cenozo\ui\push\base_participant_multi
{
  /**
   * Constructor.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Push arguments
   * @access public
   */
  public function __construct( $args )
  {
    // the parent class assumes that the subject is always "participant"
    $grand_parent = get_parent_class( get_parent_class( get_class() ) );
    $grand_parent::__construct( 'service', 'participant_release', $args );
  }

  /**
   * Validate the operation.  If validation fails this method will throw a notice exception.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @throws excpetion\argument, exception\permission
   * @access protected
   */
  protected function validate()
  {
    try
    {
      parent::validate();
    }
    catch( \cenozo\exception\notice $e )
    {
      $throw = true;

      // The parent class will throw a notice of the UID list is empty, however, we can allow
      // this so long as a date span has been choosen
      if( 'No participants have been selected.' == $e->get_raw_message() )
      {
        if( 0 < strlen( $this->get_argument( 'start_date', '' ) ) ||
            0 < strlen( $this->get_argument( 'end_date', '' ) ) )
        {
          // squelch the exception, we can allow the uid list to be empty in this instance
          $throw = false;
        }
        else
        { // be more specific in the notice text
          $e = lib::create( 'exception\notice',
            'You must either provide a list of participants or specify a start and/or end date.',
            __NOTICE__ );

        }
      }

      if( $throw ) throw $e;
    }
  }

  /**
   * This method executes the operation's purpose.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function execute()
  {
    parent::execute();

    $db_service = lib::create( 'database\service', $this->get_argument( 'service_id' ) );
    $start_date = $this->get_argument( 'start_date', '' );
    $end_date = $this->get_argument( 'end_date', '' );

    // include participants in the list, but only if one is provided
    $service_mod = 0 < count( $this->uid_list )
                 ? clone $this->modifier
                 : lib::create( 'database\modifier' );

    if( 0 < strlen( $start_date ) || 0 < strlen( $end_date ) )
    { // use start/end date to select participants
      if( 0 < strlen( $start_date ) )
      {
        // convert from server datetime since create_timestamp is written in local server time
        $datetime_string = util::from_server_datetime( $start_date );
        $service_mod->where( 'participant.create_timestamp', '>=', $datetime_string );
      }
      if( 0 < strlen( $end_date ) )
      {
        // convert from server datetime since create_timestamp is written in local server time
        $datetime_string = util::from_server_datetime( $end_date );
        $service_mod->where( 'participant.create_timestamp', '<=', $datetime_string );
      }
    }
    else
    { // do not allow all participants if there is no date span
      if( 0 == count( $this->uid_list ) ) $service_mod->where( 'uid', 'IN', array() );
    }

    $db_service->release_participant( $service_mod );
  }
}
