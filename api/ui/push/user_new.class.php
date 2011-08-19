<?php
/**
 * user_new.class.php
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
 * push: user new
 *
 * Create a new user.
 * @package mastodon\ui
 */
class user_new extends base_new
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
          !array_key_exists( 'role.name', $noid ) ||
          !array_key_exists( 'site.name', $noid ) ||
          !array_key_exists( 'site.cohort', $noid ) )
        throw new exc\argument( 'noid', $noid, __METHOD__ );

      $db_role = db\role::get_unique_record( 'name', $noid['role.name'] );
      if( !$db_role ) throw new exc\argument( 'noid', $noid, __METHOD__ );
      $this->role_id = $db_role->id;

      $db_site = db\site::get_unique_record(
        array( 'name', 'cohort' ),
        array( $noid['site.name'], $noid['site.cohort'] ) );
      if( !$db_site ) throw new exc\argument( 'noid', $noid, __METHOD__ );
      $this->site_id = $db_site->id;
    }

    parent::__construct( 'user', $args );
  }

  /**
   * Executes the push.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   * @throws exception\notice
   */
  public function finish()
  {
    $columns = $this->get_argument( 'columns' );
    
    // make sure the name, first name and last name are not blank
    if( !array_key_exists( 'name', $columns ) || 0 == strlen( $columns['name'] ) )
      throw new exc\notice( 'The participant\'s user name cannot be left blank.', __METHOD__ );
    if( !array_key_exists( 'first_name', $columns ) || 0 == strlen( $columns['first_name'] ) )
      throw new exc\notice( 'The participant\'s first name cannot be left blank.', __METHOD__ );
    if( !array_key_exists( 'last_name', $columns ) || 0 == strlen( $columns['last_name'] ) )
      throw new exc\notice( 'The participant\'s last name cannot be left blank.', __METHOD__ );

    // add the user to ldap
    $ldap_manager = bus\ldap_manager::self();
    try
    {
      $ldap_manager->new_user(
        $columns['name'], $columns['first_name'], $columns['last_name'], 'password' );
    }
    catch( exc\ldap $e )
    {
      // catch already exists exceptions, no need to report them
      if( !$e->is_already_exists() ) throw $e;
    }

    parent::finish();

    if( !is_null( $this->site_id ) && !is_null( $this->role_id ) )
    { // add the initial role to the new user
      $db_user = db\user::get_unique_record( 'name', $columns['name'] );
      $db_access = new db\access();
      $db_access->user_id = $db_user->id;
      $db_access->site_id = $this->site_id;
      $db_access->role_id = $this->role_id;
      $db_access->save();
    }
  }

  /**
   * The initial site to give the new user access to
   * @var int
   * @access protected
   */
  protected $site_id = NULL;

  /**
   * The initial role to give the new user
   * @var int
   * @access protected
   */
  protected $role_id = NULL;
}
?>
