<?php
/**
 * patch.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace mastodon\service\participant;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Special service for handling the patch meta-resource
 */
class patch extends \cenozo\service\participant\patch
{
  /**
   * Override parent method
   */
  public function get_file_as_array()
  {
    // remove preferred_site_id from the patch array
    $patch_array = parent::get_file_as_array();
    if( array_key_exists( 'application_id', $patch_array ) )
    {
      try { $this->db_application = lib::create( 'database\application', $patch_array['application_id'] ); }
      catch( \cenozo\exception\runtime $e ) {} // handled in the validate method
      unset( $patch_array['application_id'] );
    }

    return $patch_array;
  }

  /**
   * Override parent method
   */
  protected function validate()
  {
    parent::validate();

    if( 300 > $this->status->get_code() )
    {
      // make sure that, if we are updating the preferred site, that an application is also included
      if( $this->update_preferred_site && is_null( $this->db_application ) ) $this->status->set_code( 400 );
    }
  }

  /**
   * Override parent method
   */
  protected function set_preferred_site()
  {
    $this->get_leaf_record()->set_preferred_site( $this->db_application, $this->preferred_site_id );
  }

  /**
   * Which application to set the preferred site for
   * @var database\application
   * @access protected
   */
  protected $db_application = NULL;
}
