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
  protected function generate_modules()
  {
    parent::generate_modules();

    $db_role = lib::create( 'business\session' )->get_role();

    $module = $this->get_module( 'application' );
    if( !is_null( $module ) && 2 <= $db_role->tier ) $module->add_action( 'release', '/{identifier}' );

    $module = $this->get_module( 'participant' );
    if( !is_null( $module ) )
    {
      $module->add_child( 'application', 0 );
      if( 2 <= $db_role->tier ) $module->add_action( 'release', '/{identifier}' );
      if( in_array( $db_role->name, ['administrator', 'curator'] ) )
        $module->add_action( 'data', '/{identifier}?{study_phase_id}' );
      $module->add_child( 'study_phase_status' );
    }

    $user_module = $this->get_module( 'user' );
    foreach( $this->form_type_list as $form_type )
    {
      if( !is_null( $module ) ) $user_module->add_child( sprintf( '%s_form_entry', $form_type ), 0 );

      $module = $this->get_module( sprintf( '%s_form', $form_type ) );
      if( !is_null( $module ) )
      {
        $module->add_child( sprintf( '%s_form_entry', $form_type ) );
        if( 2 <= $db_role->tier ) $module->add_action( 'adjudicate', '/{identifier}' );
      }

      $module = $this->get_module( sprintf( '%s_form_entry', $form_type ) );
      if( !is_null( $module ) ) $module->remove_action( 'add' );
    }

    $module = $this->get_module( 'participant_data' );
    if( !is_null( $module ) )
    {
      $module->add_choose( 'cohort' );
      $module->add_child( 'participant_data_template' );
    }

    $module = $this->get_module( 'study_phase' );
    if( !is_null( $module ) )
    {
      $module->add_child( 'participant_data' );
      $module->add_child( 'study_phase_status' );
    }
  }

  /**
   * Extends the parent method
   */
  protected function generate_menus()
  {
    parent::generate_menus();

    $db_role = lib::create( 'business\session' )->get_role();

    // remove all parent lists and utilities for typists
    if( 'typist' == $db_role->name )
    {
      $this->remove_all_menu_items( 'list' );
      $this->remove_all_menu_items( 'utility' );
    }

    foreach( $this->form_type_list as $form_type )
    {
      $title = sprintf(
        '%s Form',
        ucWords(
          str_replace(
            ['hin', 'dm', 'ip'],
            ['HIN', 'DM', 'IP'],
            str_replace( '_', ' ', $form_type )
          )
        )
      );
      $subject = sprintf( '%s_form', $form_type );
      if( 'typist' == $db_role->name )
      {
        $title .= ' Entries';
        $subject .= '_entry';
      }
      $this->add_menu_item( 'list', $title, $subject );
    }

    $this->add_menu_item( 'list', 'Studies', 'study' );
  }

  /**
   * A list of all form types
   * @var []
   */
  private $form_type_list = [
    'consent', 'contact', 'hin', 'extended_hin', 'general_proxy', 'proxy', 'dm_consent', 'ip_consent'
  ];
}
