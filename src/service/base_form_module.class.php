<?php
/**
 * module.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\service;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Performs operations which effect how this module is used in a service
 */
abstract class base_form_module extends \cenozo\service\module
{
  /**
   * Extend parent method
   */
  public function validate()
  {
    // do not allow completed forms to be edited
    if( 'PATCH' == $this->get_method() )
    {
      if( $this->get_resource()->completed )
      {
        $this->set_data( 'Once a form has been completed it cannot be changed.' );
        $this->get_status()->set_code( 306 );
      }
    }
  }

  /**
   * Extend parent method
   */
  public function prepare_read( $select, $modifier )
  {
    parent::prepare_read( $select, $modifier );

    $form_name = $this->get_subject();
    $form_entry_name = $form_name.'_entry';

    // add the total number of entries
    $modifier->join( $form_name.'_total', $form_name.'.id', sprintf( '%s_total.%s_id', $form_name, $form_name ) );

    if( $select->has_column( 'validated' ) )
      $select->add_column( sprintf( 'validated_%s_id IS NOT NULL', $form_entry_name ), 'validated', false );

    if( $select->has_column( 'adjudicate' ) )
      $select->add_column(
        'NOT completed AND NOT invalid AND submitted_total > 1', 'adjudicate', false, 'boolean' );

    if( $select->has_column( 'cohort' ) || $select->has_column( 'uid' ) )
    {
      $join_sel = lib::create( 'database\select' );
      $join_sel->from( $form_name );
      $join_sel->add_column( 'id' );
      $join_sel->add_column(
        'GROUP_CONCAT( DISTINCT participant.uid ORDER BY participant.uid SEPARATOR "," )',
        'uid',
        false
      );
      $join_sel->add_column(
        'GROUP_CONCAT( DISTINCT cohort.name ORDER BY cohort.name SEPARATOR "," )',
        'cohort',
        false
      );

      $join_mod = lib::create( 'database\modifier' );
      $join_mod->left_join( $form_entry_name, $form_name.'.id', $form_entry_name.'.'.$form_name.'_id' );
      $join_mod->left_join( 'participant', $form_entry_name.'.uid', 'participant.uid' );
      $join_mod->left_join( 'cohort', 'participant.cohort_id', 'cohort.id' );
      $join_mod->group( $form_name.'.id' );

      $modifier->join(
        sprintf( '( %s %s )', $join_sel->get_sql(), $join_mod->get_sql() ),
        $form_name.'.id',
        'join_participant.id',
        '',
        'join_participant'
      );

      if( $select->has_column( 'cohort' ) )
      {
        $select->remove_column( 'cohort' );
        $select->add_table_column( 'join_participant', 'cohort' );
      }

      if( $select->has_column( 'uid' ) )
      {
        $select->remove_column( 'uid' );
        $select->add_table_column( 'join_participant', 'uid' );
      }
    }

    if( $select->has_column( 'status' ) )
    {
      $select->add_column(
        'IF( completed, "completed", '.
          'IF( invalid, "invalid", '.
            'IF( submitted_total > 1, "adjudication", '.
              'IF( entry_total > 0, "started", "new" ) '.
            ') '.
          ') '.
        ')',
        'status',
        false );
    }
  }
}
