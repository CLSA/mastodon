<?php
/**
 * module.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace mastodon\service\participant_data_template;
use cenozo\lib, cenozo\log, cenozo\util;

/**
 * Performs operations which effect how this module is used in a service
 */
class module extends \cenozo\service\module
{
  /**
   * Extend parent method
   */
  public function prepare_read( $select, $modifier )
  {
    parent::prepare_read( $select, $modifier );

    $modifier->join( 'language', 'participant_data_template.language_id', 'language.id' );
    $modifier->join( 'participant_data', 'participant_data_template.participant_data_id', 'participant_data.id' );
    $modifier->join( 'study_phase', 'participant_data.study_phase_id', 'study_phase.id' );
    $modifier->join( 'study', 'study_phase.study_id', 'study.id' );

    if( $select->has_column( 'participant_data_name' ) )
    {
      $select->add_column(
        'CONCAT( '.
          'study.name, ": ", '.
          'UPPER( study_phase.code ), " - ", '.
          'participant_data.category, " (", '.
          'participant_data.name, ")" '.
        ')',
        'participant_data_name',
        false
      );
    }

    $db_participant_data_template = $this->get_resource();
    if( !is_null( $db_participant_data_template ) )
    {
      if( $select->has_column( 'filename' ) )
      {
        $select->add_constant(
          sprintf( 'participant_data_template_%d.pdf', $db_participant_data_template->id ),
          'filename'
        );
      }
    }
  }

  /**
   * Extend parent method
   */
  public function post_read( &$row )
  {
    $participant_class_name = lib::get_class_name( 'database\participant' );

    $identifier = $this->get_argument( 'identifier', NULL );
    if( is_null( $identifier ) ) return;

    $db_participant = $participant_class_name::get_record_from_identifier( $identifier );
    if( is_null( $db_participant ) ) return;

    $db_participant_data_template = lib::create( 'database\participant_data_template', $row['id'] );
    $row['available'] = $db_participant_data_template->is_available( $db_participant );
  }
}
