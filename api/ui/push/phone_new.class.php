<?php
/**
 * phone_new.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: phone new
 *
 * Create a new phone.
 */
class phone_new extends \cenozo\ui\push\base_new
{
  /**
   * Constructor.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Push arguments
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'phone', $args );
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
   * Validate the operation.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @throws exception\notice
   * @access protected
   */
  protected function validate()
  {
    parent::validate();

    $columns = $this->get_argument( 'columns' );

    // make sure the number column isn't blank
    if( !array_key_exists( 'number', $columns ) )
      throw lib::create( 'exception\notice', 'The number cannot be left blank.', __METHOD__ );

    // validate the phone number
    $number_only = preg_replace( '/[^0-9]/', '', $columns['number'] );
    if( 10 != strlen( $number_only ) )
      throw lib::create( 'exception\notice',
        'Phone numbers must have exactly 10 digits.', __METHOD__ );

    $formatted_number = sprintf( '%s-%s-%s',
                                 substr( $number_only, 0, 3 ),
                                 substr( $number_only, 3, 3 ),
                                 substr( $number_only, 6 ) );
    if( !util::validate_phone_number( $formatted_number ) )
      throw lib::create( 'exception\notice',
        sprintf( 'The provided number "%s" is not a valid North American phone number.',
                 $formatted_number ),
        __METHOD__ );
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

    if( array_key_exists( 'participant_id', $args['columns'] ) )
    {
      // replace the participant id with a person id
      $participant_id = $args['columns']['participant_id'];
      unset( $args['columns']['participant_id'] );
      $db_participant = lib::create( 'database\participant', $participant_id );
      $args['columns']['person_id'] = $db_participant->person_id;
    }

    return $args;
  }
}
?>
