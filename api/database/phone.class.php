<?php
/**
 * phone.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\database;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * phone: record
 */
class phone extends \cenozo\database\has_rank
{
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
    $db_participant = $record->get_person()->get_participant();
    if( !is_null( $db_participant ) ) 
    {   
      $participant_class_name = lib::get_class_name( 'database\participant' );
      $unique_key_array['participant_id'] =
        $participant_class_name::get_unique_from_primary_key( $db_participant->id );
      unset( $unique_key_array['person_id'] );
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
