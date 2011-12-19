<?php
/**
 * base_record.class.php
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
 * Base class for all widgets pertaining to a single record.
 * 
 * @abstract
 * @package mastodon\ui
 */
abstract class base_record
  extends \mastodon\ui\widget
  implements \mastodon\ui\contains_record
{
  /**
   * Constructor
   * 
   * Defines all variables which need to be set for the associated template.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param string $subject The subject being viewed.
   * @param string $name The name of the operation.
   * @param array $args An associative array of arguments to be processed by th  widget
   * @throws exception\argument
   * @access public
   */
  public function __construct( $subject, $name, $args )
  {
    parent::__construct( $subject, $name, $args );
    
    $class_name = '\\mastodon\\database\\'.$this->get_subject();
    $this->set_record( new $class_name( $this->get_argument( 'id', NULL ) ) );
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

    // define all template variables for this widget
    $this->set_variable( 'id', $this->get_record()->id );
  }
  
  /**
   * Method required by the contains_record interface.
   * @author Patrick Emond
   * @return database\record
   * @access public
   */
  public function get_record()
  {
    return $this->record;
  }

  /**
   * Method required by the contains_record interface.
   * @author Patrick Emond
   * @param database\record $record
   * @access public
   */
  public function set_record( $record )
  {
    $this->record = $record;
  }

  /**
   * An record of the item being viewed.
   * @var record
   * @access private
   */
  private $record = NULL;
}
?>
