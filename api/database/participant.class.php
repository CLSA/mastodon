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
   * Call parent method without restricting records by service.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param database\modifier $modifier Modifications to the selection.
   * @param boolean $count If true the total number of records instead of a list
   * @param boolean $distinct Whether to use the DISTINCT sql keyword
   * @param boolean $full Do not use, parameter ignored.
   * @access public
   * @static
   */
  public static function select( $modifier = NULL, $count = false, $distinct = true, $full = false )
  {
    return parent::select( $modifier, $count, $distinct, true );
  }

  /**
   * Get record using the columns from a unique key.
   * 
   * This method returns an instance of the record using the name(s) and value(s) of a unique key.
   * If the unique key has multiple columns then the $column and $value arguments should be arrays.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param string|array $column A column with the unique key property (or array of columns)
   * @param string|array $value The value of the column to match (or array of values)
   * @param boolean $full Ignore this parameter (see parent class for details)
   * @return database\record
   * @static
   * @access public
   */
  public static function get_unique_record( $column, $value, $full = true )
  {
    return parent::get_unique_record( $column, $value, $full );
  }

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
