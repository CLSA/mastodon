<?php
/**
 * contact_form_entry_edit.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: contact_form_entry edit
 *
 * Edit a contact_form_entry.
 */
class contact_form_entry_edit extends base_form_entry_edit
{
  /**
   * Constructor.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Push arguments
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'contact', $args );
  }
}
?>
