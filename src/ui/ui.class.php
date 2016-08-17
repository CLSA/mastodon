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
    if( array_key_exists( 'application', $module_list ) && 2 <= $db_role->tier )
    {
      $module_list['application']['actions']['release'] = '/{identifier}';
    }
    if( array_key_exists( 'consent_form', $module_list ) )
    {
      $module_list['consent_form']['children'] = array( 'consent_form_entry' );
      if( 2 <= $db_role->tier ) $module_list['consent_form']['actions']['adjudicate'] = '/{identifier}';
    }
    if( array_key_exists( 'consent_form_entry', $module_list ) &&
        array_key_exists( 'add', $module_list['consent_form_entry']['actions'] ) )
    {
      // posting new form-entries is handled specially by the interface
      unset( $module_list['consent_form_entry']['actions']['add'] );
    }
    if( array_key_exists( 'participant', $module_list ) )
    {
      array_unshift( $module_list['participant']['children'], 'application' );
      if( 2 <= $db_role->tier ) $module_list['participant']['actions']['release'] = '/{identifier}';
    }
    if( array_key_exists( 'contact_form', $module_list ) )
    {
      $module_list['contact_form']['children'] = array( 'contact_form_entry' );
      if( 2 <= $db_role->tier ) $module_list['contact_form']['actions']['adjudicate'] = '/{identifier}';
    }
    if( array_key_exists( 'contact_form_entry', $module_list ) &&
        array_key_exists( 'add', $module_list['contact_form_entry']['actions'] ) )
    {
      // posting new form-entries is handled specially by the interface
      unset( $module_list['contact_form_entry']['actions']['add'] );
    }
    if( array_key_exists( 'hin_form', $module_list ) )
    {
      $module_list['hin_form']['children'] = array( 'hin_form_entry' );
      if( 2 <= $db_role->tier ) $module_list['hin_form']['actions']['adjudicate'] = '/{identifier}';
    }
    if( array_key_exists( 'hin_form_entry', $module_list ) &&
        array_key_exists( 'add', $module_list['hin_form_entry']['actions'] ) )
    {
      // posting new form-entries is handled specially by the interface
      unset( $module_list['hin_form_entry']['actions']['add'] );
    }
    if( array_key_exists( 'user', $module_list ) )
    {
      array_unshift( $module_list['user']['children'], array( 'proxy_form_entry' ) );
    }
    if( array_key_exists( 'proxy_form', $module_list ) )
    {
      $module_list['proxy_form']['children'] = array( 'proxy_form_entry' );
      if( 2 <= $db_role->tier ) $module_list['proxy_form']['actions']['adjudicate'] = '/{identifier}';
    }
    if( array_key_exists( 'proxy_form_entry', $module_list ) &&
        array_key_exists( 'add', $module_list['proxy_form_entry']['actions'] ) )
    {
      // posting new form-entries is handled specially by the interface
      unset( $module_list['proxy_form_entry']['actions']['add'] );
    }

    return $module_list;
  }

  /**
   * Extends the parent method
   */
  protected function get_list_items( $module_list )
  {
    $db_role = lib::create( 'business\session' )->get_role();
    $list = 'typist' == $db_role->name ? array() : parent::get_list_items( $module_list );

    // add application-specific states to the base list
    if( array_key_exists( 'consent_form', $module_list ) && $module_list['consent_form']['list_menu'] )
      $list['Consent Forms'] = 'consent_form';
    if( array_key_exists( 'consent_form_entry', $module_list ) &&
        $module_list['consent_form_entry']['list_menu'] &&
        'typist' == $db_role->name )
      $list['Consent Form Entries'] = 'consent_form_entry';

    if( array_key_exists( 'contact_form', $module_list ) && $module_list['contact_form']['list_menu'] )
      $list['Contact Forms'] = 'contact_form';
    if( array_key_exists( 'contact_form_entry', $module_list ) &&
        $module_list['contact_form_entry']['list_menu'] &&
        'typist' == $db_role->name )
      $list['Contact Form Entries'] = 'contact_form_entry';
    
    if( array_key_exists( 'hin_form', $module_list ) && $module_list['hin_form']['list_menu'] )
      $list['HIN Forms'] = 'hin_form';
    if( array_key_exists( 'hin_form_entry', $module_list ) &&
        $module_list['hin_form_entry']['list_menu'] &&
        'typist' == $db_role->name )
      $list['HIN Form Entries'] = 'hin_form_entry';
    
    if( array_key_exists( 'proxy_form', $module_list ) && $module_list['proxy_form']['list_menu'] )
      $list['Proxy Forms'] = 'proxy_form';
    if( array_key_exists( 'proxy_form_entry', $module_list ) &&
        $module_list['proxy_form_entry']['list_menu'] &&
        'typist' == $db_role->name )
      $list['Proxy Form Entries'] = 'proxy_form_entry';

    return $list;
  }

  /**
   * Extends the parent method
   */
  protected function get_utility_items()
  {
    return 'typist' == lib::create( 'business\session' )->get_role()->name ? array() : parent::get_utility_items();
  }
}
