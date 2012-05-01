<?php
/**
 * address_new.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: address new
 *
 * Create a new address.
 * @package mastodon\ui
 */
class address_new extends \cenozo\ui\push\base_new
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
    $columns = $this->get_argument( 'columns' );
    $db_person = lib::create( 'database\person', $columns['person_id'] );
    $db_participant = $db_person->get_participant();
    $this->set_machine_request_enabled( !is_null( $db_participant ) &&
                                        !is_null( $db_participant->sync_datetime ) );
    $this->set_machine_request_url( !is_null( $db_participant )
         ? ( 'comprehensive' == $db_participant->cohort ? BEARTOOTH_URL : SABRETOOTH_URL )
         : NULL );
  }

  /**
   * Overrides the parent method to make sure the postcode is valid.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @throws exception\notice
   * @access public
   */
  public function finish()
  {
    $columns = $this->get_argument( 'columns' );
    $postcode = $columns['postcode'];
    
    // validate the postcode
    if( !preg_match( '/^[A-Z][0-9][A-Z] [0-9][A-Z][0-9]$/', $postcode ) && // postal code
        !preg_match( '/^[0-9]{5}$/', $postcode ) ) // zip code
      throw lib::create( 'exception\notice',
        'Postal codes must be in "A1A 1A1" format, zip codes in "01234" format.', __METHOD__ );

    // determine the region, timezone and daylight savings from the postcode
    $this->get_record()->postcode = $postcode;
    $this->get_record()->source_postcode();

    parent::finish();
  }

  /**
   * Overrides the parent method to make sure the postcode is valid.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @throws exception\notice
   * @access public
   */
  protected function convert_to_noid( $args )
  {
    // replace person id with participant id
    $person_id = $args['columns']['person_id'];
    unset( $args['columns']['person_id'] );
    $db_person = lib::create( 'database\person', $person_id );
    $db_participant = $db_person->get_participant();
    if( is_null( $db_participant ) )
      throw lib::create( 'exception\runtime',
        sprintf( 'Tried to convert person id %d to participant but person is not a participant.',
          $person_id ),
        __METHOD__ );

    $args['columns']['participant_id'] = $db_participant->id;
    return parent::convert_to_noid( $args );
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
    $args = parent::convert_from_noid( $args );

    // replace the participant id with a person id
    $participant_id = $args['columns']['participant_id'];
    unset( $args['columns']['participant_id'] );
    $db_participant = lib::create( 'database\participant', $participant_id );
    if( is_null( $db_participant ) )
      throw lib::create( 'exception\runtime',
        sprintf( 'Participant id %d not found when receiving machine request.',
          $participant_id ),
        __METHOD__ );
    
    $args['columns']['person_id'] = $db_participant->person_id;
    return $args;
  }
}
?>
