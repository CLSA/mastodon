<?php
/**
 * cohort.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\database;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * cohort: record
 */
class cohort extends \cenozo\database\cohort
{
  /**
   * Call parent method without restricting records by service.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param database\modifier $modifier Modifications to the selection.
   * @param boolean $count If true the total number of records instead of a list
   * @param boolean $full Do not use, parameter ignored.
   * @access public
   * @static
   */
  public static function select( $modifier = NULL, $count = false, $full = false )
  {
    return parent::select( $modifier, $count, true );
  }

  /**
   * Override parent method so that records are not restricted by service.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param string $record_type The type of record.
   * @param modifier $modifier A modifier to apply to the list or count.
   * @param boolean $inverted Whether to invert the count (count records NOT in the joining table).
   * @param boolean $count If true then this method returns the count instead of list of records.
   * @return array( record ) | int
   * @access protected
   */
  protected function get_record_list(
    $record_type, $modifier = NULL, $inverted = false, $count = false )
  {
    $grand_parent = get_parent_class( get_parent_class( get_class() ) );
    return $grand_parent::get_record_list( $record_type, $modifier, $inverted, $count );
  }
}
