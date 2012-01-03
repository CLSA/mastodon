<?php
/**
 * participant_list_consent.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\pull;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * pull: participant list consent
 * 
 * @package mastodon\ui
 */
class participant_list_consent extends \cenozo\ui\pull\base_list_record
{
  /**
   * Constructor
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Pull arguments.
   * @access public
   */
  public function __construct( $args )
  {
    // if the uid is provided instead of the id then fetch the participant id based on the uid
    if( isset( $args['uid'] ) )
    {
      $db_participant = db\participant::get_unique_record( 'uid', $args['uid'] );

      if( is_null( $db_participant ) )
        throw lib::create( 'exception\argument', 'uid', $args['uid'], __METHOD__ );
      $args['id'] = $db_participant->id;
    }

    parent::__construct( 'participant', 'consent', $args );
  }
}
?>
