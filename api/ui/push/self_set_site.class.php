<?php
/**
 * self_set_site.class.php
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
 * push: self set_site
 *
 * Changes the current user's site.
 * Arguments must include 'site'.
 * @package mastodon\ui
 */
class self_set_site extends \mastodon\ui\push
{
  /**
   * Constructor.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Push arguments
   * @access public
   */
  public function __construct( $args )
  {
    // if the name and cohort is provided instead of the id then fetch the site id
    if( array_key_exists( 'noid', $args ) )
    {
      // use the noid argument and remove it from the args input
      $noid = $args['noid'];
      unset( $args['noid'] );
      
      // make sure there is sufficient information
      if( !is_array( $noid ) ||
          !array_key_exists( 'site.name', $noid ) ||
          !array_key_exists( 'site.cohort', $noid ) )
        throw new exc\argument( 'noid', $noid, __METHOD__ );
      $db_site = db\site::get_unique_record(
        array( 'name', 'cohort' ),
        array( $noid['site.name'], $noid['site.cohort'] ) );

      if( !$db_site ) throw new exc\argument( 'noid', $noid, __METHOD__ );
      $args['id'] = $db_site->id;
    }

    parent::__construct( 'self', 'set_site', $args );
  }
  
  /**
   * Executes the push.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @throws exception\runtime
   * @access public
   */
  public function finish()
  {
    try
    {
      $db_site = new db\site( $this->get_argument( 'id' ) );
    }
    catch( exc\runtime $e )
    {
      throw new exc\argument( 'id', $this->get_argument( 'id' ), __METHOD__, $e );
    }
    
    $session = bus\session::self();
    $db_user = $session->get_user();
    $db_role = NULL;
    
    $role_mod = new db\modifier();
    $role_mod->where( 'site_id', '=', $db_site->id );
    $role_list = $db_user->get_role_list( $role_mod );
    if( 0 == count( $role_list ) )
      throw new exc\runtime(
        'User does not have access to the given site.',  __METHOD__ );
    
    // try loading the same role as the last time this site was accessed
    $activity_mod = new db\modifier();
    $activity_mod->where( 'user_id', '=', $db_user->id );
    $activity_mod->where( 'site_id', '=', $db_site->id );
    $activity_mod->order_desc( 'datetime' );
    $activity_mod->limit( 1 );
    $db_activity = current( db\activity::select( $activity_mod ) );
    if( $db_activity )
    {
      // make sure the user still has access to the site/role
      $role_mod = new db\modifier();
      $role_mod->where( 'site_id', '=', $db_activity->site_id );
      $role_mod->where( 'role_id', '=', $db_activity->role_id );
      $db_role = current( $db_user->get_role_list( $role_mod ) );
    }
    
    // if we don't have a role then get the first role associated with the site
    if( !$db_role ) $db_role = current( $role_list );

    // get the first role associated with the site
    $session->set_site_and_role( $db_site, $db_role );
  }
}
?>
