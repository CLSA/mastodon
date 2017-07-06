<?php
/**
 * proxy_form_entry.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\database;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * proxy_form_entry: record
 */
class proxy_form_entry extends base_form_entry
{
  /**
   * Override parent method to make sure 6-character postal codes get a space
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
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
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @return associative array
   * @access public
   */
  public function get_errors()
  {
    $postcode_class_name = lib::get_class_name( 'database\postcode' );

    $errors = parent::get_errors();

    if( $this->proxy )
    {
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

    if( $this->informant && !$this->same_as_proxy )
    {
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

    return $errors;
  }
}
