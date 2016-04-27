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

    $db_role = lib::create( 'business\session' )->get_role();

    // add child actions to certain modules
    if( array_key_exists( 'application', $module_list ) )
      if( 2 <= $db_role->tier ) $module_list['application']['actions']['release'] = '/{identifier}';
    if( array_key_exists( 'consent_form', $module_list ) )
    {
      $module_list['consent_form']['children'] = array( 'consent_form_entry' );
      if( 2 <= $db_role->tier ) $module_list['consent_form']['actions']['adjudicate'] = '/{identifier}';
    }
    if( array_key_exists( 'participant', $module_list ) )
    {
      array_unshift( $module_list['participant']['children'], 'application' );
      if( 2 <= $db_role->tier ) $module_list['participant']['actions']['release'] = '/{identifier}';
    }
    if( array_key_exists( 'proxy_form', $module_list ) )
    {
      $module_list['proxy_form']['children'] = array( 'proxy_form_entry' );
      if( 2 <= $db_role->tier ) $module_list['proxy_form']['actions']['adjudicate'] = '/{identifier}';
    }

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
}
