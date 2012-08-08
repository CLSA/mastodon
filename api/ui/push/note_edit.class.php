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
class note_edit extends \cenozo\ui\push\note_edit
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

    // only send a machine request if the participant has been synched
    if( 'participant' == $this->get_argument( 'category' ) )
    {
      $db_person_note = lib::create( 'database\person_note', $this->get_argument( 'id' ) );
      $db_participant = $db_person_note->get_person()->get_participant();
      $this->set_machine_request_enabled( !is_null( $db_participant ) &&
                                          !is_null( $db_participant->sync_datetime ) );
      $this->set_machine_request_url( !is_null( $db_participant )
           ? ( 'comprehensive' == $db_participant->cohort ? BEARTOOTH_URL : SABRETOOTH_URL )
           : NULL );
    }
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
}
?>
