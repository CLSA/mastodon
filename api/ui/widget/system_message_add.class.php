<?php
/**
 * system_message_add.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget system_message add
 */
class system_message_add extends \cenozo\ui\widget\system_message_add
{
  /**
   * Defines all items in the add.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function setup()
  {
    parent::setup();

    $site_class_name = lib::get_class_name( 'database\site' );
    $session = lib::create( 'business\session' );
    $is_top_tier = 3 == $session->get_role()->tier;

    // create enum arrays
    if( $is_top_tier )
    {
      $sites = array();
      $site_mod = lib::create( 'database\modifier' );
      $site_mod->order( 'service_id' );
      $site_mod->order( 'name' );
      foreach( $site_class_name::select( $site_mod ) as $db_site )
        $sites[$db_site->id] =
          sprintf( '%s (%s)', $db_site->name, $db_site->get_service()->get_cohort()->name );
    }

    // set the add's items
    $this->set_item(
      'site_id', $this->get_record()->site_id, false, $is_top_tier ? $sites : NULL );
  }
}
