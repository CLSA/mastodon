<?php
/**
 * participant.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\database;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * participant: record
 */
class participant extends \cenozo\database\participant
{
  /**
   * This is a convenience method to get a participant's contact form, if it exists.
   * For design reasons the participant and contact_form tables do not have a one-to-one
   * relationship, therefor the base class will refuse a call to get_contact_form(), so
   * this method fakes it for us.
   * NOTE: no participant should ever have more than one contact form
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @return database\contact_form
   * @access public
   */
  public function get_contact_form()
  {
    $contact_form_list = $this->get_contact_form_list();
    return count( $contact_form_list ) ? current( $contact_form_list ) : NULL;
  }
}
