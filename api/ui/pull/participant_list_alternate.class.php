<?php
/**
 * participant_list_alternate.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\pull;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * pull: participant list alternate
 */
class participant_list_alternate extends \cenozo\ui\pull\base_list_record
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
    parent::__construct( 'participant', 'alternate', $args );
  }

  /**
   * Processes arguments, preparing them for the operation.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function prepare()
  {
    // if the uid is provided instead of the id then fetch the participant id based on the uid
    // NOTE: this must be done before calling the parent prepare() method
    if( isset( $this->arguments['uid'] ) )
    {
      $class_name = lib::get_class_name( 'database\participant' );
      $db_participant = $class_name::get_unique_record( 'uid', $this->arguments['uid'] );

      if( is_null( $db_participant ) )
        throw lib::create( 'exception\argument', 'uid', $this->arguments['uid'], __METHOD__ );

      // make sure not to mix up comprehensive and tracking participants
      if( $db_participant->cohort != lib::create( 'business\session' )->get_site()->cohort )
        throw lib::create( 'exception\runtime',
          'Tried to get participant from wrong cohort.', __METHOD__ );

      $this->arguments['id'] = $db_participant->id;
    }

    parent::prepare();
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

    foreach( $this->get_record()->get_alternate_list() as $index => $db_alternate )
    {
      // add the alternate's first phone number
      foreach( $db_alternate->get_phone_list() as $db_phone )
      {
        $this->data[$index]['phone_list'][] = array(
          'active' => $db_phone->active,
          'rank' => $db_phone->rank,
          'type' => $db_phone->type,
          'number' => $db_phone->number,
          'note' => $db_phone->note );
      }
    }
  }
}
?>
