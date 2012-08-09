<?php
/**
 * user_add.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget user add
 */
class user_add extends \cenozo\ui\widget\user_add
{
  /**
   * Sets up the operation with any pre-execution instructions that may be necessary.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function setup()
  {
    parent::setup();
    
    $role_class_name = lib::get_class_name( 'database\role' );
    $site_class_name = lib::get_class_name( 'database\site' );

    $session = lib::create( 'business\session' );
    $is_top_tier = 3 == $session->get_role()->tier;

    // create enum arrays
    $modifier = lib::create( 'database\modifier' );
    $modifier->where( 'tier', '<=', $session->get_role()->tier );
    $modifier->where( 'name', 'IN', array( 'administrator', 'typist' ) );
    $roles = array();
    foreach( $role_class_name::select( $modifier ) as $db_role )
      $roles[$db_role->id] = $db_role->name;

    // re-create the site enum array to include cohort
    $sites = array();
    if( $is_top_tier )
    {
      $site_mod = lib::create( 'database\modifier' );
      $site_mod->order( 'cohort' );
      $site_mod->order( 'name' );
      foreach( $site_class_name::select( $site_mod ) as $db_site )
        $sites[$db_site->id] = sprintf( '%s (%s)', $db_site->name, $db_site->cohort );
    }

    $value = $is_top_tier ? current( $sites ) : $session->get_site()->id;
    $this->set_item( 'site_id', $value, true, $is_top_tier ? $sites : NULL );
    $this->set_item( 'role_id', current( $roles ), true, $roles );
  }
}
?>
