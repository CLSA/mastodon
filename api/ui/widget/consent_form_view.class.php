<?php
/**
 * consent_form_view.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget consent_form view
 */
class consent_form_view extends base_form_view
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
    parent::__construct( 'consent_form', $args );
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

    // add entry values
    $this->add_item( 'uid', 'CLSA ID' );
    $this->add_item( 'option_1', 'Option #1' );
    $this->add_item( 'option_2', 'Option #2' );
    $this->add_item( 'signed', 'Signed' );
    $this->add_item( 'date', 'Date Signed' );
  }
}
?>
