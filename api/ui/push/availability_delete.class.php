<?php
/**
 * availability_delete.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: availability delete
 * 
 * @package mastodon\ui
 */
class availability_delete extends \cenozo\ui\push\base_delete
{
  /**
   * Constructor.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Push arguments
   * @access public
   */
  public function __construct( $args )
  {
    if( array_key_exists( 'noid', $args ) )
    {
      // use the noid argument and remove it from the args input
      $noid = $args['noid'];
      unset( $args['noid'] );

      // make sure there is sufficient information
      if( !is_array( $noid ) ||
          !array_key_exists( 'participant.uid', $noid ) ||
          !array_key_exists( 'availability.monday', $noid ) ||
          !array_key_exists( 'availability.tuesday', $noid ) ||
          !array_key_exists( 'availability.wednesday', $noid ) ||
          !array_key_exists( 'availability.thursday', $noid ) ||
          !array_key_exists( 'availability.friday', $noid ) ||
          !array_key_exists( 'availability.saturday', $noid ) ||
          !array_key_exists( 'availability.sunday', $noid ) ||
          !array_key_exists( 'availability.start_time', $noid ) ||
          !array_key_exists( 'availability.end_time', $noid ) )
        throw lib::create( 'exception\argument', 'noid', $noid, __METHOD__ );

      $participant_class_name = lib::get_class_name( 'database\participant' );
      $db_participant = $participant_class_name::get_unique_record( 'uid', $noid['participant.uid'] );
      if( !$db_participant ) throw lib::create( 'exception\argument', 'noid', $noid, __METHOD__ );

      $availability_mod = lib::create( 'database\modifier' );
      $availability_mod->where( 'participant_id', '=', $db_participant->id );
      $availability_mod->where( 'monday', '=', $noid['availability.monday'] );
      $availability_mod->where( 'tuesday', '=', $noid['availability.tuesday'] );
      $availability_mod->where( 'wednesday', '=', $noid['availability.wednesday'] );
      $availability_mod->where( 'thursday', '=', $noid['availability.thursday'] );
      $availability_mod->where( 'friday', '=', $noid['availability.friday'] );
      $availability_mod->where( 'saturday', '=', $noid['availability.saturday'] );
      $availability_mod->where( 'sunday', '=', $noid['availability.sunday'] );
      $availability_mod->where( 'start_time', '=', $noid['availability.start_time'] );
      $availability_mod->where( 'end_time', '=', $noid['availability.end_time'] );

      $availability_class_name = lib::get_class_name( 'database\availability' );
      $availability_list = $availability_class_name::select( $availability_mod );
      // there (legitimately) be more than one matching availability, so don't check for count == 1)
      $db_availability = current( $availability_list );
      $args['id'] = $db_availability->id;
    }

    parent::__construct( 'availability', $args );
  }
}
?>
