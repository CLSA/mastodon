<?php
/**
 * proxy_form_entry_new.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: proxy_form_entry new
 *
 * Create a new proxy_form_entry.
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
    // make sure not to include forms from onyx
    $this->form_mod = lib::create( 'database\modifier' );
    $this->form_mod->where( 'from_onyx', '=', false );

    parent::__construct( 'proxy', $args );
  }
}
?>
