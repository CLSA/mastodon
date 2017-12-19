<?php
/**
 * general_proxy_form_entry.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace mastodon\database;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * general_proxy_form_entry: record
 */
class general_proxy_form_entry extends base_form_entry
{
  /**
   * Override parent method to make sure 6-character postal codes get a space
   * 
   * @param string $column_name The name of the column
   * @param mixed $value The value to set the contents of a column to
   * @throws exception\argument
   * @access public
   */
  public function __set( $column_name, $value )
  {
    if( ( 'proxy_postcode' == $column_name || 'informant_postcode' == $column_name ) &&
        is_string( $value ) && 6 == strlen( $value ) )
      $value = sprintf( '%s %s', substr( $value, 0, 3 ), substr( $value, 3, 3 ) );

    parent::__set( $column_name, $value );
  }

  /**
   * Returns the errors found by processing this entry
   * 
   * @return associative array
   * @access public
   */
  public function get_errors()
  {
    $postcode_class_name = lib::get_class_name( 'database\postcode' );

    $errors = parent::get_errors();

    $proxy = false;
    if( !is_null( $this->proxy_first_name ) ||
        !is_null( $this->proxy_last_name ) ||
        !is_null( $this->proxy_apartment_number ) ||
        !is_null( $this->proxy_street_number ) ||
        !is_null( $this->proxy_street_name ) ||
        !is_null( $this->proxy_box ) ||
        !is_null( $this->proxy_rural_route ) ||
        !is_null( $this->proxy_address_other ) ||
        !is_null( $this->proxy_city ) ||
        !is_null( $this->proxy_region_id ) ||
        !is_null( $this->proxy_postcode ) ||
        !is_null( $this->proxy_address_note ) ||
        !is_null( $this->proxy_phone ) ||
        !is_null( $this->proxy_phone_note ) ||
        !is_null( $this->proxy_note ) )
    {
      $proxy = true;

      if( is_null( $this->proxy_first_name ) )
        $errors['proxy_first_name'] = 'Cannot be blank.';

      if( is_null( $this->proxy_last_name ) )
        $errors['proxy_last_name'] = 'Cannot be blank.';

      if( is_null( $this->proxy_street_number ) xor is_null( $this->proxy_street_name ) )
      {
        $name = is_null( $this->proxy_street_number )
              ? 'proxy_street_number' : 'proxy_street_name';
        $errors[$name] = 'Street address must include both the number and name.';
      }

      if( is_null( $this->proxy_street_name ) &&
          is_null( $this->proxy_box ) &&
          is_null( $this->proxy_address_other ) )
      {
        $error = 'At least one of "Street Name", "PO Box" or "Other Address" must be specified.';
        $errors['proxy_street_name'] = $error;
        $errors['proxy_box'] = $error;
        $errors['proxy_address_other'] = $error;
      }

      if( !is_null( $this->proxy_box ) && !util::string_matches_int( $this->proxy_box ) )
        $errors['proxy_box'] = 'Must be a number only (do not include PO, # or Box).';

      if( !is_null( $this->proxy_rural_route ) && !util::string_matches_int( $this->proxy_rural_route ) )
        $errors['proxy_rural_route'] = 'Must be a number only (do not include RR or #).';

      if( is_null( $this->proxy_city ) )
        $errors['proxy_city'] = 'Cannot be blank.';

      if( is_null( $this->proxy_region_id ) )
        $errors['proxy_region_id'] = 'Cannot be blank.';

      if( is_null( $this->proxy_postcode ) )
        $errors['proxy_postcode'] = 'Cannot be blank.';

      if( !is_null( $this->proxy_region_id ) && !is_null( $this->proxy_postcode ) )
      { // check that the postal code is valid
        $db_postcode = $postcode_class_name::get_match( $this->proxy_postcode );
        if( is_null( $db_postcode ) || $db_postcode->region_id != $this->proxy_region_id )
        {
          $errors['proxy_postcode'] = 'The postal code does not exist in the selected province.';
        }
        else
        {
          $db_address = lib::create( 'database\address' );
          $db_address->region_id = $this->proxy_region_id;
          $db_address->postcode = $this->proxy_postcode;
          if( !$db_address->is_valid() )
            $errors['proxy_postcode'] = 'The postal code is invalid.';
        }
      }

      if( is_null( $this->proxy_phone ) )
        $errors['proxy_phone'] = 'Cannot be blank.';
      else if( !util::validate_north_american_phone_number( $this->proxy_phone ) )
        $errors['proxy_phone'] = 'Invalid phone number, please use XXX-XXX-XXXX format.';
    }

    $informant = false;
    if( !is_null( $this->informant_first_name ) ||
        !is_null( $this->informant_last_name ) ||
        !is_null( $this->informant_apartment_number ) ||
        !is_null( $this->informant_street_number ) ||
        !is_null( $this->informant_street_name ) ||
        !is_null( $this->informant_box ) ||
        !is_null( $this->informant_rural_route ) ||
        !is_null( $this->informant_address_other ) ||
        !is_null( $this->informant_city ) ||
        !is_null( $this->informant_region_id ) ||
        !is_null( $this->informant_postcode ) ||
        !is_null( $this->informant_address_note ) ||
        !is_null( $this->informant_phone ) ||
        !is_null( $this->informant_phone_note ) ||
        !is_null( $this->informant_note ) )
    {
      $informant = true;

      if( is_null( $this->informant_first_name ) )
        $errors['informant_first_name'] = 'Cannot be blank.';

      if( is_null( $this->informant_last_name ) )
        $errors['informant_last_name'] = 'Cannot be blank.';

      if( is_null( $this->informant_street_number ) xor is_null( $this->informant_street_name ) )
      {
        $name = is_null( $this->informant_street_number )
              ? 'informant_street_number' : 'informant_street_name';
        $errors[$name] = 'Street address must include both the number and name.';
      }

      if( is_null( $this->informant_street_name ) &&
          is_null( $this->informant_box ) &&
          is_null( $this->informant_address_other ) )
      {
        $error = 'At least one of "Street Name", "PO Box" or "Other Address" must be specified.';
        $errors['informant_street_name'] = $error;
        $errors['informant_box'] = $error;
        $errors['informant_address_other'] = $error;
      }

      if( !is_null( $this->informant_box ) && !util::string_matches_int( $this->informant_box ) )
        $errors['informant_box'] = 'Must be a number only (do not include PO, # or Box).';

      if( !is_null( $this->informant_rural_route ) &&
          !util::string_matches_int( $this->informant_rural_route ) )
        $errors['informant_rural_route'] = 'Must be a number only (do not include RR or #).';

      if( is_null( $this->informant_city ) )
        $errors['informant_city'] = 'Cannot be blank.';

      if( is_null( $this->informant_region_id ) )
        $errors['informant_region_id'] = 'Cannot be blank.';

      if( is_null( $this->informant_postcode ) )
        $errors['informant_postcode'] = 'Cannot be blank.';

      if( !is_null( $this->informant_region_id ) && !is_null( $this->informant_postcode ) )
      { // check that the postal code is valid
        $db_postcode = $postcode_class_name::get_match( $this->informant_postcode );
        if( is_null( $db_postcode ) || $db_postcode->region_id != $this->informant_region_id )
        {
          $errors['informant_postcode'] = 'The postal code does not exist in the selected province.';
        }
        else
        {
          $db_address = lib::create( 'database\address' );
          $db_address->region_id = $this->informant_region_id;
          $db_address->postcode = $this->informant_postcode;
          if( !$db_address->is_valid() )
            $errors['informant_postcode'] = 'The postal code is invalid.';
        }
      }

      if( is_null( $this->informant_phone ) )
        $errors['informant_phone'] = 'Cannot be blank.';
      else if( !util::validate_north_american_phone_number( $this->informant_phone ) )
        $errors['informant_phone'] = 'Invalid phone number, please use XXX-XXX-XXXX format.';
    }

    // make sure a proxy or informant are provided if continue-questionnaires is true
    if( $this->continue_questionnaires && !$proxy && !$informant )
    {
      $errors['continue_questionnaires'] =
        'Cannot be set to "Yes" when both Decision Maker and Information Provider are blank.';
    }

    // make sure the same-as-proxy checkbox is compatible with the DM and IP data
    if( !is_null( $this->same_as_proxy ) )
    {
      if( $proxy && $this->same_as_proxy && $informant )
      {
        $errors['same_as_proxy'] = 'Cannot be set to "Yes" when Information Provider is not blank.';
      }
      else if( $proxy && !$this->same_as_proxy && !$informant )
      {
        $errors['same_as_proxy'] = 'Cannot be set to "No" when Information Provider is blank.';
      }
      else if( !$proxy && $this->same_as_proxy && $informant )
      {
        $errors['same_as_proxy'] = 'Cannot be set to "Yes" when Decision Maker is blank.';
      }
    }

    return $errors;
  }
}
