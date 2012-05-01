<?php
/**
 * address_edit.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: address edit
 *
 * Edit a address.
 * @package mastodon\ui
 */
class address_edit extends \cenozo\ui\push\base_edit
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

  /**
   * Overrides the parent method to make sure the postcode is valid.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @throws exception\notice
   * @access public
   */
  public function finish()
  {
    $columns = $this->get_argument( 'columns' );

    // validate the postcode
    if( array_key_exists( 'postcode', $columns ) )
    {
      $postcode = $columns['postcode'];
      if( !preg_match( '/^[A-Z][0-9][A-Z] [0-9][A-Z][0-9]$/', $postcode ) && // postal code
          !preg_match( '/^[0-9]{5}$/', $postcode ) )  // zip code
        throw lib::create( 'exception\notice',
          'Postal codes must be in "A1A 1A1" format, zip codes in "01234" format.', __METHOD__ );
      
      // determine the region, timezone and daylight savings from the postcode
      $this->get_record()->postcode = $postcode;
      $this->get_record()->source_postcode();
    }

    parent::finish();
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
