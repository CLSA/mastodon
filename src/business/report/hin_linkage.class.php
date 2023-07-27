<?php
/**
 * hin_linkage.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace mastodon\business\report;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Contact report
 */
class hin_linkage extends \cenozo\business\report\base_report
{
  /**
   * Build the report
   * @access protected
   */
  protected function build()
  {
    $participant_class_name = lib::get_class_name( 'database\participant' );

    // determine which identifier to include in the report
    $db_identifier = NULL;
    foreach( $this->get_restriction_list() as $restriction )
      if( 'identifier' == $restriction['name'] && '_NULL_' != $restriction['value'] )
        $db_identifier = lib::create( 'database\identifier', $restriction['value'] );

    $select = lib::create( 'database\select' );
    $modifier = lib::create( 'database\modifier' );
    $modifier->where( 'participant.exclusion_id', '=', NULL );

    $select->from( 'participant' );
    if( is_null( $db_identifier ) ) $select->add_column( 'uid', 'UID' );
    else $select->add_column( 'participant_identifier.value', sprintf( '%s ID', $db_identifier->name ), false );
    $select->add_column( 'participant.first_name', 'First Name', false );
    $select->add_column( 'participant.other_name', 'Other Name', false );
    $select->add_column( 'participant.last_name', 'Last Name', false );
    $select->add_column( 'participant.date_of_birth', 'Date of Birth', false );
    $select->add_column( 'participant.sex', 'Sex at Birth', false );
    $select->add_column( 'participant.date_of_death', 'Date of Death', false );

    if( !is_null( $db_identifier ) )
    {
      $join_mod = lib::create( 'database\modifier' );
      $join_mod->where( 'participant.id', '=', 'participant_identifier.participant_id', false );
      $join_mod->where( 'participant_identifier.identifier_id', '=', $db_identifier->id );
      $modifier->join_modifier( 'participant_identifier', $join_mod, 'left' );
    }

    $modifier->order( is_null( $db_identifier ) ? 'participant.uid' : 'participant_identifier.value' );

    // make sure the last HIN consent is accepted
    $modifier->join(
      'participant_last_consent',
      'participant.id',
      'participant_last_hin_consent.participant_id',
      '',
      'participant_last_hin_consent'
    );
    $modifier->join(
      'consent_type',
      'participant_last_hin_consent.consent_type_id',
      'hin_consent_type.id',
      '',
      'hin_consent_type'
    );
    $modifier->join(
      'consent',
      'participant_last_hin_consent.consent_id',
      'hin_consent.id',
      '',
      'hin_consent'
    );
    $modifier->where( 'hin_consent_type.name', '=', 'HIN access' );
    $modifier->where( 'hin_consent.accept', '=', true );

    // make sure the last CIHI consent is not rejected
    $modifier->join(
      'participant_last_consent',
      'participant.id',
      'participant_last_cihi_consent.participant_id',
      '',
      'participant_last_cihi_consent'
    );
    $modifier->join(
      'consent_type',
      'participant_last_cihi_consent.consent_type_id',
      'cihi_consent_type.id',
      '',
      'cihi_consent_type'
    );
    $modifier->left_join(
      'consent',
      'participant_last_cihi_consent.consent_id',
      'cihi_consent.id',
      'cihi_consent'
    );
    $modifier->where( 'cihi_consent_type.name', '=', 'CIHI Access' );
    $modifier->where( 'IFNULL( cihi_consent.accept, true )', '!=', false );

    // make sure the last extneded HIN consent is not rejected
    $modifier->join(
      'participant_last_consent',
      'participant.id',
      'participant_last_extended_hin_consent.participant_id',
      '',
      'participant_last_extended_hin_consent'
    );
    $modifier->join(
      'consent_type',
      'participant_last_extended_hin_consent.consent_type_id',
      'extended_hin_consent_type.id',
      '',
      'extended_hin_consent_type'
    );
    $modifier->left_join(
      'consent',
      'participant_last_extended_hin_consent.consent_id',
      'extended_hin_consent.id',
      'extended_hin_consent'
    );
    $modifier->where( 'extended_hin_consent_type.name', '=', 'Extended HIN Access' );
    $modifier->where( 'IFNULL( extended_hin_consent.accept, true )', '!=', false );

    // make sure the last extended CIHI consent is not rejected
    $modifier->join(
      'participant_last_consent',
      'participant.id',
      'participant_last_extended_cihi_consent.participant_id',
      '',
      'participant_last_extended_cihi_consent'
    );
    $modifier->join(
      'consent_type',
      'participant_last_extended_cihi_consent.consent_type_id',
      'extended_cihi_consent_type.id',
      '',
      'extended_cihi_consent_type'
    );
    $modifier->left_join(
      'consent',
      'participant_last_extended_cihi_consent.consent_id',
      'extended_cihi_consent.id',
      'extended_cihi_consent'
    );
    $modifier->where( 'extended_cihi_consent_type.name', '=', 'Extended CIHI Access' );
    $modifier->where( 'IFNULL( extended_cihi_consent.accept, true )', '!=', false );

    $this->apply_restrictions( $modifier );

    // setup the identifier table
    $id_select = lib::create( 'database\select' );
    $id_select->from( 'participant' );
    $id_select->add_column( 'uid', 'UID' );
    $id_modifier = clone $modifier;

    $this->add_table_from_select(
      '### IDENTIFIER DATA ###',
      $participant_class_name::select( $id_select, $id_modifier )
    );

    // setup the HIN table
    $hin_select = clone $select;
    $hin_select->add_column( 'hin.code', 'HIN', false );
    $hin_select->add_column( 'region.name', 'Province', false );
    $hin_select->add_column(
      'DATE( CONVERT_TZ( hin.datetime, "UTC", "Canada/Eastern" ) )',
      'HIN date',
      false
    );
    $hin_modifier = clone $modifier;
    $hin_modifier->join( 'hin', 'participant.id', 'hin.participant_id' );
    $hin_modifier->left_join( 'region', 'hin.region_id', 'region.id' );

    $this->add_table_from_select(
      '### HIN DATA ###',
      $participant_class_name::select( $hin_select, $hin_modifier )
    );

    // setup the address table
    $address_select = clone $select;
    $address_select->add_column( 'CONCAT_WS( " ", address.address1, address.address2 )', 'Address', false );
    $address_select->add_column( 'address.city', 'City', false );
    $address_select->add_column( 'region.name', 'Province', false );
    $address_select->add_column( 'address.postcode', 'Postal Code', false );
    $address_select->add_column(
      'IF( address.create_timestamp = 0, "Unknown", DATE( address.create_timestamp ) )',
      'Address date',
      false
    );
    $address_modifier = clone $modifier;
    $address_modifier->join( 'address', 'participant.id', 'address.participant_id' );
    $address_modifier->join( 'region', 'address.region_id', 'region.id' );
    $address_modifier->join( 'country', 'region.country_id', 'country.id' );
    $address_modifier->where( 'country.name', '=', 'Canada' );

    $this->add_table_from_select(
      '### ADDRESS DATA ###',
      $participant_class_name::select( $address_select, $address_modifier )
    );
  }
}
