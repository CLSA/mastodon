<?php
/**
 * note_edit.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Extends the parent class to send machine requests.
 */
class note_edit
  extends \cenozo\ui\push\note_edit
  implements base_participant_base
{
  /**
   * Constructor.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Push arguments
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( $args );
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

    if( 'participant' == $this->get_argument( 'category' ) )
    {
      $db_person_note = lib::create( 'database\person_note', $this->get_argument( 'id' ) );
      $this->set_participant_for_machine_requests(
        $db_person_note->get_person()->get_participant() );
    }
  }

  /**
   * Sets up the machine request url before calling the parent class' setup() method
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function setup()
  {
    // only send a machine request if the participant has been synched
    $this->set_machine_request_enabled(
      !is_null( $db_participant_for_machine_requests ) &&
      !is_null( $db_participant_for_machine_requests->sync_datetime ) );

    // send the request to the participant's primary site's service
    $this->set_machine_request_url(
      !is_null( $db_participant_for_machine_requests ) ?
      $db_participant_for_machine_requests->get_primary_site()->get_service()->get_url() : NULL );

    parent::setup();
  }

  /**
   * Override parent method to handle the note category
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args An argument list, usually those passed to the push operation.
   * @return array
   * @access protected
   */
  protected function convert_to_noid( $args )
  {
    $category = $args['category'];
    $category_class_name = lib::get_class_name( 'database\\'.$category );
    $get_category_method_name = sprintf( 'get_%s', $category );
    $category_key_name = sprintf( '%s_id', $category );
    $category_note_name = sprintf( '%s_note', $category );

    // temporarily set the category to person, convert, then change back
    $args['category'] = 'person';
    $args = parent::convert_to_noid( $args );
    $args['category'] = $category;
    
    // convert from person_note to <category>_note
    unset( $args['noid']['person_note']['person_id'] );
    $args['noid'][$category_note_name] = $args['noid']['person_note'];
    unset( $args['noid']['person_note'] );

    $db_person_note = lib::create( 'database\person_note', $this->get_argument( 'id' ) );
    $record = $db_person_note->get_person()->$get_category_method_name();
    $args['noid'][$category_note_name][$category_key_name] =
      $category_class_name::get_unique_from_primary_key( $record->id );

    return $args;
  }

  /**
   * Override parent method to handle the note category
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args An argument list, usually those passed to the push operation.
   * @return array
   * @access protected
   */
  protected function convert_from_noid( $args )
  {
    if( array_key_exists( 'noid', $args ) )
    {
      if( array_key_exists( 'participant_note', $args['noid'] ) )
      {
        // replace the participant unique key with a person primary key
        $participant_class_name = lib::get_class_name( 'database\participant' );
        $participant_id = $participant_class_name::get_primary_from_unique_key(
          $args['noid']['participant_note']['participant_id'] );
        $db_participant = lib::create( 'database\participant', $participant_id );
        $args['category'] = 'person';
        $args['noid']['person_note'] = $args['noid']['participant_note'];
        unset( $args['noid']['participant_note'] );
        $args['noid']['person_note']['person_id'] = $db_participant->get_person()->id;
        unset( $args['noid']['person_note']['participant_id'] );
      }
    }

    return parent::convert_from_noid( $args );
  }

  /**
   * Define the participant record which should be used for determining whether to
   * sync the data and with which external services.
   * In order for data to be passed to external services this method must be called
   * in the implementing class' prepare() method.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  public function set_participant_for_machine_requests( $db_participant )
  {
    $this->db_participant_for_machine_requests = $db_participant;
  }

  /**
   * The participant record used to determine whether to sync the data and with which
   * external services
   * @var database\participant
   * @access private
   */
  private $db_participant_for_machine_requests = NULL;
}
?>
