<?php
/**
 * contains_record.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui;

/**
 * Interface that specifies that a class is directly related to a single record.
 * @package mastodon\ui
 */
interface contains_record
{
  /**
   * Returns this object's record.
   * 
   * @return database\record
   * @access public
   */
  public function get_record();

  /**
   * Sets this object's record.
   * 
   * @param $record database\record
   * @access public
   */
  public function set_record( $record );
}
