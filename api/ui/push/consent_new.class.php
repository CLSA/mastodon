<?php
/**
 * consent_new.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: consent new
 *
 * Create a new consent.
 */
class consent_new extends \cenozo\ui\push\consent_new
{
  /**
   * This method executes the operation's purpose.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function execute()
  {
    parent::execute();

    // if a form variable was included try to decode it and store it as a consent form
    $form = $this->get_argument( 'form', NULL );
    if( !is_null( $form ) )
    {
      $form_decoded = base64_decode( chunk_split( $form ) );
      if( false == $form_decoded )
        throw lib::create( 'exception\runtime', 'Unable to decode form argument.', __METHOD__ );

      // create a new consent form
      $db_consent_form = lib::create( 'database\consent_form' );
      $db_consent_form->consent_id = $this->get_record()->id;
      $db_consent_form->date = util::get_datetime_object()->format( 'Y-m-d' );
      $db_consent_form->scan = $form_decoded;
      $db_consent_form->complete = true;
      $db_consent_form->save();
    }
  }
}
