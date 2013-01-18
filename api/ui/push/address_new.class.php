<?php
/**
 * address_new.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: address new
 *
 * Create a new address.
 */
class address_new extends base_participant_new
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

    $columns = $this->get_argument( 'columns' );
    $db_person = lib::create( 'database\person', $columns['person_id'] );
    $this->set_participant_for_machine_requests( $db_person->get_participant() );
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
    
    // validate the postcode
    if( !preg_match( '/^[A-Z][0-9][A-Z] [0-9][A-Z][0-9]$/', $columns['postcode'] ) && // postal code
        !preg_match( '/^[0-9]{5}$/', $columns['postcode'] ) ) // zip code
      throw lib::create( 'exception\notice',
        'Postal codes must be in "A1A 1A1" format, zip codes in "01234" format.', __METHOD__ );

    $postcode_class_name = lib::get_class_name( 'database\postcode' );
    $db_postcode = $postcode_class_name::get_match( $columns['postcode'] );
    if( is_null( $db_postcode ) ) 
      throw lib::create( 'exception\notice',
        'The postcode is invalid and cannot be used.', __METHOD__ );
  }

  /**
   * This method executes the operation's purpose.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function execute()
  {
    $columns = $this->get_argument( 'columns' );
    $this->get_record()->postcode = $columns['postcode'];
    $this->get_record()->source_postcode();

    parent::execute();
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
