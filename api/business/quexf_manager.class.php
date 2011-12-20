<?php
/**
 * quexf_manager.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\business
 * @filesource
 */

namespace mastodon\business;
use mastodon\log, mastodon\util;
use mastodon\database as db;
use mastodon\exception as exc;

/**
 * Manages importing data from QUEXF
 * 
 * @package mastodon\business
 */
class quexf_manager extends \mastodon\base_object
{
  /**
   * Constructor.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function __construct()
  {
  }

  /**
   * Gets the number of participants ready for import.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function get_participant_count()
  {
    // always return 0 if there is no path to quexf
    if( is_null( QUEXF_PATH ) ) return 0;

    // TODO: implement
    return 1;
  }

  /**
   * Gets the number of participants ready for import which are invalid.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function get_invalid_participant_count()
  {
    // always return 0 if there is no path to quexf
    if( is_null( QUEXF_PATH ) ) return 0;

    // TODO: implement
    return 1;
  }

  /**
   * Gets the number of participants ready for import which are valid.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function get_valid_participant_count()
  {
    // always return 0 if there is no path to quexf
    if( is_null( QUEXF_PATH ) ) return 0;

    // TODO: implement
    return 1;
  }

  /**
   * Imports all valid participants, assigns them a UID from the pool and removes them from QUEXF
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function import()
  {
    // TODO: implement
  }
}
?>
