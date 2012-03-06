<?php
/**
 * base_view.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Base class for widgets which view forms.
 * 
 * @abstract
 * @package cenozo\ui
 */
abstract class base_form_view extends \cenozo\ui\widget\base_record
{
  /**
   * Constructor
   * 
   * Defines all variables which need to be set for the associated template.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param string $subject The subject being viewed.
   * @param array $args An associative array of arguments to be processed by th  widget
   * @throws exception\argument
   * @access public
   */
  public function __construct( $subject, $args )
  {
    parent::__construct( $subject, 'view', $args );
    
    // make sure we have an id (we don't actually need to use it since the parent does)
    $this->get_argument( 'id' );

    // determine properties based on the current user's permissions
    $operation_class_name = lib::get_class_name( 'database\operation' );
    $session = lib::create( 'business\session' );

    $this->set_heading( 'Viewing '.$this->get_subject().' details' );
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

    foreach( $this->items as $item_id => $item )
    {
      $this->items[$item_id]['entry_1'] = is_null( $this->form_entry_1 )
        ? array( 'user' => 'n/a',
                 'value' => NULL )
        : array( 'user' => $this->form_entry_1->get_user()->name,
                 'value' => $this->form_entry_1->$item_id );
      $this->items[$item_id]['entry_2'] = is_null( $this->form_entry_2 )
        ? array( 'user' => 'n/a',
                 'value' => NULL )
        : array( 'user' => $this->form_entry_2->get_user()->name,
                 'value' => $this->form_entry_2->$item_id );
    }

    $this->set_variable( 'item', $this->items );
  }
  
  /**
   * Add an item to the form view.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param string $item_id The item's id, can be one of the record's column names.
   * @param string $heading The item's heading as it will appear in the view
   * @param string $note A note to add below the item.
   * @access public
   */
  public function add_item( $item_id, $heading, $note = NULL )
  {
    $this->items[$item_id] = array(
      'heading' => $heading,
      'type' => 'constant' );
    if( !is_null( $note ) ) $this->items[$item_id]['note'] = $note;
  }

  /**
   * Sets and item's value and additional data.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param string $item_id The item's id, can be one of the record's column names.
   * @param record $form_entry_1 The first entry to the form.
   * @param record $form_entry_2 The second entry to the form.
   * @throws exception\argument
   * @access public
   */
  public function set_form_entries( $form_entry_1 = NULL, $form_entry_2 = NULL )
  {
    $this->form_entry_1 = $form_entry_1;
    $this->form_entry_2 = $form_entry_2;
  }

  /**
   * An associative array where the key is a unique identifier (usually a column name) and the
   * value is an associative array which includes:
   * "heading" => the label to display
   * "type" => the type of variable (see {@link add_item} for details)
   * "value" => the value of the column
   * "enum" => all possible values if the item type is "enum"
   * "required" => boolean describes whether the value can be left blank
   * @var array
   * @access private
   */
  private $items = array();

  // TODO: document
  protected $form_entry_1 = NULL;

  // TODO: document
  protected $form_entry_2 = NULL;
}
?>
