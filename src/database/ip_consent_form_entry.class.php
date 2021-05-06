<?php
/**
 * ip_consent_form_entry.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace mastodon\database;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * ip_consent_form_entry: record
 */
class ip_consent_form_entry extends base_form_entry
{
  /**
   * Returns the errors found by processing this entry
   * 
   * @return associative array
   * @access public
   */
  public function get_errors()
  {
    $postcode_class_name = lib::get_class_name( 'database\postcode' );

    $errors = parent::get_errors();

    if( is_null( $this->alternate_id ) ) $errors['alternate_id'] = 'Cannot be blank.';

    return $errors;
  }
}
