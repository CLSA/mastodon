<?php
/**
 * ui.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Application extension to ui class
 */
class ui extends \cenozo\ui\ui
{
  /**
   * Extends the parent method
   */
  protected function get_module_list( $modifier = NULL )
  {
    $module_list = parent::get_module_list( $modifier );

    // add child actions to certain modules
    if( array_key_exists( 'consent_form', $module_list ) )
      $module_list['consent_form']['children'] = array( 'consent_form_entry' );
    if( array_key_exists( 'proxy_form', $module_list ) )
      $module_list['proxy_form']['children'] = array( 'proxy_form_entry' );

    return $module_list;
  }

  /**
   * Extends the parent method
   */
  protected function get_list_items( $module_list )
  {
    $list = parent::get_list_items( $module_list );
    $db_role = lib::create( 'business\session' )->get_role();

    // add application-specific states to the base list
    if( array_key_exists( 'consent_form', $module_list ) && $module_list['consent_form']['list_menu'] )
      $list['Consent Forms'] = 'consent_form';
    if( array_key_exists( 'proxy_form', $module_list ) && $module_list['proxy_form']['list_menu'] )
      $list['Proxy Forms'] = 'proxy_form';

    return $list;
  }

  /**
   * Extends the parent method
   */
  protected function get_utility_items()
  {
    $list = parent::get_utility_items();
    $db_role = lib::create( 'business\session' )->get_role();

    // add application-specific states to the base list
    if( 2 <= $db_role->tier )
      $list['Participant Release'] = array( 'subject' => 'participant', 'action' => 'release' );

    return $list;
  }
}
