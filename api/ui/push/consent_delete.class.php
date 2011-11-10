<?php
/**
 * consent_delete.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\push;
use mastodon\log, mastodon\util;
use mastodon\business as bus;
use mastodon\database as db;
use mastodon\exception as exc;

/**
 * push: consent delete
 * 
 * @package mastodon\ui
 */
class consent_delete extends base_delete
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
      // use the noid argument and remove it from the args input
      $noid = $args['noid'];
      unset( $args['noid'] );

      // make sure there is sufficient information
      if( !is_array( $noid ) ||
          !array_key_exists( 'participant.uid', $noid ) ||
          !array_key_exists( 'consent.event', $noid ) ||
          !array_key_exists( 'consent.date', $noid ) )
        throw new exc\argument( 'noid', $noid, __METHOD__ );

      $db_participant = db\participant::get_unique_record( 'uid', $noid['participant.uid'] );
      if( !$db_participant ) throw new exc\argument( 'noid', $noid, __METHOD__ );
      $consent_mod = new db\modifier();
      $consent_mod->where( 'participant_id', '=', $db_participant->id );
      $consent_mod->where( 'event', '=', $noid['consent.event'] );
      $consent_mod->where( 'date', '=', $noid['consent.date'] );
      $consent_list = db\consent::select( $consent_mod );
      if( 0 == count( $consent_list ) ) throw new exc\argument( 'noid', $noid, __METHOD__ );
      $db_consent = current( $consent_list );
      $args['id'] = $db_consent->id;
    }

    parent::__construct( 'consent', $args );
  }
}
?>
