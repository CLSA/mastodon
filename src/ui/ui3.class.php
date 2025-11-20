<?php
/**
 * ui.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace mastodon\ui;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Application extension to ui class
 */
class ui3 extends \cenozo\ui\ui3
{
  /**
   * Extends the parent method
   */
  public static function generate()
  {
    $data = parent::generate();

    $db_role = lib::create( 'business\session' )->get_role();

    // add child actions to certain modules
    if( 2 <= $db_role->tier && array_key_exists( 'application', $data['module_list'] ) )
      $data['module_list']['application']->add_action( 'release', '/{identifier}' );

    if( array_key_exists( 'participant', $data['module_list'] ) )
    {
      $module = $data['module_list']['participant'];
      $module->add_child( 'application', 0 );
      if( 2 <= $db_role->tier ) $module->add_action( 'release', '/{identifier}' );
      if( in_array( $db_role->name, ['administrator', 'curator'] ) )
        $module->add_action( 'data', '/{identifier}?{study_phase_id}' );
    }

    if( array_key_exists( 'user', $data['module_list'] ) )
    {
      $module = $data['module_list']['user'];
      $module->add_child( 'consent_form_entry', 0 );
      $module->add_child( 'contact_form_entry', 0 );
      $module->add_child( 'general_proxy_form_entry', 0 );
      $module->add_child( 'proxy_form_entry', 0 );
      $module->add_child( 'dm_consent_form_entry', 0 );
      $module->add_child( 'ip_consent_form_entry', 0 );
    }

    // consent form
    if( array_key_exists( 'consent_form', $data['module_list'] ) )
    {
      $module = $data['module_list']['consent_form'];
      $module->add_child( 'consent_form_entry' );
      if( 2 <= $db_role->tier ) $module->add_action( 'adjudicate', '/{identifier}' );
    }
    if( array_key_exists( 'consent_form_entry', $data['module_list'] ) )
    {
      $module = $data['module_list']['consent_form_entry'];
      $module->remove_action( 'add' );
    }

    // contact form
    if( array_key_exists( 'contact_form', $data['module_list'] ) )
    {
      $module = $data['module_list']['contact_form'];
      $module->add_child( 'contact_form_entry' );
      if( 2 <= $db_role->tier ) $module->add_action( 'adjudicate', '/{identifier}' );
    }
    if( array_key_exists( 'contact_form_entry', $data['module_list'] ) )
    {
      $module = $data['module_list']['contact_form_entry'];
      $module->remove_action( 'add' );
    }

    // hin form
    if( array_key_exists( 'hin_form', $data['module_list'] ) )
    {
      $module = $data['module_list']['hin_form'];
      $module->add_child( 'hin_form_entry' );
      if( 2 <= $db_role->tier ) $module->add_action( 'adjudicate', '/{identifier}' );
    }
    if( array_key_exists( 'hin_form_entry', $data['module_list'] ) )
    {
      $module = $data['module_list']['hin_form_entry'];
      $module->remove_action( 'add' );
    }

    // extended hin form
    if( array_key_exists( 'extended_hin_form', $data['module_list'] ) )
    {
      $module = $data['module_list']['extended_hin_form'];
      $module->add_child( 'extended_hin_form_entry' );
      if( 2 <= $db_role->tier ) $module->add_action( 'adjudicate', '/{identifier}' );
    }
    if( array_key_exists( 'extended_hin_form_entry', $data['module_list'] ) )
    {
      $module = $data['module_list']['extended_hin_form_entry'];
      $module->remove_action( 'add' );
    }

    // general proxy form
    if( array_key_exists( 'general_proxy_form', $data['module_list'] ) )
    {
      $module = $data['module_list']['general_proxy_form'];
      $module->add_child( 'general_proxy_form_entry' );
      if( 2 <= $db_role->tier ) $module->add_action( 'adjudicate', '/{identifier}' );
    }
    if( array_key_exists( 'general_proxy_form_entry', $data['module_list'] ) )
    {
      $module = $data['module_list']['general_proxy_form_entry'];
      $module->remove_action( 'add' );
    }

    // proxy form
    if( array_key_exists( 'proxy_form', $data['module_list'] ) )
    {
      $module = $data['module_list']['proxy_form'];
      $module->add_child( 'proxy_form_entry' );
      if( 2 <= $db_role->tier ) $module->add_action( 'adjudicate', '/{identifier}' );
    }
    if( array_key_exists( 'proxy_form_entry', $data['module_list'] ) )
    {
      $module = $data['module_list']['proxy_form_entry'];
      $module->remove_action( 'add' );
    }

    // decision maker consent form
    if( array_key_exists( 'dm_consent_form', $data['module_list'] ) )
    {
      $module = $data['module_list']['dm_consent_form'];
      $module->add_child( 'dm_consent_form_entry' );
      if( 2 <= $db_role->tier ) $module->add_action( 'adjudicate', '/{identifier}' );
    }
    if( array_key_exists( 'dm_consent_form_entry', $data['module_list'] ) )
    {
      $module = $data['module_list']['dm_consent_form_entry'];
      $module->remove_action( 'add' );
    }

    // information provider consent form
    if( array_key_exists( 'ip_consent_form', $data['module_list'] ) )
    {
      $module = $data['module_list']['ip_consent_form'];
      $module->add_child( 'ip_consent_form_entry' );
      if( 2 <= $db_role->tier ) $module->add_action( 'adjudicate', '/{identifier}' );
    }
    if( array_key_exists( 'ip_consent_form_entry', $data['module_list'] ) )
    {
      $module = $data['module_list']['ip_consent_form_entry'];
      $module->remove_action( 'add' );
    }

    if( array_key_exists( 'participant_data', $data['module_list'] ) )
    {
      $module = $data['module_list']['participant_data'];
      $module->add_choose( 'cohort' );
      $module->add_child( 'participant_data_template' );
    }

    if( array_key_exists( 'study_phase', $data['module_list'] ) )
    {
      $module = $data['module_list']['study_phase'];
      $module->add_child( 'participant_data' );
    }

    // remove all parent list and utilities for typists
    if( 'typist' == $db_role->name )
    {
      $data['menu']['lists'] = [];
      $data['menu']['utilities'] = [];
    }

    // remove the application list from non admins
    if( 3 > $db_role->tier ) unset( $data['menu']['lists']['Applications'] );

    // add application-specific states to the base list
    $menu_list_items = [
      ['subject' => 'consent_form', 'title' => 'Consent Forms'],
      ['subject' => 'contact_form', 'title' => 'Contact Forms'],
      ['subject' => 'hin_form', 'title' => 'HIN Forms'],
      ['subject' => 'extended_hin_form', 'title' => 'Extended HIN Forms'],
      ['subject' => 'general_proxy_form', 'title' => 'General Proxy Forms'],
      ['subject' => 'proxy_form', 'title' => 'Proxy Forms'],
      ['subject' => 'dm_consent_form', 'title' => 'Proxy DM Forms'],
      ['subject' => 'ip_consent_form', 'title' => 'Proxy IP Forms'],
    ];

    if( 'typist' == $db_role->name )
    {
      $menu_list_items = [
        ['subject' => 'consent_form_entry', 'title' => 'Consent Form Entries'],
        ['subject' => 'contact_form_entry', 'title' => 'Contact Form Entries'],
        ['subject' => 'hin_form_entry', 'title' => 'HIN Form Entries'],
        ['subject' => 'extended_hin_form_entry', 'title' => 'Extended HIN Form Entries'],
        ['subject' => 'general_proxy_form_entry', 'title' => 'General Proxy Form Entries'],
        ['subject' => 'proxy_form_entry', 'title' => 'Proxy Form Entries'],
        ['subject' => 'dm_consent_form_entry', 'title' => 'Proxy DM Form Entries'],
        ['subject' => 'ip_consent_form_entry', 'title' => 'Proxy IP Form Entries'],
      ];
    }

    foreach( $menu_list_items as $item )
    {
      if( array_key_exists( $item['subject'], $data['module_list'] ) )
      {
        $module = $data['module_list'][$item['subject']];
        if( $module->get_list_menu() && $module->has_action( 'list' ) )
          $data['menu']['lists'][$item['title']] = $item['subject'];
      }
    }

    return $data;
  }
}
