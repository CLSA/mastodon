<?php
/**
 * user_add.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget user add
 * 
 * @package mastodon\ui
 */
class user_add extends \cenozo\ui\widget\user_add{
  /**
   * Finish setting the variables in a widget.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function finish()
  {
    parent::finish();
    
    $site_class_name = lib::get_class_name( 'database\site' );
    $session = lib::create( 'business\session' );
    $is_top_tier = 3 == $session->get_role()->tier;

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

    $this->finish_setting_items();
  }
}
?>
