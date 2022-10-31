<?php
/**
 * decedent_responder.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace mastodon\business\report;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Contact report
 */
class decedent_responder extends \cenozo\business\report\base_report
{
  /**
   * Build the report
   * @access protected
   */
  protected function build()
  {
    $participant_class_name = lib::get_class_name( 'database\participant' );
    $alternate_type_class_name = lib::get_class_name( 'database\alternate_type' );
    $db_alternate_type = $alternate_type_class_name::get_unique_record( 'name', 'decedent' );

    $alternate_sel = lib::create( 'database\select' );
    $alternate_sel->from( 'alternate' );
    $alternate_sel->add_column( 'participant_id' );
    $alternate_sel->add_column( 'id', 'alternate_id' );
    $alternate_mod = lib::create( 'database\modifier' );
    $alternate_mod->join( 'alternate_has_alternate_type', 'alternate.id', 'alternate_has_alternate_type.alternate_id' );
    $alternate_mod->join( 'alternate_type', 'alternate_has_alternate_type.alternate_type_id', 'alternate_type.id' );
    $alternate_mod->where( 'alternate_type.name', '=', 'decedent' );
    $alternate_mod->group( 'participant_id' );
    $sql = sprintf(
      'CREATE TEMPORARY TABLE IF NOT EXISTS decedent ( '.
        'participant_id INT UNSIGNED NOT NULL, '.
        'alternate_id INT UNSIGNED NOT NULL, '.
        'KEY fk_participant_id ( participant_id ), '.
        'KEY fk_alternate_id ( alternate_id ) '.
      ') %s %s',
      $alternate_sel->get_sql(),
      $alternate_mod->get_sql()
    );
    $participant_class_name::db()->execute( $sql );

    $select = lib::create( 'database\select' );
    $modifier = lib::create( 'database\modifier' );

    $select->from( 'participant' );
    $select->add_column( 'cohort.name', 'Cohort', false );
    $select->add_column( 'language.name', 'Language', false );
    $select->add_column( 'uid', 'UID' );
    $this->add_application_identifier_columns( $select, $modifier );
    $select->add_column( 'honorific', 'Participant Honorific' );
    $select->add_column( 'first_name', 'Participant First Name' );
    $select->add_column( 'last_name', 'Participant Last Name' );
    $select->add_column( 'alternate.first_name', 'Alternate First Name', false);
    $select->add_column( 'alternate.last_name', 'Alternate Last Name', false);
    $select->add_column( 'address.address1', 'Address1', false);
    $select->add_column( 'address.address2', 'Address2', false);
    $select->add_column( 'address.city', 'City', false);
    $select->add_column( 'region.abbreviation', 'Province/State', false);
    $select->add_column( 'address.postcode', 'Postcode', false);
    $select->add_column( 'country.name', 'Country', false);

    $modifier->join( 'language', 'participant.language_id', 'language.id' );
    $modifier->join( 'cohort', 'participant.cohort_id', 'cohort.id' );

    $modifier->left_join( 'decedent', 'participant.id', 'decedent.participant_id' );
    $modifier->left_join( 'alternate', 'decedent.alternate_id', 'alternate.id' );
    $modifier->left_join( 'alternate_first_address', 'alternate.id', 'alternate_first_address.alternate_id' );
    $modifier->left_join( 'address', 'alternate_first_address.address_id', 'address.id' );
    $modifier->left_join( 'region', 'address.region_id', 'region.id' );
    $modifier->left_join( 'country', 'region.country_id', 'country.id' );

    // set up requirements
    $this->apply_restrictions( $modifier );

    $this->add_table_from_select( NULL, $participant_class_name::select( $select, $modifier ) );
  }
}
