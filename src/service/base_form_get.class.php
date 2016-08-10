<?php
/**
 * get.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\service;
use cenozo\lib, cenozo\log;

class base_form_get extends \cenozo\service\downloadable
{
  /**
   * Replace parent method
   */
  protected function get_downloadable_mime_type_list()
  {
    return array( 'application/pdf' );
  }

  /**
   * Replace parent method
   */
  protected function get_downloadable_public_name()
  {
    return sprintf( '%s %d.pdf',
                    ucwords( str_replace( '_', ' ', $this->get_leaf_subject() ) ),
                    $this->get_leaf_record()->id );
  }
  
  /**
   * Replace parent method
   */
  protected function get_downloadable_file_path()
  {
    return $this->get_leaf_record()->get_filename();
  }
}
