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
class ui extends \cenozo\ui\ui
{
  /**
   * Extends the parent method
   */
  protected function build_module_list()
  {
    parent::build_module_list();

    $db_role = lib::create( 'business\session' )->get_role();

    // add child actions to certain modules

    $module = $this->get_module( 'application' );
    if( !is_null( $module ) && 2 <= $db_role->tier ) $module->add_action( 'release', '/{identifier}' );

    $module = $this->get_module( 'participant' );
    if( !is_null( $module ) )
    {
      $module->add_child( 'application', 0 );
      if( 2 <= $db_role->tier ) $module->add_action( 'release', '/{identifier}' );
    }

    $module = $this->get_module( 'user' );
    if( !is_null( $module ) )
    {
      $module->add_child( 'consent_form_entry', 0 );
      $module->add_child( 'contact_form_entry', 0 );
      $module->add_child( 'general_proxy_form_entry', 0 );
      $module->add_child( 'proxy_form_entry', 0 );
      $module->add_child( 'proxy_consent_form_entry', 0 );
    }

    // consent form
    $module = $this->get_module( 'consent_form' );
    if( !is_null( $module ) )
    {
      $module->add_child( 'consent_form_entry' );
      if( 2 <= $db_role->tier ) $module->add_action( 'adjudicate', '/{identifier}' );
    }
    $module = $this->get_module( 'consent_form_entry' );
    if( !is_null( $module ) ) $module->remove_action( 'add' );

    // contact form
    $module = $this->get_module( 'contact_form' );
    if( !is_null( $module ) )
    {
      $module->add_child( 'contact_form_entry' );
      if( 2 <= $db_role->tier ) $module->add_action( 'adjudicate', '/{identifier}' );
    }
    $module = $this->get_module( 'contact_form_entry' );
    if( !is_null( $module ) ) $module->remove_action( 'add' );

    // hin form
    $module = $this->get_module( 'hin_form' );
    if( !is_null( $module ) )
    {
      $module->add_child( 'hin_form_entry' );
      if( 2 <= $db_role->tier ) $module->add_action( 'adjudicate', '/{identifier}' );
    }
    $module = $this->get_module( 'hin_form_entry' );
    if( !is_null( $module ) ) $module->remove_action( 'add' );

    // extended hin form
    $module = $this->get_module( 'extended_hin_form' );
    if( !is_null( $module ) )
    {
      $module->add_child( 'extended_hin_form_entry' );
      if( 2 <= $db_role->tier ) $module->add_action( 'adjudicate', '/{identifier}' );
    }
    $module = $this->get_module( 'extended_hin_form_entry' );
    if( !is_null( $module ) ) $module->remove_action( 'add' );

    // general proxy form
    $module = $this->get_module( 'general_proxy_form' );
    if( !is_null( $module ) )
    {
      $module->add_child( 'general_proxy_form_entry' );
      if( 2 <= $db_role->tier ) $module->add_action( 'adjudicate', '/{identifier}' );
    }
    $module = $this->get_module( 'general_proxy_form_entry' );
    if( !is_null( $module ) ) $module->remove_action( 'add' );

    // proxy form
    $module = $this->get_module( 'proxy_form' );
    if( !is_null( $module ) )
    {
      $module->add_child( 'proxy_form_entry' );
      if( 2 <= $db_role->tier ) $module->add_action( 'adjudicate', '/{identifier}' );
    }
    $module = $this->get_module( 'proxy_form_entry' );
    if( !is_null( $module ) ) $module->remove_action( 'add' );

    // proxy consent form
    $module = $this->get_module( 'proxy_consent_form' );
    if( !is_null( $module ) )
    {
      $module->add_child( 'proxy_consent_form_entry' );
      if( 2 <= $db_role->tier ) $module->add_action( 'adjudicate', '/{identifier}' );
    }
    $module = $this->get_module( 'proxy_consent_form_entry' );
    if( !is_null( $module ) ) $module->remove_action( 'add' );
  }

  /**
   * Extends the parent method
   */
  protected function build_listitem_list()
  {
    $db_role = lib::create( 'business\session' )->get_role();

    // don't generate the parent list items for typists
    if( 'typist' != $db_role->name ) parent::build_listitem_list();

    // remove the application list from non admins
    if( 3 > $db_role->tier ) $this->remove_listitem( 'Applications' );

    // add application-specific states to the base list
    $this->add_listitem( 'Consent Forms', 'consent_form' );
    $this->add_listitem( 'Contact Forms', 'contact_form' );
    $this->add_listitem( 'HIN Forms', 'hin_form' );
    $this->add_listitem( 'Extended HIN Forms', 'extended_hin_form' );
    $this->add_listitem( 'General Proxy Forms', 'general_proxy_form' );
    if( 3 <= $db_role->tier ) $this->add_listitem( 'Opal Form Template', 'opal_form_template' );
    $this->add_listitem( 'Proxy Forms', 'proxy_form' );
    $this->add_listitem( 'Proxy DMIP Forms', 'proxy_consent_form' );

    if( 'typist' == $db_role->name )
    {
      $this->add_listitem( 'Consent Form Entries', 'consent_form_entry' );
      $this->add_listitem( 'Contact Form Entries', 'contact_form_entry' );
      $this->add_listitem( 'HIN Form Entries', 'hin_form_entry' );
      $this->add_listitem( 'Extended HIN Form Entries', 'extended_hin_form_entry' );
      $this->add_listitem( 'General Proxy Form Entries', 'general_proxy_form_entry' );
      $this->add_listitem( 'Proxy Form Entries', 'proxy_form_entry' );
      $this->add_listitem( 'Proxy DMIP Form Entries', 'proxy_consent_form_entry' );
    }
  }

  /**
   * Extends the parent method
   */
  protected function get_utility_items()
  {
    return 'typist' == lib::create( 'business\session' )->get_role()->name ? array() : parent::get_utility_items();
  }
}
