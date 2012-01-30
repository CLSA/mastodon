<?php
/**
 * participant_delete_phone.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: participant delete_phone
 * 
 * @package mastodon\ui
 */
class participant_delete_phone extends \cenozo\ui\push\base_delete_record
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
          !array_key_exists( 'phone.rank', $noid ) )
        throw lib::create( 'exception\argument', 'noid', $noid, __METHOD__ );
 
      $participant_class_name = lib::get_class_name( 'database\participant' );
      $db_participant = $participant_class_name::get_unique_record( 'uid', $noid['participant.uid'] );
      if( !$db_participant ) throw lib::create( 'exception\argument', 'noid', $noid, __METHOD__ );
      $args['id'] = $db_participant->id;

      $phone_class_name = lib::get_class_name( 'database\phone' );
      $db_phone = $phone_class_name::get_unique_record(
        array( 'person_id', 'rank' ),
        array( $db_participant->person_id, $noid['phone.rank'] ) );
      if( !$db_phone ) throw lib::create( 'exception\argument', 'noid', $noid, __METHOD__ );
      $args['remove_id'] = $db_phone->id;
    }

    parent::__construct( 'participant', 'phone', $args );
  }
}
?>
