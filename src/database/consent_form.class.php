<?php
/**
 * consent_form.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace mastodon\database;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * consent_form: record
 */
class consent_form extends base_form
{
  /**
   * Implements the parent's abstract import method.
   * @param database\form_entry $db_base_form_entry The entry to be used as the valid data.
   * @access public
   */
  public function import( $db_consent_form_entry )
  {
    parent::import( $db_consent_form_entry );

    // add the participation and HIN consent
    $db_form = $this->get_form();
    $db_form->add_consent( 'participation', array( 'accept' => $db_consent_form_entry->option_1 ) );
    $db_form->add_consent( 'HIN access', array( 'accept' => $db_consent_form_entry->option_2 ) );
  }
}
