<?php
/**
 * self_shortcuts.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\widget;
use mastodon\log, mastodon\util;
use mastodon\business as bus;
use mastodon\database as db;
use mastodon\exception as exc;

/**
 * widget self shortcuts
 * 
 * @package mastodon\ui
 */
class self_shortcuts extends \mastodon\ui\widget
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
    parent::__construct( 'self', 'shortcuts', $args );
    $this->show_heading( false );
  }

  /**
   * Finish setting the variables in a widget.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function finish()
  {
    parent::finish();
    
    $this->set_variable( 'calculator', true );
    $this->set_variable( 'timezone_calculator', true );
    $this->set_variable( 'navigation', 'operator' != bus\session::self()->get_role()->name );
    $this->set_variable( 'refresh', true );
    $this->set_variable( 'home', false );
  }
}
?>
