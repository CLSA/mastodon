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

    $select = lib::create( 'database\select' );
    $select->from( 'participant' );
    $select->add_column( 'cohort.name', 'Cohort', false );
    $select->add_column( 'language.name', 'Language', false );
    $select->add_column( 'uid', 'UID' );
    $select->add_column( 'honorific', 'Alternate Honorific' );
    $select->add_column( 'first_name', 'Participant First Name' );
    $select->add_column( 'last_name', 'Participant Last Name' );
    $select->add_column( 'alternate.first_name', 'Alternate First Name', false );
    $select->add_column( 'alternate.last_name', 'Alternate Last Name', false );
    $select->add_column( 'address.address1', 'Address1', false );
    $select->add_column( 'address.address2', 'Address2', false );
    $select->add_column( 'address.city', 'City', false );
    $select->add_column( 'region.abbreviation', 'Province/State', false );
    $select->add_column( 'address.postcode', 'Postcode', false );
    $select->add_column( 'region.country', 'Country', false );

    $modifier = lib::create( 'database\modifier' );
    $modifier->join( 'language', 'participant.language_id', 'language.id' );
    $modifier->join( 'cohort', 'participant.cohort_id', 'cohort.id' );

    $join_mod = lib::create( 'database\modifier' );
    $join_mod->where( 'participant.id', '=', 'alternate.participant_id', false );
    $join_mod->where( 'alternate.decedent', '=', true );
    $modifier->join_modifier( 'alternate', $join_mod, 'left' );
    $modifier->left_join( 'alternate_first_address', 'alternate.id', 'alternate_first_address.alternate_id' );
    $modifier->left_join( 'address', 'alternate_first_address.address_id', 'address.id' );
    $modifier->left_join( 'region', 'address.region_id', 'region.id' );

    // set up requirements
    $this->apply_restrictions( $modifier );

    $this->add_table_from_select( NULL, $participant_class_name::select( $select, $modifier ) );
  }
}
