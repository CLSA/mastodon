<?php
/**
 * base_form_download.class.php
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
abstract class base_form_download extends \cenozo\ui\pull\base_record
{
  /**
   * Constructor
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param string $form_type The type of form being downloaded.
   * @param array $args Pull arguments.
   * @access public
   */
  public function __construct( $form_type, $args )
  {
    parent::__construct( $form_type.'_form', 'download', $args );
    $this->form_type = $form_type;
  }

  /**
   * Returns the file name for the form.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @return string
   * @access public
   */
  public function get_file_name()
  {
    return $this->get_record()->id;
  }

  /**
   * Returns the file data type (extension) for the form (always pdf)
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @return string
   * @access public
   */
  public function get_data_type()
  {
    return 'pdf';
  }

  /**
   * Returns the form's scan.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @return binary string
   * @access public
   */
  public function finish()
  {
    return $this->get_record()->scan;
  }

  /**
   * The type of form (ie: consent, contact, proxy)
   * @var string $form_type;
   * @access private
   */
  private $form_type;
}
?>
