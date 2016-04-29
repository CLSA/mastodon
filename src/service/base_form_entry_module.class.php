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
  public function validate()
  {
    parent::validate();

    if( 300 > $this->get_status()->get_code() )
    {
      $method = $this->get_method();

      if( 'PATCH' == $method )
      {
        // when setting the UID, make sure there is a matching participant
        $file = $this->get_file_as_array();
        if( array_key_exists( 'uid', $file ) )
        {
          $participant_class_name = lib::get_class_name( 'database\participant' );
          if( is_null( $participant_class_name::get_unique_record( 'uid', $file['uid'] ) ) )
            $this->get_status()->set_code( 406 );
        }
      }
      else if( 'POST' == $method )
      {
        // make sure there is no parent when posting a new entry
        if( !is_null( $this->get_parent_subject() ) ) $this->get_status()->set_code( 400 );
        else
        {
          // try and get a form which requires another entry
          $form_entry_name = $this->get_subject();
          $form_name = str_replace( '_entry', '', $form_entry_name );
          $class_name = lib::get_class_name( sprintf( 'database\%s', $form_name ) );

          $select = lib::create( 'database\select' );
          $select->from( $form_name );
          $select->add_column( 'id' );

          $modifier = lib::create( 'database\modifier' );

          // where the form isn't complete or invalid
          $modifier->where( $form_name.'.complete', '=', false );
          $modifier->where( $form_name.'.invalid', '=', false );

          // where the user doesn't already have an entry
          $join_mod = lib::create( 'database\modifier' );
          $join_mod->where( $form_name.'.id', '=', sprintf( '%s.%s_id', $form_entry_name, $form_name ), false );
          $join_mod->where( $form_entry_name.'.user_id', '=', lib::create( 'business\session' )->get_user()->id );
          $modifier->join_modifier( $form_entry_name, $join_mod, 'left' );
          $modifier->where( $form_entry_name.'.id', '=', NULL );

          // where there isn't already two entries
          $modifier->join(
            $form_name.'_total', $form_name.'.id', sprintf( '%s_total.%s_id', $form_name, $form_name ) );
          $modifier->where( $form_name.'_total.entry_total', '<', 2 );

          // get the oldest form
          $modifier->order( $form_name.'.date' );
          $modifier->limit( 1 );

          $row_list = $class_name::select( $select, $modifier );
          if( 0 == count( $row_list ) ) $this->get_status()->set_code( 404 );
          else $this->new_form_id = $row_list[0]['id'];
        }
      }
    }
  }

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

  /**
   * Extends parent method
   */
  public function pre_write( $record )
  {
    if( 'POST' == $this->get_method() )
    {
      // set the form when requesting a new form entry (posting)
      $form_entry_name = $this->get_subject();
      $form_name = str_replace( '_entry', '', $form_entry_name );
      $form_column_name = $form_name.'_id';

      $record->$form_column_name = $this->new_form_id;
      $record->deferred = true;
    }
  }

  /**
   * When posting a new entry the validate method searches for a form that requires one.
   * 
   * If no form is found then this variable will remain null and the status code will be set to 404.
   * If a form is found then this variable will be its primary key.
   * @var integer $new_form_id
   */
  protected $new_form_id = NULL;
}
