<?php
/**
 * ip_consent_form.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace mastodon\database;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * ip_consent_form: record
 */
class ip_consent_form extends base_form
{
  /**
   * Implements the parent's abstract import method.
   * @param database\form_entry $db_base_form_entry The entry to be used as the valid data.
   * @access public
   */
  public function import( $db_ip_consent_form_entry )
  {
    parent::import( $db_ip_consent_form_entry );

    // add the information provider participation consent
    $datetime_obj = util::get_datetime_object(
      !is_null( $db_ip_consent_form_entry->date ) ? $db_ip_consent_form_entry->date : $this->date
    );
    $datetime_obj->setTime( 12, 0 );

    // reactivate if necessary
    $db_alternate = $db_ip_consent_form_entry->get_alternate();
    if( !$db_alternate->active )
    {
      $db_alternate->active = true;
      $db_alternate->save();
    }

    $this->get_form()->add_proxy_consent(
      'information provider',
      $db_alternate->id,
      array( 'accept' => $db_ip_consent_form_entry->accept, 'datetime' => $datetime_obj )
    );

  }
}
