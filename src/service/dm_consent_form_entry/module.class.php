<?php
/**
 * module.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace mastodon\service\dm_consent_form_entry;
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

    $modifier->left_join( 'user', 'dm_consent_form_entry.user_id', 'user.id' );
    $modifier->left_join( 'participant', 'dm_consent_form_entry.participant_id', 'participant.id' );
    $modifier->left_join( 'alternate', 'dm_consent_form_entry.alternate_id', 'alternate.id' );
    $select->add_table_column( 'participant', 'uid' );
    $select->add_column( 'CONCAT_WS( " ", alternate.first_name, alternate.last_name )', 'alternate_full_name', false );

    if( !is_null( $this->get_resource() ) )
    {
      // include the user's first/last/name as supplemental data
      $select->add_column(
        'CONCAT( user.first_name, " ", user.last_name, " (", user.name, ")" )',
        'formatted_user_id',
        false
      );

      // include the alternate first/last/type as supplemental data
      $modifier->left_join( 'alternate_has_alternate_type', 'alternate.id', 'alternate_has_alternate_type.alternate_id' );
      $modifier->left_join( 'alternate_type', 'alternate_has_alternate_type.alternate_type_id', 'alternate_type.id' );
      $modifier->group( 'alternate.id' );
      $select->add_column(
        'CONCAT( alternate.first_name, " ", alternate.last_name, " (", GROUP_CONCAT( alternate_type.title ), ")" )',
        'formatted_alternate_id',
        false
      );
    }
  }
}
