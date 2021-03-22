<?php
/**
 * base_form_entry.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace mastodon\database;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Base class for all form records
 */
abstract class base_form_entry extends \cenozo\database\record
{
  /**
   * Returns the errors found by processing this entry
   * 
   * @return associative array
   * @access public
   */
  public function get_errors()
  {
    $participant_class_name = lib::get_class_name( 'database\participant' );
    $table_name = static::get_table_name();
    $type = substr( $table_name, 0, strrpos( $table_name, '_form_entry' ) );

    $errors = array();
    if( 'contact' != $type && is_null( $this->participant_id ) ) $errors['participant_id'] = 'Cannot be blank.';
    return $errors;
  }
}
