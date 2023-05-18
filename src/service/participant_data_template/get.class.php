<?php
/**
 * get.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace mastodon\service\participant_data_template;
use cenozo\lib, cenozo\log, cenozo\util;

class get extends \cenozo\service\downloadable
{
  /**
   * Replace parent method
   * 
   * When the client calls for a file we return the associated data belonging to the participant.
   */
  protected function get_downloadable_mime_type_list()
  {
    return array( 'application/pdf' );
  }

  /**
   * Replace parent method
   * 
   * When the client calls for a file we return the associated data belonging to the participant.
   */
  protected function get_downloadable_public_name()
  {
    $file = $this->get_argument( 'file', NULL );
    if( 'filename' == $file ) return sprintf( 'participant_data_template_%d.pdf', $this->get_leaf_record()->id );

    throw lib::create( 'exception\argument', 'file', $file, __METHOD__ );
  }

  /**
   * Replace parent method
   * 
   * When the client calls for a file we return the associated data belonging to the participant.
   */
  protected function get_downloadable_file_path()
  {
    $db_participant_data_template = $this->get_leaf_record();

    $file = $this->get_argument( 'file', NULL );
    if( 'filename' == $file )
    {
      $db_participant_data_template->create_template_file();
      return $db_participant_data_template->get_filename();
    }

    throw lib::create( 'exception\argument', 'file', $file, __METHOD__ );
  }

  /**
   * Extend parent method
   */
  public function execute()
  {
    if( 'application/json' == $this->get_mime_type() && $this->get_argument( 'file', false ) )
    {
      $db_participant_data_template = $this->get_leaf_record();
      $path = $this->get_downloadable_file_path();
      if( !is_null( $db_participant_data_template ) && file_exists( $path ) )
        $this->set_data( stat( $path )['size'] );
    }
    else
    {
      parent::execute();
    }
  }
}
