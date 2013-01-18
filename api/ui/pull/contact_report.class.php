<?php
/**
 * contact_report.class.php
 * 
 * @author Dean Inglis <inglisd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\pull;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Mailout required report data.
 * 
 * @abstract
 */
class contact_report extends \cenozo\ui\pull\base_report
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
    parent::__construct( 'contact', $args );
  }

  /**
   * Builds the report.
   * @author Dean Inglis <inglisd@mcmaster.ca>
   * @access protected
   */
  protected function build()
  {
    $participant_class_name = lib::get_class_name( 'database\participant' );

    // get the report arguments
    $uid_list_string = preg_replace( '/[^a-zA-Z0-9]/', ' ', $this->get_argument( 'uid_list' ) );
    $uid_list_string = trim( $uid_list_string );
    $uid_list = array_unique( preg_split( '/\s+/', $uid_list_string ) );

    $contents = array();
    foreach( $uid_list as $uid )
    {
      // determine the participant record and make sure it is valid and has a valid address
      $db_participant = $participant_class_name::get_unique_record( 'uid', $uid );
      if( is_null( $db_participant ) ) continue;
      $db_address = $db_participant->get_first_address();
      if( is_null( $db_address ) ) continue;

      $db_site = $db_participant->get_primary_site();
      $site_name = is_null( $db_site ) ? 'None' : $db_site->name;
      $db_region = $db_address->get_region();
      $address = $db_address->address1;
      if( !is_null( $db_address->address2 ) ) $address .= ' '.$db_address->address2;
      $db_consent = $db_participant->get_last_consent();
      $consent = $db_consent ? $db_consent->event : 'None';

      $contents[] = array(
        $db_participant->get_cohort()->name,
        $site_name,
        'fr' == $db_participant->language ? 'fr' : 'en', // english if not set
        $db_participant->uid,
        $db_participant->first_name,
        $db_participant->last_name,
        $address,
        $db_address->city,
        $db_region->name,
        $db_address->postcode,
        $db_region->country,
        is_null( $db_participant->status ) ? 'None' : $db_participant->status,
        $consent );
    }
    
    $header = array(
      'Cohort',
      'Site',
      'Language',
      'CLSA ID',
      'First Name',
      'Last Name',
      'Address',
      'City',
      'Prov/State',
      'Postal Code',
      'Country',
      'Status',
      'Consent' );
    
    $this->add_table( NULL, $header, $contents, NULL );
  }
}
?>
