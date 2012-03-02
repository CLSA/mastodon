<?php
/**
 * proxy_form_download.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\pull;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Contact form download data.
 * 
 * @package mastodon\ui
 */
class proxy_form_download extends \cenozo\ui\pull\base_download
{
  /**
   * Constructor
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Pull arguments.
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'proxy_form', $args );
    
    // determine the file to upload to the user
    $alternate_class_name = lib::get_class_name( 'database\alternate' );
    $db_alternate = lib::create( 'database\alternate', $this->get_argument( 'id' ) );
    $this->set_file_name( $db_alternate->get_proxy_form_file_name() );
  }
}
?>
