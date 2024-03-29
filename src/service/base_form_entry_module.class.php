<?php
/**
 * module.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
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
    $participant_class_name = lib::get_class_name( 'database\participant' );

    parent::validate();

    $form_entry_name = $this->get_subject();
    $form_name = str_replace( '_entry', '', $form_entry_name );

    if( $this->service->may_continue() )
    {
      $method = $this->get_method();

      if( 'PATCH' == $method )
      {
        // do not allow completed forms to be edited
        $get_form_method = sprintf( 'get_%s', $form_name );
        if( $this->get_resource()->$get_form_method()->completed )
        {
          $this->set_data( 'Once a form has been completed it cannot be changed.' );
          $this->get_status()->set_code( 306 );
        }
        else
        {
          // when setting the UID, make sure there is a matching participant
          $file = $this->get_file_as_array();
          if( array_key_exists( 'submitted', $file ) && true == $file['submitted'] )
          {
            // test the entry for errors
            $errors = $this->get_resource()->get_errors();
            if( 0 < count( $errors ) )
            {
              $this->get_status()->set_code( 400 );
              $this->set_data( $errors );
            }
          }
        }
      }
      else if( 'POST' == $method )
      {
        // make sure there is no parent when posting a new entry
        if( !is_null( $this->get_parent_subject() ) ) $this->get_status()->set_code( 400 );
        else
        {
          // try and get a form which requires another entry
          $class_name = lib::get_class_name( sprintf( 'database\%s', $form_name ) );

          $select = lib::create( 'database\select' );
          $select->from( $form_name );
          $select->add_column( 'id' );

          $modifier = lib::create( 'database\modifier' );

          // where the form isn't completed or invalid
          $modifier->where( $form_name.'.completed', '=', false );
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

    $modifier->join( $form_name, sprintf( '%s.%s_id', $form_entry_name, $form_name ), $form_name.'.id' );

    // special restricts for typists
    if( 'typist' == $db_role->name )
    {
      $modifier->where( $form_entry_name.'.user_id', '=', $db_user->id );
      $modifier->where( $form_entry_name.'.submitted', '=', false );
      $modifier->where( $form_name.'.invalid', '=', false );
    }

    if( $select->has_column( 'validated' ) )
    {
      $select->add_column(
        sprintf( 'IF( %s.validated_%s_id = %s.id, true, false )', $form_name, $form_entry_name, $form_entry_name ),
        'validated',
        false );
    }

    // always add the user's name
    $modifier->join( 'user', $form_entry_name.'.user_id', 'user.id' );
    $select->add_column( 'CONCAT( user.first_name, " ", user.last_name, " (", user.name, ")" )', 'user', false );

    if( 'contact_form_entry' != $form_entry_name )
    {
      $modifier->left_join( 'participant', sprintf( '%s.participant_id', $form_entry_name ), 'participant.id' );
      $select->add_table_column( 'participant', 'uid' );

      if( !is_null( $this->get_resource() ) && 'contact_form_entry' != $form_entry_name )
      {
        // include the participant first/last/uid as supplemental data
        $select->add_column(
          'CONCAT( participant.first_name, " ", participant.last_name, " (", participant.uid, ")" )',
          'formatted_participant_id',
          false );
      }
    }
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
      $record->submitted = false;
    }
  }

  /**
   * Extends parent method
   */
  public function post_write( $record )
  {
    if( 'PATCH' == $this->get_method() )
    {
      $file = $this->get_file_as_array();
      if( array_key_exists( 'submitted', $file ) && $record->submitted )
      {
        // submit the parent form if the sibling form has been submitted and matches this one
        $form_entry_name = $this->get_subject();
        $form_name = str_replace( '_entry', '', $form_entry_name );
        $form_method = sprintf( 'get_%s', $form_name );
        $form_entry_method = sprintf( 'get_%s_list', $form_entry_name );
        $db_parent_form = $record->$form_method();

        $form_entry_sel = lib::create( 'database\select' );
        $form_entry_sel->add_all_table_columns();
        $form_entry_list = $db_parent_form->$form_entry_method( $form_entry_sel );

        if( 1 < count( $form_entry_list ) )
        {
          $match = true;
          $base_form_entry = array_pop( $form_entry_list );
          foreach( $base_form_entry as $column => $value )
          {
            if( !in_array( $column, array( 'id', 'update_timestamp', 'create_timestamp', 'user_id' ) ) )
            {
              foreach( $form_entry_list as $compare_form_entry )
              {
                $compare_value = $compare_form_entry[$column];
                if( is_null( $value ) )
                {
                  if( !is_null( $compare_value ) ) $match = false;
                }
                else if( is_string( $value ) )
                {
                  if( strtoupper( $value ) !== strtoupper( $compare_value ) ) $match = false;
                }
                else
                {
                  if( $value !== $compare_value ) $match = false;
                }

                if( !$match ) break;
              }
            }

            if( !$match ) break;
          }

          if( $match ) 
          {
            // importing may cause a duplicate record in the form table
            try
            {
              $db_parent_form->import( $record );
            }
            catch( \cenozo\exception\database $e )
            {
              if( $e->is_duplicate_entry() )
              {
                $this->set_data( $e->get_duplicate_columns( 'form' ) );
                $this->get_status()->set_code( 409 );
              }
              else throw $e;
            }
          }
        }
      }
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
