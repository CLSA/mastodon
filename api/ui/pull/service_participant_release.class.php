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
class service_participant_release extends \cenozo\ui\pull\base_participant_multi
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
        { // squelch the exception, we can allow the uid list to be empty in this instance
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

    $participant_count = array();
    $address_count = 0;
    $phone_count = 0;
    $consent_count = 0;
    $availability_count = 0;
    $note_count = 0;
    
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
      if( 0 == count( $this->uid_list ) ) $service_mod->where( 'uid', 'IN', array('') );
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
