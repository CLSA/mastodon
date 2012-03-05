<?php
/**
 * consent_form_download.class.php
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
class consent_form_download extends \cenozo\ui\pull\base_record
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
    parent::__construct( 'consent_form', 'download', $args );
  }

  // TODO: document
  public function get_file_name()
  {
    return $this->get_record()->id;
  }

  // TODO: document
  public function get_data_type()
  {
    return 'pdf';
  }

  // TODO: document
  public function finish()
  {
    return $this->get_record()->scan;
  }
}
?>
