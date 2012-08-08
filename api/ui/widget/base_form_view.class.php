<?php
/**
 * base_view.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Base class for widgets which view forms.
 * 
 * @abstract
 */
abstract class base_form_view
  extends \cenozo\ui\widget\base_record
  implements \cenozo\ui\widget\actionable
{
  /**
   * Constructor
   * 
   * Defines all variables which need to be set for the associated template.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param string $subject The subject being viewed.
   * @param array $args An associative array of arguments to be processed by the widget
   * @throws exception\argument
   * @access public
   */
  public function __construct( $subject, $args )
  {
    parent::__construct( $subject, 'view', $args );
  }

  /**
   * Processes arguments, preparing them for the operation.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @throws exception\notice
   * @access protected
   */
  protected function prepare()
  {
    parent::prepare();
    
    $id = $this->get_argument( 'id' );

    $this->set_heading( sprintf( 'Viewing %s #%d', $this->get_subject(), $id ) );

    // Set the two form entries
    $form_entry_list_method = sprintf( 'get_%s_entry_list', $this->get_subject() );
    $form_entry_list = $this->get_record()->$form_entry_list_method();
    $db_form_entry_1 = current( $form_entry_list );
    $db_form_entry_2 = next( $form_entry_list ); 

    $this->set_form_entries(
      false == $db_form_entry_1 ? NULL : $db_form_entry_1,
      false == $db_form_entry_2 ? NULL : $db_form_entry_2 );
  }
  
  /**
   * Sets up the operation with any pre-execution instructions that may be necessary.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function setup()
  {
    parent::setup();
    
    $operation_class_name = lib::get_class_name( 'database\operation' );

    // add in form actions
    $this->add_action( 'download', 'Download', NULL,
      'Download a PDF copy of the form' );
    $this->add_action( 'submit', 'Submit', NULL,
      'Submit the selected form values and process the form' );
    $this->add_action( 'invalidate', 'Invalidate', NULL,
      'Invalidate the form, removing it from the data entry system' );
    
    // validate the entries
    $error_list_1 = array();
    if( !is_null( $this->form_entry_1 ) )
    {
      $username = $this->form_entry_1->get_user()->name;
      $this->add_action( 'defer_'.$username, 'Defer '.$username, NULL,
        sprintf( 'Defer %s\'s form for further editing', $username ) );

      $args = array( 'id' => $this->form_entry_1->id );
      $operation = lib::create( sprintf( 'ui\pull\%s_entry_validate', $this->get_subject() ), $args );
      $operation->process();
      $error_list_1 = $operation->get_data();
    }
    $error_list_2 = array();
    if( !is_null( $this->form_entry_2 ) )
    {
      $username = $this->form_entry_2->get_user()->name;
      $this->add_action( 'defer_'.$username, 'Defer '.$username, NULL,
        sprintf( 'Defer %s\'s form for further editing', $username ) );

      $args = array( 'id' => $this->form_entry_2->id );
      $operation = lib::create( sprintf( 'ui\pull\%s_entry_validate', $this->get_subject() ), $args );
      $operation->process();
      $error_list_2 = $operation->get_data();
    }

    foreach( $this->items as $item_id => $item )
    {
      // get the user, error and value for the first entry
      $entry = array( 'user' => 'n/a',
                      'error' => false,
                      'value' => '(no value)' );
      if( !is_null( $this->form_entry_1 ) )
      {
        $entry['user'] = sprintf( '%s%s',
                                  $this->form_entry_1->get_user()->name,
                                  $this->form_entry_1->deferred ? ' (deferred)' : '' );

        if( array_key_exists( $item_id, $error_list_1 ) )
          $entry['error'] = $error_list_1[$item_id];

        if( !is_null( $this->form_entry_1->$item_id ) )
        {
          if( preg_match( '/region_id/', $item_id ) )
          {
            $db_region = lib::create( 'database\region', $this->form_entry_1->$item_id );
            $entry['value'] = $db_region->name.', '.$db_region->country;
          }
          else $entry['value'] = $this->form_entry_1->$item_id;
        }
      }
      $this->items[$item_id]['entry_1'] = $entry;

      // get the user, error and value for the second entry
      $entry = array( 'user' => 'n/a',
                      'error' => false,
                      'value' => '(no value)' );
      if( !is_null( $this->form_entry_2 ) )
      {
        $entry['user'] = sprintf( '%s%s',
                                  $this->form_entry_2->get_user()->name,
                                  $this->form_entry_2->deferred ? ' (deferred)' : '' );

        if( array_key_exists( $item_id, $error_list_2 ) )
          $entry['error'] = $error_list_2[$item_id];

        if( !is_null( $this->form_entry_2->$item_id ) )
        {
          if( preg_match( '/region_id/', $item_id ) )
          {
            $db_region = lib::create( 'database\region', $this->form_entry_2->$item_id );
            $entry['value'] = $db_region->name.', '.$db_region->country;
          }
          else $entry['value'] = $this->form_entry_2->$item_id;
        }
      }
      $this->items[$item_id]['entry_2'] = $entry;

      // set whether the item is in conflict
      $this->items[$item_id]['conflict'] =
        ( is_string( $this->items[$item_id]['entry_1']['value'] ) &&
          0 != strcasecmp( $this->items[$item_id]['entry_1']['value'],
                           $this->items[$item_id]['entry_2']['value'] ) ) ||
        ( !is_string( $this->items[$item_id]['entry_1']['value'] ) &&
          $this->items[$item_id]['entry_1']['value'] !=
          $this->items[$item_id]['entry_2']['value'] );
    }

    $this->set_variable( 'entry_1', is_null( $this->form_entry_1 )
      ? array( 'exists' => false )
      : array( 'exists' => true,
               'id' => $this->form_entry_1->id,
               'deferred' => $this->form_entry_1->deferred,
               'user' => $this->form_entry_1->get_user()->name ) );
    $this->set_variable( 'entry_2', is_null( $this->form_entry_2 )
      ? array( 'exists' => false )
      : array( 'exists' => true,
               'id' => $this->form_entry_2->id,
               'deferred' => $this->form_entry_2->deferred,
               'user' => $this->form_entry_2->get_user()->name ) );

    $this->set_variable( 'item', $this->items );
    $this->set_variable( 'allow_adjudication',
      !is_null( $this->form_entry_1 ) && !$this->form_entry_1->deferred &&
      !is_null( $this->form_entry_2 ) && !$this->form_entry_2->deferred );

    $this->set_variable( 'actions', $this->actions );
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
   * Adds a new action to the widget.
   * 
   * @param string $action_id The action's id (must be a valid HTML id name).
   * @param string $heading The action's heading as it will appear in the widget.
   * @param database\operation $db_operation The operation to perform.  If NULL then the button
   *        will appear in the interface without any action and the extending template is
   *        expected to implement the actions operation in the action_script block.
   * @param string $description Pop-up text to show when hovering over the action's button.
   * @access public
   */
  public function add_action( $action_id, $heading, $db_operation = NULL, $description = NULL )
  {
    $this->actions[$action_id] =
      array( 'heading' => $heading,
             'type' => is_null( $db_operation ) ? false : $db_operation->type,
             'subject' => is_null( $db_operation ) ? false : $db_operation->subject,
             'name' => is_null( $db_operation ) ? false : $db_operation->name,
             'description' => $description );
  }
  
  /**
   * Removes an action from the widget.
   * 
   * @param string $action_id The action's id (must be a valid HTML id name).
   * @access public
   */
  public function remove_action( $action_id )
  {
    if( array_key_exists( $action_id, $this->actions ) )
      unset( $this->actions[$action_id] );
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

  /**
   * The first user's entry for this form.
   * @var database\form_entry $form_entry_1
   * @access protected
   */
  protected $form_entry_1 = NULL;

  /**
   * The second user's entry for this form.
   * @var database\form_entry $form_entry_1
   * @access protected
   */
  protected $form_entry_2 = NULL;

  /**
   * An associative array where the key is a unique identifier and the value is an associative
   * array which includes:
   * "heading" => the label to display
   * "name" => the name of the operation to perform on the record
   * "description" => the popup help text
   * @var array
   * @access private
   */
  private $actions = array();
}
?>
