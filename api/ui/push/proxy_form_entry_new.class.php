<?php
/**
 * proxy_form_entry_new.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: proxy_form_entry new
 *
 * Create a new proxy_form_entry.
 * @package mastodon\ui
 */
class proxy_form_entry_new extends base_form_entry_new
{
  /**
   * Constructor.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Push arguments
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'proxy', $args );
  }
}
?>
