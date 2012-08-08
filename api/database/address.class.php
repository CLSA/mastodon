<?php
/**
 * address.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\database;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * address: record
 */
class address extends \cenozo\database\has_rank
{
  /**
   * Sets the region, timezone offset and daylight savings columns based on the postcode.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function source_postcode()
  {
    $postcode_class_name = lib::get_class_name( 'database\postcode' );
    if( !is_null( $this->postcode ) )
    {
      $db_postcode = $postcode_class_name::get_match( $this->postcode );
      if( !is_null( $db_postcode ) )
      {
        $this->region_id = $db_postcode->region_id;
        $this->timezone_offset = $db_postcode->timezone_offset;
        $this->daylight_savings = $db_postcode->daylight_savings;
      }
    }
  }

  /**
   * Determines the difference in hours between the user's timezone and the address's timezone
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @return float (NULL if it is not possible to get the time difference)
   * @access public
   */
  public function get_time_diff()
  {
    // get the user's timezone differential from UTC
    $user_offset = util::get_datetime_object()->getOffset() / 3600;

    // determine if we are currently under daylight savings
    $summer_offset = util::get_datetime_object( '2000-07-01' )->getOffset() / 3600;
    $under_daylight_savings = $user_offset == $summer_offset;

    if( !is_null( $this->timezone_offset ) && !is_null( $this->daylight_savings ) )
    {
      $offset = $this->timezone_offset;
      if( $under_daylight_savings && $this->daylight_savings ) $offset += 1;
      return $offset - $user_offset;
    }

    // if we get here then there is no way to get the time difference
    return NULL;
  }

  /**
   * Determines if the address is valid by making sure all address-based manditory fields
   * are filled and checking for postcode-region mismatches.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @return boolean
   * @access public
   */
  public function is_valid()
  {
    // make sure all mandatory address-based fields are filled in
    if( is_null( $this->address1 ) ||
        is_null( $this->city ) ||
        is_null( $this->region_id ) ||
        is_null( $this->postcode ) ) return false;

    // look up the postal code for the correct region
    $postcode_class_name = lib::get_class_name( 'database\postcode' );
    $db_postcode = $postcode_class_name::get_match( $this->postcode );
    if( is_null( $db_postcode ) ) return NULL;
    return $db_postcode->region_id == $this->region_id;
  }

  /** 
   * If the owner is a participant then refer to it instead of the person record.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param int $key A primary key value for the table.
   * @return associative array
   * @static
   * @access public
   */
  public static function get_unique_from_primary_key( $key )
  {
    $unique_key_array = parent::get_unique_from_primary_key( $key );

    $record = new static( $key );
    $db_person = $record->get_person();
    if( !is_null( $db_person ) )
    {
      $db_participant = $db_person->get_participant();
      if( !is_null( $db_participant ) )
      {
        $participant_class_name = lib::get_class_name( 'database\participant' );
        $unique_key_array['participant_id'] =
          $participant_class_name::get_unique_from_primary_key( $db_participant->id );
        unset( $unique_key_array['person_id'] );
      }
    }

    return $unique_key_array;
  }

  /**
   * Replace participant_id with person_id in unique key
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param associative array
   * @return int
   * @static
   * @access public
   */
  public static function get_primary_from_unique_key( $key )
  {
    // we may have a stdObject, so convert to an array if we do
    if( is_object( $key ) ) $key = (array) $key;
    if( !is_array( $key ) ) return NULL;

    if( array_key_exists( 'participant_id', $key ) )
    {
      $participant_class_name = lib::get_class_name( 'database\participant' );
      $db_participant = lib::create( 'database\participant',
        $participant_class_name::get_primary_from_unique_key( $key['participant_id'] ) );
      $key['person_id'] = !is_null( $db_participant ) ? $db_participant->person_id : NULL;
      unset( $key['participant_id'] );
    }

    return parent::get_primary_from_unique_key( $key );
  }

  /**
   * The type of record which the record has a rank for.
   * @var string
   * @access protected
   * @static
   */
  protected static $rank_parent = 'person';
}
?>
