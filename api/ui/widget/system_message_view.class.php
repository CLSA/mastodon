<?php
/**
 * system_message_view.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget system_message view
 */
class system_message_view extends \cenozo\ui\widget\system_message_view
{
  /**
   * Defines all items in the view.
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
      $site_mod->order( 'name' );
      foreach( $site_class_name::select( $site_mod ) as $db_site )
        $sites[$db_site->id] = sprintf( '%s (%s)', $db_site->name, $db_site->cohort );
    }

    // set the view's items
    $this->set_item(
      'site_id', $this->get_record()->site_id, false, $is_top_tier ? $sites : NULL );
  }
}
?>
