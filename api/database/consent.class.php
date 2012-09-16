<?php
/**
 * consent.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\database;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * consent: record
 */
class consent extends \cenozo\database\record
{
  /**
   * This is a convenience method to get a consent's form, if it exists.
   * For design reasons the consent and consent_form tables do not have a one-to-one
   * relationship, therefor the base class will refuse a call to get_consent_form(), so
   * this method fakes it for us.
   * NOTE: no consent should ever have more than one consent form
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @return database\consent_form
   * @access public
   */
  public function get_consent_form()
  {
    $consent_form_list = $this->get_consent_form_list();
    return count( $consent_form_list ) ? current( $consent_form_list ) : NULL;
  }
}
?>
