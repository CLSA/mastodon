<?php
/**
 * participant_multinote.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: participant multinote
 *
 * Syncs participant information between Sabretooth and Mastodon
 */
class participant_multinote extends \cenozo\ui\push
{
  /**
   * Constructor.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Push arguments
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'participant', 'multinote', $args );
  }

  /**
   * This method executes the operation's purpose.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function execute()
  {
    parent::execute();

    $participant_class_name = lib::get_class_name( 'database\participant' );

    $note = $this->get_argument( 'note' );

    $uid_list_string = preg_replace( '/[^a-zA-Z0-9]/', ' ', $this->get_argument( 'uid_list' ) );
    $uid_list_string = trim( $uid_list_string );
    $uid_list = array_unique( preg_split( '/\s+/', $uid_list_string ) );

    foreach( $uid_list as $uid )
    {
      // determine the participant record and make sure it is valid
      $db_participant = $participant_class_name::get_unique_record( 'uid', $uid );

      if( !is_null( $db_participant ) )
      {
        $args = array( 'category' => 'participant',
                       'category_id' => $db_participant->id,
                       'note' => $note );
        $operation = lib::create( 'ui\push\note_new', $args );
        $operation->process();
      }
    }
  }
}
?>
