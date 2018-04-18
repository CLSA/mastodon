<?php
/**
 * get.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace mastodon\service\participant;
use cenozo\lib, cenozo\log, cenozo\util;

class get extends \cenozo\service\downloadable
{
  /**
   * Replace parent method
   * 
   * When the client calls for a file we return a zip file of all opal-forms for this participant
   */
  protected function get_downloadable_mime_type_list()
  {
    return array( 'application/zip' );
  }

  /**
   * Replace parent method
   * 
   * When the client calls for a file we return a zip file of all opal-forms for this participant
   */
  protected function get_downloadable_public_name()
  {
    return sprintf( '%s.zip', $this->get_leaf_record()->uid );
  }

  /**
   * Replace parent method
   * 
   * When the client calls for a file we return a zip file of all opal-forms for this participant
   */
  protected function get_downloadable_file_path()
  {
    $opal_form_template_class_name = lib::get_class_name( 'database\opal_form_template' );

    // generate opal forms, zip and return zip file
    $this->zip_filename = sprintf( '%s/%s.zip', TEMP_PATH, rand( 1000000000, 9999999999 ) );
    $zip = NULL;

    foreach( $opal_form_template_class_name::select_objects() as $db_opal_form_template )
    {
      if( is_null( $zip ) )
      {
        $zip = new \ZipArchive();
        if( true !== $zip->open( $this->zip_filename, \ZipArchive::CREATE ) )
        {
          throw lib::create( 'exception\runtime',
            sprintf(
              'Unable to create temporary zip file "%s" for opal forms.',
              $this->zip_filename ),
            __METHOD__ );
        }
      }
      $data = $db_opal_form_template->generate( $this->get_leaf_record() );
      if( !is_null( $data ) ) $zip->addFromString( $db_opal_form_template->name.'.pdf', $data );
    }

    if( is_null( $zip ) ) $this->get_status()->set_code( 404 );
    else $zip->close();

    return $this->zip_filename;
  }

  /**
   * Extend parent method
   */
  public function finish()
  {
    parent::finish();

    if( $this->get_argument( 'opal_forms', false ) &&
        !is_null( $this->zip_filename ) &&
        file_exists( $this->zip_filename ) ) unlink( $this->zip_filename );
  }

  /**
   * The name of the temporary zip file containing the participant's forms
   * @var string
   * @access private
   */
  private $zip_filename = NULL;
}
