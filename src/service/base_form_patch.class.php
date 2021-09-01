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
  public function get_file_as_array()
  {
    $patch_array = parent::get_file_as_array();

    // remove adjudicate from the patch array
    if( array_key_exists( 'adjudicate', $patch_array ) )
    {
      $this->adjudicate = $patch_array['adjudicate'];
      unset( $patch_array['adjudicate'] );
    }

    return $patch_array;
  }

  /**
   * Override parent method
   */
  protected function validate()
  {
    parent::validate();

    if( 300 > $this->status->get_code() )
    {
      $this->get_file_as_array(); // make sure to process the site array before the following check

      $db_role = lib::create( 'business\session' )->get_role();

      // make sure that only tier 2+ roles can reverse a withdraw
      if( $this->adjudicate && 2 > $db_role->tier ) $this->status->set_code( 403 );
    }
  }

  /**
   * Override parent method
   */
  protected function execute()
  {
    parent::execute();

    if( $this->adjudicate )
    {
      $form_name = $this->get_leaf_subject();
      $form_entry_name = $form_name.'_entry';
      $record = $this->get_leaf_record();

      // make sure the adjudicate ID points to a valid entry record
      $form_column = sprintf( '%s_id', $form_name );
      $db_form_entry = lib::create( sprintf( 'database\%s', $form_entry_name ), $this->adjudicate );
      if( $record->id != $db_form_entry->$form_column )
      {
        throw lib::create( 'exception\runtime', 
          sprintf( 'Tried to adjudicate %s id %d with entry id %d which doesn\'t belong to this form.',
                   str_replace( '_', ' ', $form_name ),
                   $record->id,
                   $this->adjudicate ),
          __METHOD__
        );
      }

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

  /**
   * When adjudicating a form this contains the ID of the form_entry record
   * @var boolean
   * @access protected
   */
  protected $adjudicate;
}
