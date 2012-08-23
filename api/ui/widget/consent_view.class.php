<?php
/**
 * consent_view.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget consent view
 */
class consent_view extends \cenozo\ui\widget\base_view
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
    parent::__construct( 'consent', 'view', $args );
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
    
    // add items to the view
    $this->add_item( 'event', 'enum', 'Event' );
    $this->add_item( 'date', 'date', 'Date' );
    $this->add_item( 'note', 'text', 'Note' );
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

    // add a consent form download action
    $db_consent_form = $this->get_record()->get_consent_form();
    if( !is_null( $db_consent_form ) )
      $this->set_variable( 'consent_form_id', $db_consent_form->id );
    $this->add_action( 'consent_form', 'Consent Form', NULL,
      'Download the form associated with this consent entry, if available' );

    // create enum arrays
    $class_name = lib::get_class_name( 'database\consent' );
    $events = $class_name::get_enum_values( 'event' );
    $events = array_combine( $events, $events );

    // set the view's items
    $this->set_item( 'event', $this->get_record()->event, true, $events );
    $this->set_item( 'date', $this->get_record()->date, true );
    $this->set_item( 'note', $this->get_record()->note );
  }
}
?>
