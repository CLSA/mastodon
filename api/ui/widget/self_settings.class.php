<?php
/**
 * self_settings.class.php
 * 
 * @author Dean Inglis <inglisd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget self settings
 */
class self_settings extends \cenozo\ui\widget\self_settings
{
  /**
   * Sets up the operation with any pre-execution instructions that may be necessary.
   * 
   * @author Dean Inglis <inglisd@mcmaster.ca>
   * @access protected
   */
  protected function setup()
  {
    parent::setup();

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
