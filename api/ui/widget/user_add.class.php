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
    $session = lib::create( 'business\session' );

    // create enum arrays
    $modifier = lib::create( 'database\modifier' );
    $modifier->where( 'tier', '<=', $session->get_role()->tier );
    $modifier->where( 'name', 'IN', array( 'administrator', 'typist' ) );
    $roles = array();
    foreach( $role_class_name::select( $modifier ) as $db_role )
      $roles[$db_role->id] = $db_role->name;

    $this->set_item( 'role_id', current( $roles ), true, $roles );
  }
}
