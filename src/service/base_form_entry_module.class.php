<?php
/**
 * module.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\service;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Performs operations which effect how this module is used in a service
 */
abstract class base_form_entry_module extends \cenozo\service\module
{
  /**
   * Extend parent method
   */
  public function prepare_read( $select, $modifier )
  {
    parent::prepare_read( $select, $modifier );

    $session = lib::create( 'business\session' );
    $db_user = $session->get_user();
    $db_role = $session->get_role();

    $form_entry_name = $this->get_subject();
    $form_name = str_replace( '_entry', '', $form_entry_name );

    // special restricts for typists
    if( 'typist' == $db_role->name )
    {
      $modifier->where( $form_entry_name.'.user_id', '=', $db_user->id );
      $modifier->where( 'deferred', '=', true );
    }

    if( $select->has_column( 'validated' ) )
    {
      $modifier->join( $form_name, sprintf( '%s.%s_id', $form_entry_name, $form_name ), $form_name.'.id' );
      $select->add_column(
        sprintf( 'IF( %s.validated_%s_id = %s.id, true, false )', $form_name, $form_entry_name, $form_entry_name ),
        'validated',
        false );
    }

    // always add the user's name
    $modifier->join( 'user', $form_entry_name.'.user_id', 'user.id' );
    $select->add_column( 'CONCAT( user.first_name, " ", user.last_name, " (", user.name, ")" )', 'user', false );
  }
}
