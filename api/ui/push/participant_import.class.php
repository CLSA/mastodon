<?php
/**
 * participant_import.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: participant import
 *
 * Syncs participant information between Sabretooth and Mastodon
 * @package mastodon\ui
 */
class participant_import extends \cenozo\ui\push
{
  /**
   * Constructor.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Push arguments
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'participant', 'import', $args );
  }

  /**
   * Executes the push.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function finish()
  {
    $quexf_manager = lib::create( 'business\quexf_manager', QUEXF_PATH );
    $quexf_manager->import_contact_data();
  }
}
?>
