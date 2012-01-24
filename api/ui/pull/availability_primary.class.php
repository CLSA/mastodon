<?php
/**
 * availability_primary.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\pull;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * pull: availability primary
 * 
 * @package mastodon\ui
 */
class availability_primary extends \cenozo\ui\pull\base_primary
{
  /**
   * Constructor
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Pull arguments.
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'availability', $args );
  }
}
?>
