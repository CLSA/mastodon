<?php
/**
 * module.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\service\consent_form;
use cenozo\lib, cenozo\log, mastodon\util;

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

    // add the total number of entries
    if( $select->has_column( 'entry_count' ) )
    {
      $join_sel = lib::create( 'database\select' );
      $join_sel->from( 'consent_form_entry' );
      $join_sel->add_column( 'consent_form_id' );
      $join_sel->add_column( 'COUNT( * )', 'entry_count', false );

      $join_mod = lib::create( 'database\modifier' );
      $join_mod->group( 'consent_form_id' );

      $join_sel = lib::create( 'database\select' );
      $join_sel->from( 'consent_form' );
      $join_sel->add_column( 'id', 'consent_form_id' );
      $join_sel->add_column( 'IF( consent_form_entry.id IS NULL, 0, COUNT(*) )', 'entry_count', false );

      $join_mod = lib::create( 'database\modifier' );
      $join_mod->left_join( 'consent_form_entry', 'consent_form.id', 'consent_form_entry.consent_form_id' );
      $join_mod->group( 'consent_form.id' );

      $modifier->join(
        sprintf( '( %s %s ) AS consent_form_join_entry', $join_sel->get_sql(), $join_mod->get_sql() ),
        'consent_form.id',
        'consent_form_join_entry.consent_form_id' );
      $select->add_column( 'IFNULL( entry_count, 0 )', 'entry_count', false );
    }

    // add the total number of entries
    if( $select->has_column( 'submitted_entry_count' ) )
    {
      $join_sel = lib::create( 'database\select' );
      $join_sel->from( 'consent_form' );
      $join_sel->add_column( 'id', 'consent_form_id' );
      $join_sel->add_column( 'IF( consent_form_entry.id IS NULL, 0, COUNT(*) )', 'submitted_entry_count', false );

      $join_mod = lib::create( 'database\modifier' );
      $join_mod->left_join( 'consent_form_entry', 'consent_form.id', 'consent_form_entry.consent_form_id' );
      $join_mod->where( 'IFNULL( deferred, true )', '=', false );
      $join_mod->group( 'consent_form.id' );

      $modifier->join(
        sprintf( '( %s %s ) AS consent_form_join_submitted_entry', $join_sel->get_sql(), $join_mod->get_sql() ),
        'consent_form.id',
        'consent_form_join_submitted_entry.consent_form_id' );
      $select->add_column( 'IFNULL( submitted_entry_count, 0 )', 'submitted_entry_count', false );
    }
  }
}
