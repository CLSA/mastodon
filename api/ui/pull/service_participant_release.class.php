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
    
    $db_service = lib::create( 'business\session' )->get_site()->get_service();
    $uid_list_string = preg_replace( '/[^a-zA-Z0-9]/', ' ', $this->get_argument( 'uid_list' ) );
    $uid_list_string = trim( $uid_list_string );
    $start_date = $this->get_argument( 'start_date', '' );
    $end_date = $this->get_argument( 'end_date', '' );
    
    $service_mod = lib::create( 'database\modifier' );
    if( 0 < strlen( $start_date ) || 0 < strlen( $end_date ) )
    { // use start/end date to select participants
      $service_mod->where_bracket( true );
      $service_mod->where_bracket( true );
      if( 0 < strlen( $start_date ) )
        $service_mod->where( 'import_entry.date', '>=', $start_date );
      if( 0 < strlen( $end_date ) )
        $service_mod->where( 'import_entry.date', '<=', $end_date );
      $service_mod->where_bracket( false );
      $service_mod->where_bracket( true, true ); // or
      if( 0 < strlen( $start_date ) )
        $service_mod->where( 'contact_form.date', '>=', $start_date );
      if( 0 < strlen( $end_date ) )
        $service_mod->where( 'contact_form.date', '<=', $end_date );
      $service_mod->where_bracket( false );
      $service_mod->where_bracket( false );
    }
    
    if( 0 < strlen( $uid_list_string ) && 0 != strcasecmp( 'all', $uid_list_string ) )
    { // include participants in the list only
      $uid_list = array_unique( preg_split( '/\s+/', $uid_list_string ) );
      $service_mod->where( 'uid', 'IN', $uid_list );
    }

    // get a list of all unparticipant_releasehed participants
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
    foreach( $participant_count as $site => $count )
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
