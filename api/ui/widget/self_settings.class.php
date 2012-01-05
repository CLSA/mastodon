<?php
/**
 * self_settings.class.php
 * 
 * @author Dean Inglis <inglisd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget self settings
 * 
 * @package mastodon\ui
 */
class self_settings extends \cenozo\ui\widget\self_settings
{
  /**
   * Finish setting the variables in a widget.
   * 
   * Defines all variables which need to be set for the associated template.
   * @author Dean Inglis <inglisd@mcmaster.ca>
   * @access public
   */
  public function finish()
  {
    parent::finish();

    $this->set_variable( 'logo', 'img/logo_small.png' );

    $session = lib::create( 'business\session' );

    // override what the parent created for the site names
    $db_user = $session->get_user();
    
    $sites = array();
    foreach( $db_user->get_site_list() as $db_site )
      $sites[ $db_site->id ] = 
        $db_site->name.' ('.( 'tracking' == $db_site->cohort ? 'track' : 'comp' ).')';

    $this->set_variable( 'sites', $sites );    
  }
}
?>
