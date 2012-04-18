<?php
/**
 * participant_edit.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: participant edit
 *
 * Edit a participant.
 * @package mastodon\ui
 */
class participant_edit extends \cenozo\ui\push\base_edit
{
  /**
   * Constructor.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Push arguments
   * @access public
   */
  public function __construct( $args )
  {
    if( array_key_exists( 'noid', $args ) )
    {
      $participant_class_name = lib::get_class_name( 'database\participant' );
      $site_class_name = lib::get_class_name( 'database\site' );

      // use the noid argument and remove it from the args input
      $noid = $args['noid'];
      unset( $args['noid'] );

      // make sure there is sufficient information
      if( !is_array( $noid ) ||
          !array_key_exists( 'participant.uid', $noid ) )
        throw lib::create( 'exception\argument', 'noid', $noid, __METHOD__ );
      
      $db_participant = $participant_class_name::get_unique_record( 'uid', $noid['participant.uid'] );
      if( !$db_participant ) throw lib::create( 'exception\argument', 'noid', $noid, __METHOD__ );
      $args['id'] = $db_participant->id;

      if( is_array( $noid ) &&
          array_key_exists( 'site.name', $noid ) &&
          array_key_exists( 'site.cohort', $noid ) )
      {
        $db_site = $site_class_name::get_unique_record(
          array( 'name', 'cohort' ),
          array( $noid['site.name'], $noid['site.cohort'] ) );
        $args['columns']['site_id'] = $db_site->id;
      }
    }

    parent::__construct( 'participant', $args );
  }
}
?>
