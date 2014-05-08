<?php
/**
 * alternate_new.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: alternate new
 *
 * Create a new alternate.
 */
class alternate_new extends \cenozo\ui\push\alternate_new
{
  /**
   * Finishes the operation with any post-execution instructions that may be necessary.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function finish()
  {
    parent::finish();

    // if a form variable was included try to decode it and store it as a proxy form
    $form = $this->get_argument( 'form', NULL );
    if( !is_null( $form ) && ( $record->proxy || $record->informant ) )
    {
      $form_decoded = base64_decode( chunk_split( $form ) );
      if( false == $form_decoded )
        throw lib::create( 'exception\runtime', 'Unable to decode form argument.', __METHOD__ );

      // create a new proxy form
      $db_proxy_form = lib::create( 'database\proxy_form' );
      if( $record->proxy ) $db_proxy_form->proxy_alternate_id = $record->id;
      if( $record->informant ) $db_proxy_form->informant_alternate_id = $record->id;
      $db_proxy_form->date = util::get_datetime_object()->format( 'Y-m-d' );
      $db_proxy_form->complete = true;
      $db_proxy_form->save();

      // now write the form
      $db_proxy_form->write_form( $form_decoded );
    }
  }
}
