<?php
/**
 * proxy_form_entry_edit.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: proxy_form_entry edit
 *
 * Edit a proxy_form_entry.
 * @package mastodon\ui
 */
class proxy_form_entry_edit extends base_form_entry_edit
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
