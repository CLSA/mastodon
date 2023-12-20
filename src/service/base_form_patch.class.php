<?php
/**
 * patch.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace mastodon\service;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Special service for handling the patch meta-resource
 */
class base_form_patch extends \cenozo\service\patch
{
  /**
   * Override parent method
   */
  protected function prepare()
  {
    $this->extract_parameter_list[] = 'adjudicate';

    parent::prepare();
  }

  /**
   * Override parent method
   */
  protected function validate()
  {
    parent::validate();

    if( $this->may_continue() )
    {
      $this->get_file_as_array(); // make sure to process the site array before the following check

      $db_role = lib::create( 'business\session' )->get_role();

      // make sure that only tier 2+ roles can reverse a withdraw
      if( $this->get_argument( 'adjudicate', false ) && 2 > $db_role->tier ) $this->status->set_code( 403 );
    }
  }

  /**
   * Override parent method
   */
  protected function execute()
  {
    parent::execute();

    // If the adjudicate argument is provided then it contains the ID of the form_entry record
    $form_entry_id = $this->get_argument( 'adjudicate', false );
    if( false !== $form_entry_id )
    {
      $form_name = $this->get_leaf_subject();
      $form_entry_name = $form_name.'_entry';
      $record = $this->get_leaf_record();

      // make sure the form entry ID points to a valid entry record
      $form_column = sprintf( '%s_id', $form_name );
      $db_form_entry = lib::create( sprintf( 'database\%s', $form_entry_name ), $form_entry_id );
      if( $record->id != $db_form_entry->$form_column )
      {
        throw lib::create( 'exception\runtime', 
          sprintf(
            'Tried to adjudicate %s id %d with entry id %d which doesn\'t belong to this form.',
            str_replace( '_', ' ', $form_name ),
            $record->id,
            $form_entry_id
          ),
          __METHOD__
        );
      }

      // make sure the entry has no errors
      $errors = $db_form_entry->get_errors();
      if( 0 < count( $errors ) )
      {
        foreach( $errors as $column => $message )
          $errors[$column] = sprintf( '%s => %s', ucwords( str_replace( '_', ' ', str_replace( '_id', '', $column ) ) ), $message );
        throw lib::create( 'exception\notice',
          "The entry cannot be imported because it has the following error(s):\n".implode( "\n", $errors ),
          __METHOD__
        );
      }
      else
      {
        try
        {
          $record->import( $db_form_entry );
        }
        catch( \cenozo\exception\database $e )
        {
          if( $e->is_duplicate_entry() )
          {
            throw lib::create( 'exception\notice',
              'A form of the same type and date already exists for this participant. '.
              'The form cannot be imported and must be invalidated by an administrator.',
              __METHOD__,
              $e
            );
          }
          else throw $e;
        }
      }
    }
  }
}
