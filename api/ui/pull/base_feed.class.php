<?php
/**
 * base_feed.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\pull;
use mastodon\log, mastodon\util;
use mastodon\business as bus;
use mastodon\database as db;
use mastodon\exception as exc;

/**
 * Base class for all feed pull operations.
 * 
 * @abstract
 * @package mastodon\ui
 */
abstract class base_feed extends \mastodon\ui\pull
{
  /**
   * Constructor
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Pull arguments.
   * @access public
   */
  public function __construct( $subject, $args )
  {
    parent::__construct( $subject, 'feed', $args );

    // set the start and end datetimes
    $this->start_datetime = $this->get_argument( 'start' );
    $this->end_datetime = $this->get_argument( 'end' );
  }
  
  /**
   * Feeds are always returned in JSON format.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @return string
   * @access public
   */
  public function get_data_type() { return "json"; }

  /**
   * The start date/time of the feed
   * @var string
   * @access protected
   */
  protected $start_datetime = NULL;
  
  /**
   * The end date/time of the feed
   * @var string
   * @access protected
   */
  protected $end_datetime = NULL;
}
?>
