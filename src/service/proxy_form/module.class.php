<?php
/**
 * module.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\service\proxy_form;
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
    $modifier->join( 'proxy_form_total', 'proxy_form.id', 'proxy_form_total.proxy_form_id' );

    if( $select->has_column( 'validated' ) )
      $select->add_column( 'validated_proxy_form_entry_id IS NOT NULL', 'validated', false );

    if( $select->has_column( 'adjudicate' ) )
      $select->add_column(
        'NOT complete AND NOT invalid AND submitted_total > 1', 'adjudicate', false, 'boolean' );
  }
}
