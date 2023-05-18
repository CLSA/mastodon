<?php
/**
 * participant_data_template.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @fileparticipant_data_template
 */

namespace mastodon\database;
use cenozo\lib, cenozo\log, cenozo\util;

/**
 * participant_data_template: record
 */
class participant_data_template extends \cenozo\database\record
{
  /**
   * Writes the template to disk
   */
  public function create_template_file()
  {
    file_put_contents(
      $this->get_filename(),
      base64_decode( $this->data )
    );
  }

  /**
   * Deletes the template from disk
   */
  public function delete_template_file()
  {
    $filename = $this->get_filename();
    if( file_exists( $filename ) ) unlink( $filename );
  }

  /**
   * Gets the path of the template when written to disk
   * @return string
   * @access public
   */
  public function get_filename()
  {
    return sprintf(
      '%s/participant_data_template_%d.pdf',
      TEMP_PATH,
      $this->id
    );
  }
}
