<?php
/**
 * self_home.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\widget;
use mastodon\log, mastodon\util;
use mastodon\business as bus;
use mastodon\database as db;
use mastodon\exception as exc;

/**
 * widget self home
 * 
 * @package mastodon\ui
 */
class self_home extends \mastodon\ui\widget
{
  /**
   * Constructor
   * 
   * Defines all variables which need to be set for the associated template.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args An associative array of arguments to be processed by the widget
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'self', 'home', $args );
    $this->set_heading( 'Home' );
    
    $session = bus\session::self();
    $db_user = $session->get_user();

    // determine the user's last activity
    $db_activity = $session->get_user()->get_last_activity();

    $this->set_variable( 'version',
      bus\setting_manager::self()->get_setting( 'general', 'version' ) );
    $this->set_variable( 'user_name', $db_user->first_name.' '.$db_user->last_name );
    $this->set_variable( 'role_name', $session->get_role()->name );
    $this->set_variable( 'site_name', $session->get_site()->name );
    if( $db_activity )
    {
      $this->set_variable( 'last_day', util::get_formatted_date( $db_activity->datetime ) );
      $this->set_variable( 'last_time', util::get_formatted_time( $db_activity->datetime ) );
    }
  }
}
?>
