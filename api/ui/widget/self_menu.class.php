<?php
/**
 * self_menu.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget self menu
 * 
 * @package mastodon\ui
 */
class self_menu extends \cenozo\ui\widget\self_menu
{
  /**
   * Constructor
   * 
   * Defines all variables which need to be set for the associated template.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args An associative array of arguments to be processed by the widget
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( $args );

    $exclude = array(
      'address',
      'alternate',
      'consent',
      'phone' );
    $this->exclude_widget_list = array_merge( $this->exclude_widget_list, $exclude );
  }
}
?>
