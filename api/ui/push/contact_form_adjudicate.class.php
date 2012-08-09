<?php
/**
 * contact_form_adjudicate.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: contact_form adjudicate
 *
 * Edit a contact_form.
 */
class contact_form_adjudicate extends base_form_adjudicate
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
