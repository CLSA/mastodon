<?php
/**
 * extended_hin_form.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace mastodon\database;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * extended_hin_form: record
 */
class extended_hin_form extends base_form
{
  /**
   * Implements the parent's abstract import method.
   * @param database\form_entry $db_base_form_entry The entry to be used as the valid data.
   * @access public
   */
  public function import( $db_extended_hin_form_entry )
  {
    parent::import( $db_extended_hin_form_entry );

    $this->get_form()->add_consent(
      'Extended HIN Access',
      array( 'accept' => $db_extended_hin_form_entry->hin10_access )
    );

    $this->get_form()->add_consent(
      'CIHI Access',
      array( 'accept' => $db_extended_hin_form_entry->cihi_access )
    );

    $this->get_form()->add_consent(
      'Extended CIHI Access',
      array( 'accept' => $db_extended_hin_form_entry->cihi10_access )
    );
  }
}
