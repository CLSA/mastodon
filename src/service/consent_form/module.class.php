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
    if( $select->has_column( 'entry_count' ) ) $select->add_constant( 0, 'entry_count' );

    // add the total number of entries
    if( $select->has_column( 'submitted_entry_count' ) ) $select->add_constant( 0, 'submitted_entry_count' );
  }
}
