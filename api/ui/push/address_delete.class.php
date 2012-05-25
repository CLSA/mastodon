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
  }

  /**
   * Processes arguments, preparing them for the operation.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function prepare()
  {
    parent::prepare();

    // only send a machine request if the participant has been synched
    $db_participant = $this->get_record()->get_person()->get_participant();
    $this->set_machine_request_enabled( !is_null( $db_participant ) &&
                                        !is_null( $db_participant->sync_datetime ) );
    $this->set_machine_request_url( !is_null( $db_participant )
         ? ( 'comprehensive' == $db_participant->cohort ? BEARTOOTH_URL : SABRETOOTH_URL )
         : NULL );
  }

  /**
   * Override the parent method to replace the person key with a participant key.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args An argument list, usually those passed to the push operation.
   * @return array
   * @access protected
   */
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

  /**
   * Override the parent method to replace the participant key with a person key.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args An argument list, usually those passed to the push operation.
   * @return array
   * @access protected
   */
  protected function convert_from_noid( $args )
  {
    if( array_key_exists( 'noid', $args ) ) 
    {   
      // replace the participant unique key with a person primary key
      $participant_class_name = lib::get_class_name( 'database\participant' );
      $participant_id = $participant_class_name::get_primary_from_unique_key(
        $args['noid']['address']['participant_id'] );
      unset( $args['noid']['address']['participant_id'] );
      $db_participant = lib::create( 'database\participant', $participant_id );
      $args['noid']['address']['person_id'] = $db_participant->person_id;
    }   

    return parent::convert_from_noid( $args );
  }
}
?>
