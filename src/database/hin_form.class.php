<?php
/**
 * hin_form.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\database;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * hin_form: record
 */
class hin_form extends base_form
{
  /**
   * Implements the parent's abstract import method.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param database\form_entry $db_base_form_entry The entry to be used as the valid data.
   * @access public
   */
  public function import( $db_hin_form_entry )
  {
    parent::import( $db_hin_form_entry );

    // add the HIN access consent
    $this->get_form()->add_consent( 'HIN access', array( 'accept' => $db_hin_form_entry->accept ) );
  }
}
