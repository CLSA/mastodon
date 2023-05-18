<?php
/**
 * patch.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace mastodon\service\participant_data\participant_data_template;
use cenozo\lib, cenozo\log, mastodon\util;

class patch extends \cenozo\service\patch
{
  /**
   * Extend parent method
   */
  public function setup()
  {
    parent::setup();

    $file = $this->get_argument( 'file', NULL );
    if( false !== strpos( util::get_header( 'Content-Type' ), 'application/pdf' ) && !is_null( $file ) )
    {
      if( 'filename' != $file ) throw lib::create( 'exception\argument', 'file', $file, __METHOD__ );

      try
      {
        $this->get_leaf_record()->data = base64_encode( $this->get_file_as_raw() );
      }
      catch( \cenozo\exception\argument $e )
      {
        $this->status->set_code( 400 );
        throw $e;
      }
    }
  }
}
