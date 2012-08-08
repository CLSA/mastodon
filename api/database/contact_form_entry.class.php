<?php
/**
 * contact_form_entry.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\database;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * contact_form_entry: record
 */
class contact_form_entry extends \cenozo\database\record
{
  /**
   * Override parent method to make sure 6-character postal codes get a space
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param string $column_name The name of the column
   * @param mixed $value The value to set the contents of a column to
   * @throws exception\argument
   * @access public
   */
  public function __set( $column_name, $value )
  {
    if( 'postcode' == $column_name && is_string( $value ) && 6 == strlen( $value ) )
      $value = sprintf( '%s %s', substr( $value, 0, 3 ), substr( $value, 3, 3 ) );

    parent::__set( $column_name, $value );
  }
}
?>
