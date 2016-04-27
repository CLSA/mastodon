<?php
/**
 * module.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\service\consent_form_entry;
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
      $modifier->join( 'consent_form', 'consent_form_entry.consent_form_id', 'consent_form.id' );
      $select->add_column(
        'IF( consent_form.validated_consent_form_entry_id = consent_form_entry.id, true, false )',
        'validated',
        false );
    }

    // always add the user's name
    $modifier->join( 'user', 'consent_form_entry.user_id', 'user.id' );
    $select->add_column( 'CONCAT( user.first_name, " ", user.last_name, " (", user.name, ")" )', 'user', false );
  }
}
