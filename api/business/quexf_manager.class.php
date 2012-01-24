<?php
/**
 * quexf_manager.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\business
 * @filesource
 */

namespace mastodon\business;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Manages importing data from QUEXF
 * 
 * @package mastodon\business
 */
class quexf_manager extends \cenozo\singleton
{
  /**
   * Constructor.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function __construct()
  {
    $this->enabled = !is_null( QUEXF_PATH );
  }

  /**
   * Determines if Quexf is enabled.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @return boolean
   * @access public
   */
  public function is_enabled()
  {
    return $this->enabled;
  }

  /**
   * Gets the number of participants ready for import.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function get_participant_count()
  {
    // always return 0 if quexf is disabled
    if( !$this->enabled ) return 0;

    $modifier = lib::create( 'database\modifier' );
    $modifier->where( 'uid', '=', NULL );
    $quexf_person_class_name = lib::get_class_name( 'database\quexf\person' );
    return $quexf_person_class_name::count( $modifier );
  }

  /**
   * Gets the number of participants ready for import which are invalid.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function get_invalid_participant_count()
  {
    // always return 0 if quexf is disabled
    if( !$this->enabled ) return 0;

    return $this->get_participant_count() - $this->get_valid_participant_count();
  }

  /**
   * Gets the number of participants ready for import which are valid.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function get_valid_participant_count()
  {
    // always return 0 if quexf is disabled
    if( !$this->enabled ) return 0;

    $modifier = static::get_valid_participant_modifier();
    $modifier->where( 'uid', '=', NULL );
    $quexf_person_class_name = lib::get_class_name( 'database\quexf\person' );
    return $quexf_person_class_name::count( $modifier );
  }

  /**
   * Imports all valid participants, assigns them a UID from the pool and removes them from QUEXF
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function import()
  {
    $quexf_person_class_name = lib::get_class_name( 'database\quexf\person' );
    $participant_class_name = lib::get_class_name( 'database\participant' );

    // loop through every valid participant ready for import
    $modifier = static::get_valid_participant_modifier();
    $modifier->where( 'uid', '=', NULL );
    foreach( $quexf_person_class_name::select( $modifier ) as $db_quexf_person )
    {
      // create an entry into the person table
      $db_person = lib::create( 'database\person' );
      $db_person->save();

      // gather information to add to the participant table
      $uid = $participant_class_name::get_new_uid();
      $cohort = '05621101' == $db_quexf_person->barcode ? 'tracking' : 'comprehensive';
      $gender = $db_quexf_person->male ? 'male' : 'female';
      $year = date( 'Y' );
      if( $db_quexf_person->a45_49 ) $dob = sprintf( '%d-01-01', $year - 47 );
      else if( $db_quexf_person->a50_54 ) $dob = sprintf( '%d-01-01', $year - 52 );
      else if( $db_quexf_person->a55_59 ) $dob = sprintf( '%d-01-01', $year - 57 );
      else if( $db_quexf_person->a60_64 ) $dob = sprintf( '%d-01-01', $year - 62 );
      else if( $db_quexf_person->a65_69 ) $dob = sprintf( '%d-01-01', $year - 67 );
      else if( $db_quexf_person->a70_74 ) $dob = sprintf( '%d-01-01', $year - 72 );
      else if( $db_quexf_person->a75_79 ) $dob = sprintf( '%d-01-01', $year - 77 );
      else if( $db_quexf_person->a80_85 ) $dob = sprintf( '%d-01-01', $year - 82 );
      else $dob = NULL;
      if( $db_quexf_person->french && !$db_quexf_person->english ) $language = 'fr';
      else if( !$db_quexf_person->french && $db_quexf_person->english ) $language = 'en';
      else $language = NULL;

      // create an entry into the participant table
      $db_participant = lib::create( 'database\participant' );
      $db_participant->person_id = $db_person->id;
      $db_participant->active = true;
      $db_participant->uid = $uid;
      $db_participant->source = 'ministry';
      $db_participant->cohort = $cohort;
      $db_participant->first_name = $db_quexf_person->first_name;
      $db_participant->last_name = $db_quexf_person->last_name;
      $db_participant->gender = $gender;
      $db_participant->date_of_birth = $dob;
      $db_participant->eligible = true;
      $db_participant->status = NULL;
      $db_participant->language = $language;
      $db_participant->no_in_home = false;
      $db_participant->prior_contact_date = NULL;
      $db_participant->email = $db_quexf_person->email;
      $db_participant->save();

      // create an entry into the status table
      //TODO: finish implementing
    }
  }
  
  /**
   * Returns a modifier that restricts a quexf person select query to valid participants only.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param database\modifier $modifier A modifier to add the restrictions to.  If null this method
   *        will create and return new modifier object.
   * @access protected
   * @static
   */
  protected static function get_valid_participant_modifier( $modifier = NULL )
  {
    if( is_null( $modifier ) ) $modifier = lib::create( 'database\modifier' );
    $modifier->where( 'first_name', '!=', NULL );
    $modifier->where( 'first_name', '!=', '' );
    $modifier->where( 'last_name', '!=', NULL );
    $modifier->where( 'last_name', '!=', '' );
    $modifier->where( 'address', '!=', NULL );
    $modifier->where( 'address', '!=', '' );
    $modifier->where( 'city', '!=', NULL );
    $modifier->where( 'city', '!=', '' );
    $modifier->where( 'province', '!=', NULL );
    $modifier->where( 'province', '!=', '' );
    $modifier->where( 'postal_code', '!=', NULL );
    $modifier->where( 'postal_code', '!=', '' );
    // AND (
    $modifier->where_bracket( true );
    // (
    $modifier->where_bracket( true );
    $modifier->where( 'home_phone', '!=', NULL );
    $modifier->where( 'home_phone', '!=', '' );
    // )
    $modifier->where_bracket( false );
    // OR (
    $modifier->where_bracket( true, true );
    $modifier->where( 'cell_phone', '!=', NULL );
    $modifier->where( 'cell_phone', '!=', '' );
    // )
    $modifier->where_bracket( false );
    // )
    $modifier->where_bracket( false );
    $modifier->where( 'male + female', '=', 1 );
    $modifier->where(
      'a45_49 + a50_54 + a55_59 + a60_64 + a65_69 + a70_74 + a75_79 + a80_85', '=', 1 );
    $modifier->where( 'date_filled', '!=', NULL );
    $modifier->where( 'date_filled', '!=', '' );
    // AND (
    $modifier->where_bracket( true );
    $modifier->where( 'barcode', '=', '05621101' ); // tracking
    $modifier->or_where( 'barcode', '=', '09954401' ); // comprehensive
    // )
    $modifier->where_bracket( false );
    $modifier->where( 'new_name', '!=', NULL );
    $modifier->where( 'new_name', '!=', '' );
    return $modifier;
  }

  /**
   * Whether or not Quexf is enabled
   * @var boolean
   * @access protected
   */
  protected $enabled = false;

  /**
   * The base path to quexf
   * @var string
   * @access protected
   */
  protected $base_path = NULL;

  /**
   * The quexf database connection
   * @var database\database
   * @access protected
   */
  protected $database = NULL;

  /**
   * The path to processed contact PDF forms
   * @var string
   * @access protected
   */
  protected $processed_contact_path = NULL;

  /**
   * The path to processed consent PDF forms
   * @var string
   * @access protected
   */
  protected $processed_consent_path = NULL;
}
?>
