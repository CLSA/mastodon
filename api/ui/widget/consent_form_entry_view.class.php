<?php
/**
 * consent_form_entry_view.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget consent_form_entry view
 */
class consent_form_entry_view extends base_form_entry_view
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
    parent::__construct( 'consent', $args );
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

    // add the entry values
    $this->add_item( 'uid', 'string', 'CLSA ID' );
    $this->add_item( 'option_1', 'boolean', 'Option #1' );
    $this->add_item( 'option_2', 'boolean', 'Option #2' );
    $this->add_item( 'signed', 'boolean', 'Signed' );
    $this->add_item( 'date', 'date', 'Date Signed' );
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

    // set the entry values
    $this->set_item( 'uid', $this->get_record()->uid, false );
    $this->set_item( 'option_1', $this->get_record()->option_1, true );
    $this->set_item( 'option_2', $this->get_record()->option_2, true );
    $this->set_item( 'signed', $this->get_record()->signed, true );
    $this->set_item( 'date', $this->get_record()->date, false );
  }
}
?>
