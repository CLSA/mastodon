<?php
/**
 * address_delete.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: address delete
 * 
 * @package mastodon\ui
 */
class address_delete extends \cenozo\ui\push\base_delete
{
  /**
   * Constructor.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Push arguments
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'address', $args );

    // only send a machine request if the participant has been synched
    $db_participant = $this->get_record()->get_person()->get_participant();
    $this->set_machine_request_enabled( !is_null( $db_participant ) &&
                                        !is_null( $db_participant->sync_datetime ) );
    $this->set_machine_request_url( !is_null( $db_participant )
         ? ( 'comprehensive' == $db_participant->cohort ? BEARTOOTH_URL : SABRETOOTH_URL )
         : NULL );
  }

  // TODO: document
  protected function convert_to_noid( $args )
  {
    $args = parent::convert_to_noid( $args );

    // replace person key with participant key
    $person_id = $args['noid']['address']['person_id'];
    unset( $args['noid']['address']['person_id'] );
    $participant_class_name = lib::get_class_name( 'database\participant' );
    $db_participant = $this->get_record()->get_person()->get_participant();
    $args['noid']['address']['participant_id'] =
      $participant_class_name::get_unique_from_primary_key( $db_participant->id );

    return $args;
  }

  // TODO: document
  protected function convert_from_noid( $args )
  {
    if( array_key_exists( 'noid', $args ) ) 
    {   
      // replace the participant key with a person key
      $uid = $args['noid']['address']['participant_id']['uid'];
      unset( $args['noid']['address']['participant_id'] );
  
      $participant_class_name = lib::get_class_name( 'database\participant' );
      $db_participant = $participant_class_name::get_unique_record( 'uid', $uid );
      if( is_null( $db_participant ) ) 
        throw lib::create( 'exception\argument',
          'args[noid][address][participant_id][uid]', $uid, __METHOD__ );
  
      $args['noid']['address']['person_id'] = $db_participant->person_id;
    }   

    return parent::convert_from_noid( $args );
  }
}
?>
