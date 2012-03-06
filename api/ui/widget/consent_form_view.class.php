<?php
/**
 * consent_form_view.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget consent_form view
 * 
 * @package mastodon\ui
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

    $this->add_item( 'option_1', 'Option #1' );
    $this->add_item( 'option_2', 'Option #2' );
    $this->add_item( 'date', 'Date' );

    // if there are two submitted entries for this form then display them both
    $consent_form_entry_mod = lib::create( 'database\modifier' );
    $consent_form_entry_mod->where( 'deferred', '=', false );

    $consent_form_entry_list = 
      $this->get_record()->get_consent_form_entry_list( $consent_form_entry_mod );
    $db_consent_form_entry_1 = current( $consent_form_entry_list );
    $db_consent_form_entry_2 = next( $consent_form_entry_list );

    $this->set_form_entries(
      false == $db_consent_form_entry_1 ? NULL : $db_consent_form_entry_1,
      false == $db_consent_form_entry_2 ? NULL : $db_consent_form_entry_2 );
  }
}
?>
