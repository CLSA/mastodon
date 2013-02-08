<?php
/**
 * participant_view.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget participant view
 */
class participant_view extends \cenozo\ui\widget\participant_view
{
  /**
   * Finish setting the variables in a widget.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function setup()
  {
    parent::setup();

    // add a contact form download action
    $db_contact_form = $this->get_record()->get_contact_form();
    if( !is_null( $db_contact_form ) )
      $this->set_variable( 'contact_form_id', $db_contact_form->id );
    $this->add_action( 'contact_form', 'Contact Form', NULL,
      'Download this participant\'s contact form, if available' );
  }
}
