<?php
/**
 * contact_form_entry.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace mastodon\database;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * contact_form_entry: record
 */
class contact_form_entry extends base_form_entry
{
  /**
   * Returns the errors found by processing this entry
   * 
   * @return associative array
   * @access public
   */
  public function get_errors()
  {
    $postcode_class_name = lib::get_class_name( 'database\postcode' );
    $address_class_name = lib::get_class_name( 'database\address' );
    $participant_class_name = lib::get_class_name( 'database\participant' );

    $errors = parent::get_errors();

    // validate each entry value in the form
    if( is_null( $this->first_name ) )
      $errors['first_name'] = 'Cannot be blank.';

    if( is_null( $this->last_name ) )
      $errors['last_name'] = 'Cannot be blank.';

    if( is_null( $this->street_number ) xor
        is_null( $this->street_name ) )
    {
      $name = is_null( $this->street_number ) ? 'street_number' : 'street_name';
      $errors[$name] = 'Street address must include both the number and name.';
    }

    if( is_null( $this->street_name ) &&
        is_null( $this->box ) &&
        is_null( $this->address_other ) )
    {
      $error = 'At least one of "Street Name", "PO Box" or "Other Address" must be specified.';
      $errors['street_name'] = $error;
      $errors['box'] = $error;
      $errors['address_other'] = $error;
    }

    if( !is_null( $this->box ) && !util::string_matches_int( $this->box ) )
      $errors['box'] = 'Must be a number only (do not include PO, # or Box).';

    if( !is_null( $this->rural_route ) && !util::string_matches_int( $this->rural_route ) )
      $errors['rural_route'] = 'Must be a number only (do not include RR or #).';

    if( is_null( $this->city ) )
      $errors['city'] = 'Cannot be blank.';

    if( is_null( $this->region_id ) )
      $errors['region_id'] = 'Cannot be blank.';

    if( is_null( $this->postcode ) )
      $errors['postcode'] = 'Cannot be blank.';

    if( !is_null( $this->region_id ) && !is_null( $this->postcode ) )
    { // check that the postal code is valid
      $db_postcode = $postcode_class_name::get_match( $this->postcode );
      if( is_null( $db_postcode ) || $db_postcode->region_id != $this->region_id )
        $errors['postcode'] = 'The postcode does not exist in the selected province/state.';
    }

    // check for address duplicates in the same cohort
    $address = util::parse_address(
      $this->apartment_number,
      $this->street_number,
      $this->street_name,
      $this->box,
      $this->rural_route,
      $this->address_other );
    $postcode = 6 == strlen( $this->postcode )
              ? sprintf( '%s %s',
                         substr( $this->postcode, 0, 3 ),
                         substr( $this->postcode, 3, 3 ) )
              : $this->postcode;

    $address_mod = lib::create( 'database\modifier' );
    $address_mod->where( 'address1', '=', $address[0] );
    $address_mod->where( 'address2', '=', $address[1] );
    $address_mod->where( 'city', '=', $this->city );
    $address_mod->where( 'region_id', '=', $this->region_id );
    $address_mod->where( 'postcode', '=', $postcode );
    foreach( $address_class_name::select( $address_mod ) as $db_address )
    {
      $db_participant = $db_address->get_person()->get_participant();
      if( $db_participant && $db_participant->cohort_id == $this->cohort_id )
      {
        $message = sprintf( 'A %s participant already exists at this address.',
                            $this->get_cohort()->name );
        $errors['apartment_number'] = $message;
        $errors['street_number'] = $message;
        $errors['street_name'] = $message;
        $errors['box'] = $message;
        $errors['address_other'] = $message;
        $errors['rural_route'] = $message;
        $errors['city'] = $message;
        $errors['region_id'] = $message;
        $errors['postcode'] = $message;
      }
    }

    if( is_null( $this->home_phone ) && is_null( $this->mobile_phone ) )
    {
      $error = 'At least one phone number must be provided.';
      $errors['home_phone'] = $error;
      $errors['mobile_phone'] = $error;
    }
    $home_phone = NULL;
    if( !is_null( $this->home_phone ) )
    {
      if( util::validate_north_american_phone_number( $this->home_phone ) )
        $home_phone = $this->home_phone;
      else $errors['home_phone'] = 'Invalid phone number, please use XXX-XXX-XXXX format.';
    }

    $mobile_phone = NULL;
    if( !is_null( $this->mobile_phone ) )
    {
      if( util::validate_north_american_phone_number( $this->mobile_phone ) )
        $mobile_phone = $this->mobile_phone;
      else $errors['mobile_phone'] = 'Invalid phone number, please use XXX-XXX-XXXX format.';
    }

    if( !is_null( $this->first_name ) &&
        !is_null( $this->last_name ) &&
        ( !is_null( $home_phone ) || !is_null( $mobile_phone ) ) )
    { // look for duplicates
      $participant_mod = lib::create( 'database\modifier' );
      $participant_mod->where( 'first_name', '=', $this->first_name );
      $participant_mod->where( 'last_name', '=', $this->last_name );
      foreach( $participant_class_name::select( $participant_mod ) as $db_participant )
      {
        foreach( $db_participant->get_phone_list() as $db_phone )
        {
          if( !is_null( $home_phone ) && $home_phone == $db_phone->number ||
              !is_null( $mobile_phone ) && $mobile_phone == $db_phone->number )
          {
            $error = 'A participant with the same first name, last name and phone number already '.
                     'exists in the system.';
            $errors['first_name'] = $error;
            $errors['last_name'] = $error;
          }
        }
      }
    }

    if( is_null( $this->gender ) )
      $errors['gender'] = 'Cannot be blank.';

    if( is_null( $this->age_bracket ) )
      $errors['age_bracket'] = 'Cannot be blank.';

    if( is_null( $this->cohort_id ) )
      $errors['cohort_id'] = 'Cannot be blank.';

    if( is_null( $this->code ) )
      $errors['code'] = 'Cannot be blank.';

    // make sure the cohort/code match
    if( !is_null( $this->cohort_id ) && !is_null( $this->code ) )
    {
      $cohort = $this->get_cohort()->name;

      // all comprehensive codes start with C, all tracking codes start with T
      if( 0 != strcasecmp( substr( $cohort, 0, 1 ), substr( $this->code, 0, 1 ) ) )
      {
        $error = sprintf(
          'Either the cohort or code is incorrect (all %s codes must begin with a "%s")',
          $cohort,
          strtoupper( substr( $cohort, 0, 1 ) ) );
        $errors['cohort_id'] = $error;
        $errors['code'] = $error;
      }
    }

    return $errors;
  }
}
