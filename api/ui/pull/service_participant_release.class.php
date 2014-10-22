<?php
/**
 * service_participant_release.class.php
 * 
 * @author Dean Inglis <inglisd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\pull;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Base class for all list pull operations.
 * 
 * @abstract
 */
class service_participant_release extends \cenozo\ui\pull
{
  /**
   * Constructor
   * 
   * @author Dean Inglis <inglisd@mcmaster.ca>
   * @param array $args Pull arguments.
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'service', 'participant_release', $args );
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

    $participant_count = array();
    $address_count = 0;
    $phone_count = 0;
    $consent_count = 0;
    $availability_count = 0;
    $note_count = 0;
    
    $db_service = lib::create( 'database\service', $this->get_argument( 'service_id' ) );
    $uid_list_string = preg_replace( '/[^a-zA-Z0-9]/', ' ', $this->get_argument( 'uid_list' ) );
    $uid_list_string = trim( $uid_list_string );
    $start_date = $this->get_argument( 'start_date', NULL );
    $end_date = $this->get_argument( 'end_date', NULL );
    
    $service_mod = lib::create( 'database\modifier' );

    // include participants in the list only
    $uid_list = array_unique( preg_split( '/\s+/', $uid_list_string ) );
    if( 1 == count( $uid_list ) && '' == $uid_list[0] ) $uid_list = array();

    if( 0 < count( $uid_list ) ) $service_mod->where( 'uid', 'IN', $uid_list );
    
    if( !is_null( $start_date ) || !is_null( $end_date ) )
    { // use start/end date to select participants
      if( !is_null( $start_date ) )
      {
        // convert from server datetime since create_timestamp is written in local server time
        $datetime_string = util::from_server_datetime( $start_date );
        $service_mod->where( 'participant.create_timestamp', '>=', $datetime_string );
      }
      if( !is_null( $end_date ) )
      {
        // convert from server datetime since create_timestamp is written in local server time
        $datetime_string = util::from_server_datetime( $end_date );
        $service_mod->where( 'participant.create_timestamp', '<=', $datetime_string );
      }
    }
    else
    { // do not allow all participants if there is no date span
      if( 0 == count( $uid_list ) ) $service_mod->where( 'uid', 'IN', array('') );
    }

    // get a list of all unreleased participants
    foreach( $db_service->release_participant( $service_mod, true ) as $db_participant )
    {
      $address_count += $db_participant->get_address_count();
      $phone_count += $db_participant->get_phone_count();
      $consent_count += $db_participant->get_consent_count();
      $availability_count += $db_participant->get_availability_count();
      $note_count += $db_participant->get_note_count();

      $db_site = $db_participant->get_effective_site( $db_service );
      $site_name = is_null( $db_site ) ? 'none' : $db_site->name;
      if( !array_key_exists( $site_name, $participant_count ) ) $participant_count[$site_name] = 0;
      $participant_count[$site_name]++;
    }

    $this->data = array();
    if( 0 == count( $participant_count ) )
      $this->data['New participants'] = 0;
    else foreach( $participant_count as $site => $count )
      $this->data['New participants ('.$site.')'] = $count;
    $this->data['Addresses'] = $address_count;
    $this->data['Phone numbers'] = $phone_count;
    $this->data['Consent entries'] = $consent_count;
    $this->data['Availability entries'] = $availability_count;
    $this->data['Note entries'] = $note_count;
  }
  
  /**
   * Lists are always returned in JSON format.
   * 
   * @author Dean Inglis <inglisd@mcmaster.ca>
   * @return string
   * @access public
   */
  public function get_data_type() { return "json"; }
}
