<?php
/**
 * get.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace mastodon\service\participant_data;
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
    return array( 'image/jpeg', 'application/pdf' );
  }

  /**
   * Replace parent method
   * 
   * When the client calls for a file we return the associated data belonging to the participant.
   */
  protected function get_downloadable_public_name()
  {
    $db_participant_data = $this->get_leaf_record();
    $db_study_phase = $db_participant_data->get_study_phase();
    return sprintf(
      '%s %s %s %s.%s',
      $db_study_phase->get_study()->name,
      strtoupper( $db_study_phase->code ),
      $db_participant_data->category,
      $db_participant_data->name,
      $db_participant_data->filetype
    );
  }

  /**
   * Replace parent method
   * 
   * When the client calls for a file we return the associated data belonging to the participant.
   */
  protected function get_downloadable_file_path()
  {
    return $this->get_leaf_record()->generate( $this->db_participant );
  }

  /**
   * Extend parent method
   */
  public function prepare()
  {
    $participant_class_name = lib::get_class_name( 'database\participant' );
    parent::prepare();

    // create the participant record if an identifier is provided
    $identifier = $this->get_argument( 'identifier', NULL );
    $this->db_participant = is_null( $identifier ) ?
      NULL : $participant_class_name::get_record_from_identifier( $identifier );
  }

  /**
   * Extend parent method
   */
  public function finish()
  {
    parent::finish();

    // clean up by deleting temporary files
    if( $this->get_argument( 'download', false ) && !is_null( $this->db_participant ) )
    {
      $filename = $this->get_leaf_record()->get_filename( $this->db_participant );
      if( file_exists( $filename ) ) unlink( $filename );
    }
  }

  /**
   * The participant's record, (only when getting the participant's data)
   * @var database\participant
   * @access private
   */
  private $db_participant = NULL;

  /**
   * The name of the temporary zip file containing the participant's forms
   * @var string
   * @access private
   */
  private $filename = NULL;
}
