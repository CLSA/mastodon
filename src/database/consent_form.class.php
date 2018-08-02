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
    $datetime_obj = util::get_datetime_object( !is_null( $db_consent_form_entry->date ) ? $db_consent_form_entry->date : $this->date );
    $datetime_obj->setTime( 12, 0 );

    $db_form = $this->get_form();
    $db_form->add_consent( 'participation', array( 'accept' => $db_consent_form_entry->participation, 'datetime' => $datetime_obj ) );
    if( !is_null( $db_consent_form_entry->blood_urine ) )
    {
      $db_form->add_consent( 'draw blood', array( 'accept' => $db_consent_form_entry->blood_urine, 'datetime' => $datetime_obj ) );
      $db_form->add_consent( 'take urine', array( 'accept' => $db_consent_form_entry->blood_urine, 'datetime' => $datetime_obj ) );
    }
    $db_form->add_consent( 'HIN access', array( 'accept' => $db_consent_form_entry->hin_access, 'datetime' => $datetime_obj ) );
  }
}
