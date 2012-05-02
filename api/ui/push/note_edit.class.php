<?php
/**
 * note_edit.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, cenozo\util;

/**
 * Extends the parent class to send machine requests.
 * @package mastodon\ui
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
    $this->set_machine_request_enabled( true );
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
        // convert the participant_note to a person_note
        $uid = $args['noid']['participant_note']['participant_id']['uid'];
        $participant_class_name = lib::get_class_name( 'database\participant' );
        $db_participant = $participant_class_name::get_unique_record( 'uid', $uid );
        if( is_null( $db_participant ) )
          throw lib::create( 'exception\runtime',
            sprintf( 'Participant UID "%s" not found.', $uid ), __METHOD__ );

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
