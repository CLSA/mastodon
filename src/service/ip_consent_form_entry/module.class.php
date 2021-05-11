<?php
/**
 * module.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace mastodon\service\ip_consent_form_entry;
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

    $modifier->left_join( 'user', 'ip_consent_form_entry.user_id', 'user.id' );

    if( !is_null( $this->get_resource() ) )
    {
      // include the user's first/last/name as supplemental data
      $select->add_column(
        'CONCAT( user.first_name, " ", user.last_name, " (", user.name, ")" )',
        'formatted_user_id',
        false
      );

      // include the alternate first/last/type as supplemental data
      $modifier->left_join( 'alternate', 'ip_consent_form_entry.alternate_id', 'alternate.id' );
      $select->add_column(
        'CONCAT( alternate.first_name, " ", alternate.last_name, " (", IF('.
          'proxy AND informant, '.
          '"information provider and information provider", '.
          'IF( proxy, "information provider", "information provider" ) '.
        '), ")" )',
        'formatted_alternate_id',
        false
      );
    }
  }
}
