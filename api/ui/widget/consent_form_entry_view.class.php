<?php
/**
 * consent_form_entry_view.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget consent_form_entry view
 * 
 * @package mastodon\ui
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

    // add the entry values
    $this->add_item( 'uid', 'string', 'CLSA ID' );
    $this->add_item( 'option_1', 'boolean', 'Option #1' );
    $this->add_item( 'option_2', 'boolean', 'Option #2' );
    $this->add_item( 'date', 'date', 'Date' );
    $this->add_item( 'note', 'text', 'Note' );
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

    // set the entry values
    $this->set_item( 'uid', $this->get_record()->uid, false );
    $this->set_item( 'option_1', $this->get_record()->option_1, false );
    $this->set_item( 'option_2', $this->get_record()->option_2, false );
    $this->set_item( 'date', $this->get_record()->date, false );
    $this->set_item( 'note', $this->get_record()->note, false );

    $this->finish_setting_items();
  }
}
?>
