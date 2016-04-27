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
class module extends \cenozo\service\module
{
  /**
   * Extend parent method
   */
  public function prepare_read( $select, $modifier )
  {
    parent::prepare_read( $select, $modifier );

    if( $select->has_column( 'validated' ) )
    {
      $modifier->join( 'proxy_form', 'proxy_form_entry.proxy_form_id', 'proxy_form.id' );
      $select->add_column(
        'IF( proxy_form.validated_proxy_form_entry_id = proxy_form_entry.id, true, false )',
        'validated',
        false );
    }

    // always add the user's name
    $modifier->join( 'user', 'proxy_form_entry.user_id', 'user.id' );
    $select->add_column( 'CONCAT( user.first_name, " ", user.last_name, " (", user.name, ")" )', 'user', false );

    // always add the proxy and informant region names
    $modifier->left_join(
      'region', 'proxy_form_entry.proxy_region_id', 'proxy_region.id', 'proxy_region' );
    $select->add_table_column( 'proxy_region', 'name', 'proxy_region' );
    $modifier->left_join(
      'region', 'proxy_form_entry.informant_region_id', 'informant_region.id', 'informant_region' );
    $select->add_table_column( 'informant_region', 'name', 'informant_region' );
  }
}
