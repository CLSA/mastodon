<?php
/**
 * module.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace mastodon\service\participant_data;
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

    $modifier->join( 'study_phase', 'participant_data.study_phase_id', 'study_phase.id' );
    $modifier->join( 'study', 'study_phase.study_id', 'study.id' );

    if( $select->has_column( 'available' ) )
    {
      $select->add_constant( false, 'available', 'boolean' );
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

    $db_participant_data = lib::create( 'database\participant_data', $row['id'] );
    $row['available'] = $db_participant_data->is_available( $db_participant );
  }
}
