<?php
/**
 * self_menu.class.php
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
 * widget self menu
 * 
 * @package mastodon\ui
 */
class self_menu extends \mastodon\ui\widget
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
    parent::__construct( 'self', 'menu', $args );
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

    $db_role = bus\session::self()->get_role();

    // get all list widgets that the user has access to
    $lists = array();

    $modifier = new db\modifier();
    $modifier->where( 'operation.type', '=', 'widget' );
    $modifier->where( 'operation.name', '=', 'list' );
    $widgets = $db_role->get_operation_list( $modifier );
    
    $exclude = array(
      'address',
      'alternate',
      'consent',
      'operation',
      'phone' );

    foreach( $widgets as $db_widget )
    {
      if( !in_array( $db_widget->subject, $exclude ) )
        $lists[] = array(
          'heading' => util::pluralize( str_replace( '_', ' ', $db_widget->subject ) ),
          'subject' => $db_widget->subject,
          'name' => $db_widget->name );
    }

    // get all report widgets that the user has access to
    $reports = array();

    $modifier = new db\modifier();
    $modifier->where( 'operation.type', '=', 'widget' );
    $modifier->where( 'operation.name', '=', 'report' );
    $widgets = $db_role->get_operation_list( $modifier );
    
    foreach( $widgets as $db_widget )
    {
      $reports[] = array( 'heading' => str_replace( '_', ' ', $db_widget->subject ),
                          'subject' => $db_widget->subject,
                          'name' => $db_widget->name );
    }

    $this->set_variable( 'lists', $lists );
    $this->set_variable( 'reports', $reports );
  }
}
?>
