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
