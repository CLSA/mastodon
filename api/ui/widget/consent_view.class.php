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
class consent_view extends \cenozo\ui\widget\consent_view
{
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
  }
}
