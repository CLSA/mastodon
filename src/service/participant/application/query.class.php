<?php
/**
 * query.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace mastodon\service\participant\application;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Extends parent class
 */
class query extends \cenozo\service\query
{
  /**
   * Extends parent method
   */
  protected function get_record_count()
  {
    $application_class_name = lib::create( 'database\application' );
    $db_participant = $this->get_parent_record();
    $modifier = clone $this->modifier;
    
    // need to change existing join to participant_site (to include all applications)
    $join_mod = lib::create( 'database\modifier' );
    $join_mod->where( 'application.id', '=', 'participant_site.application_id', false );
    $join_mod->where( 'participant_site.participant_id', '=', $db_participant->id );
    $modifier->join_modifier( 'participant_site', $join_mod, '', NULL, true );

    // add new join to application_has_participant
    $join_mod = lib::create( 'database\modifier' );
    $join_mod->where( 'application.id', '=', 'application_has_participant.application_id', false );
    $join_mod->where( 'application_has_participant.participant_id', '=', $db_participant->id );
    $modifier->join_modifier( 'application_has_participant', $join_mod, 'left', NULL, true );

    // find aliases in the select and translate them in the modifier
    $this->select->apply_aliases_to_modifier( $modifier );

    return $application_class_name::count( $modifier );
  }

  /**
   * Extends parent method
   */
  protected function get_record_list()
  {
    $application_class_name = lib::create( 'database\application' );
    $db_participant = $this->get_parent_record();
    $modifier = clone $this->modifier;
    
    // need to change existing join to participant_site (to include all applications)
    $join_mod = lib::create( 'database\modifier' );
    $join_mod->where( 'application.id', '=', 'participant_site.application_id', false );
    $join_mod->where( 'participant_site.participant_id', '=', $db_participant->id );
    $modifier->join_modifier( 'participant_site', $join_mod, '', NULL, true );

    // add new join to application_has_participant
    $join_mod = lib::create( 'database\modifier' );
    $join_mod->where( 'application.id', '=', 'application_has_participant.application_id', false );
    $join_mod->where( 'application_has_participant.participant_id', '=', $db_participant->id );
    $modifier->join_modifier( 'application_has_participant', $join_mod, 'left', NULL, true );

    // find aliases in the select and translate them in the modifier
    $this->select->apply_aliases_to_modifier( $modifier );

    return $application_class_name::select( $this->select, $modifier );
  }
}
