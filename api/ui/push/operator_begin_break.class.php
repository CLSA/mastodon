<?php
/**
 * operator_begin_break.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\push;
use mastodon\log, mastodon\util;
use mastodon\business as bus;
use mastodon\database as db;
use mastodon\exception as exc;

/**
 * push: operator begin_break
 *
 * Start the current user on a break (away_time)
 * @package mastodon\ui
 */
class operator_begin_break extends \mastodon\ui\push
{
  /**
   * Constructor.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Push arguments
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'operator', 'begin_break', $args );
  }
  
  /**
   * Executes the push.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function finish()
  {
    $session = bus\session::self();
    $db_away_time = lib::create( 'database\away_time' );
    $db_away_time->user_id = $session->get_user()->id;
    $db_away_time->save();
  }
}
?>
