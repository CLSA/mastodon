<?php
/**
 * base_form_entry.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
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
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @return associative array
   * @access public
   */
  public function get_errors()
  {
    $participant_class_name = lib::get_class_name( 'database\participant' );
    $table_name = static::get_table_name();
    $type = substr( $table_name, 0, strrpos( $table_name, '_form_entry' ) );

    $errors = array();

    if( 'contact' != $type )
    {
      if( is_null( $this->uid ) ) $errors['uid'] = 'Cannot be blank.';
      else
      {
        $participant_mod = lib::create( 'database\modifier' );
        $participant_mod->where( 'uid', '=', $this->uid );
        if( 0 == $participant_class_name::count( $participant_mod ) )
          $errors['uid'] = sprintf( 'There is no participant with the UID "%s".', $this->uid );
      }
    }

    return $errors;
  }
}
