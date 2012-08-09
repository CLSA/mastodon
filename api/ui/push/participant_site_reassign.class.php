<?php
/**
 * participant_site_reassign.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: participant site_reassign
 *
 * Syncs participant information between Sabretooth and Mastodon
 */
class participant_site_reassign extends \cenozo\ui\push
{
  /**
   * Constructor.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Push arguments
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'participant', 'site_reassign', $args );
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

    $site_id = $this->get_argument( 'site_id' );
    $db_site = 0 < $site_id ? lib::create( 'database\site', $site_id ) : NULL;

    $uid_list_string = preg_replace( '/[^a-zA-Z0-9]/', ' ', $this->get_argument( 'uid_list' ) );
    $uid_list_string = trim( $uid_list_string );
    $uid_list = array_unique( preg_split( '/\s+/', $uid_list_string ) );

    foreach( $uid_list as $uid )
    {
      // determine the participant record and make sure it is valid
      $db_participant = $participant_class_name::get_unique_record( 'uid', $uid );

      if( !is_null( $db_participant ) &&
          ( is_null( $db_site ) || $db_participant->cohort == $db_site->cohort ) )
      {
        $site_id = is_null( $db_site ) ? NULL : $db_site->id;
        $args = array( 'id' => $db_participant->id,
                       'columns' => array( 'site_id' => $site_id ) );
        $operation = lib::create( 'ui\push\participant_edit', $args );
        $operation->process();
      }
    }
  }
}
?>
