<?php
/**
 * consent_form_entry_new.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: consent_form_entry new
 *
 * Create a new consent_form_entry.
 * @package mastodon\ui
 */
class consent_form_entry_defer extends \cenozo\ui\push\base_record
{
  /**
   * Constructor.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Push arguments
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'consent_form_entry', 'defer', $args );
  }

  /**
   * Overrides the parent method to make sure the number isn't blank and is a valid number
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @throws exception\notice
   * @access public
   */
  public function finish()
  {
  }
}
?>
