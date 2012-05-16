<?php
/**
 * phone_edit.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: phone edit
 *
 * Edit a phone.
 * @package mastodon\ui
 */
class phone_edit extends \cenozo\ui\push\base_edit
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

  // TODO: document
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

  // TODO: document
  public function validate()
  {
    parent::validate();

    $columns = $this->get_argument( 'columns' );

    // if there is a phone number, validate it
    if( array_key_exists( 'number', $columns ) )
    {
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
    $person_id = $args['noid']['phone']['person_id'];
    unset( $args['noid']['phone']['person_id'] );
    $participant_class_name = lib::get_class_name( 'database\participant' );
    $db_participant = $this->get_record()->get_person()->get_participant();
    $args['noid']['phone']['participant_id'] =
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
        $args['noid']['phone']['participant_id'] );
      unset( $args['noid']['phone']['participant_id'] );
      $db_participant = lib::create( 'database\participant', $participant_id );
      $args['noid']['phone']['person_id'] = $db_participant->person_id;
    }

    return parent::convert_from_noid( $args );
  }
}
?>
