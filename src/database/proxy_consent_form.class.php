<?php
/**
 * proxy_consent_form.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace mastodon\database;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * proxy_consent_form: record
 */
class proxy_consent_form extends base_form
{
  /**
   * Implements the parent's abstract import method.
   * @param database\form_entry $db_base_form_entry The entry to be used as the valid data.
   * @access public
   */
  public function import( $db_proxy_consent_form_entry )
  {
    parent::import( $db_proxy_consent_form_entry );

    // add the proxy participation consent
    $datetime_obj = util::get_datetime_object( !is_null( $db_consent_form_entry->date ) ? $db_consent_form_entry->date : $this->date );
    $datetime_obj->setTime( 12, 0 );

    $db_form = $this->get_form();
    $db_form->add_consent(
      $db_consent_form_entry->type,
      array( 'accept' => $db_consent_form_entry->accept, 'datetime' => $datetime_obj )
    );
  }
}
