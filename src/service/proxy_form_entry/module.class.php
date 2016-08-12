<?php
/**
 * module.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\service\proxy_form_entry;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Performs operations which effect how this module is used in a service
 */
class module extends \mastodon\service\base_form_entry_module
{
  /**
   * Extend parent method
   */
  public function prepare_read( $select, $modifier )
  {
    parent::prepare_read( $select, $modifier );

    // include the user's first/last/name as supplemental data
    $modifier->left_join( 'user', 'proxy_form_entry.user_id', 'user.id' );
    $select->add_column(
      'CONCAT( user.first_name, " ", user.last_name, " (", user.name, ")" )',
      'formatted_user_id',
      false );

    // always add the proxy and informant region names
    $modifier->left_join(
      'region', 'proxy_form_entry.proxy_region_id', 'proxy_region.id', 'proxy_region' );
    $select->add_table_column( 'proxy_region', 'name', 'proxy_region' );
    $modifier->left_join(
      'region', 'proxy_form_entry.informant_region_id', 'informant_region.id', 'informant_region' );
    $select->add_table_column( 'informant_region', 'name', 'informant_region' );
  }
}
