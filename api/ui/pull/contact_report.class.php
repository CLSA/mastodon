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

    $modifier = lib::create( 'database\modifier' );
    $modifier->where( 'application.id', '=', lib::create( 'business\session' )->get_application()->id );
    $modifier->where( 'uid', 'IN', $this->uid_list );

    // create temporary table of last address
    $participant_class_name::db()->execute(
      'CREATE TEMPORARY TABLE temp_first_address '.
      'SELECT * FROM participant_first_address' );
    $participant_class_name::db()->execute(
      'ALTER TABLE temp_first_address '.
      'ADD INDEX dk_participant_id_address_id ( participant_id, address_id )' );

    // create temporary table of last consent
    $participant_class_name::db()->execute(
      'CREATE TEMPORARY TABLE temp_last_consent '.
      'SELECT * FROM participant_last_consent' );
    $participant_class_name::db()->execute(
      'ALTER TABLE temp_last_consent '.
      'ADD INDEX dk_participant_id_consent_id ( participant_id, consent_id )' );

    $modifier->cross_join( 'participant' );
    $modifier->join( 'language AS application_language', 'application.language_id', 'application_language.id' );
    $modifier->join( 'cohort', 'participant.cohort_id', 'cohort.id' );
    $modifier->left_join( 'language', 'participant.language_id', 'language.id' );
    $modifier->join( 'temp_first_address', 'participant.id', 'temp_first_address.participant_id' );
    $modifier->join( 'address', 'temp_first_address.address_id', 'address.id' );
    $modifier->join( 'region', 'address.region_id', 'region.id' );
    $modifier->left_join( 'state', 'participant.state_id', 'state.id' );
    $modifier->join( 'temp_last_consent', 'participant.id', 'temp_last_consent.participant_id' );
  
    $sql =
      'SELECT cohort.name AS cohort, '.
             'IFNULL( language.code, application_language.code ) AS language, '.
             'uid, '.
             'first_name, '.
             'last_name, '.
             'IF( address2 IS NULL, address1, CONCAT( address1, ", ", address2 ) ) AS address, '.
             'city, '.
             'region.abbreviation AS province, '.
             'postcode, '.
             'region.country, '.
             'IFNULL( state.name, "None" ) AS state, '.
             'IF( temp_last_consent.consent_id IS NULL, '.
                 '"None", '.
                 'CONCAT( '.
                   'IF( written, "Written ", "Verbal " ), '.
                   'IF( accept, "Accept", "Deny" ) '.
                 ') '.
             ') AS consent '.
      'FROM application '.
      $modifier->get_sql();

    $rows = $participant_class_name::db()->get_all( $sql );

    $header = array();
    $content = array();
    foreach( $rows as $row )
    {   
      // set up the header
      if( 0 == count( $header ) ) 
        foreach( $row as $column => $value )
          $header[] = ucwords( str_replace( '_', ' ', $column ) );

      $content[] = array_values( $row );
    }   

    $this->add_table( NULL, $header, $content, NULL );
  }
}
