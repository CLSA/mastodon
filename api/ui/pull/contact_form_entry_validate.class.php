<?php
/**
 * contact_form_entry_validate.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\pull;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * pull: contact_form_entry validate
 */
class contact_form_entry_validate extends \cenozo\ui\pull\base_record
{
  /**
   * Constructor
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Pull arguments.
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'contact_form_entry', 'validate', $args );
  }

  /**
   * This method executes the operation's purpose.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function execute()
  {
    parent::execute();

    $postcode_class_name = lib::get_class_name( 'database\postcode' );
    $address_class_name = lib::get_class_name( 'database\address' );
    $participant_class_name = lib::get_class_name( 'database\participant' );

    $record = $this->get_record();
    $errors = array();

    // validate each entry value in the form
    if( is_null( $record->first_name ) )
      $errors['first_name'] = 'This value cannot be left blank.';

    if( is_null( $record->last_name ) )
      $errors['last_name'] = 'This value cannot be left blank.';

    if( is_null( $record->street_number ) xor
        is_null( $record->street_name ) )
    {
      $name = is_null( $record->street_number ) ? 'street_number' : 'street_name';
      $errors[$name] = 'Street address must include both the number and name.';
    }

    if( is_null( $record->street_name ) &&
        is_null( $record->box ) &&
        is_null( $record->address_other ) )
    {
      $error = 'At least one of "Street Name", "PO Box" or "Other Address" must be specified.';
      $errors['street_name'] = $error;
      $errors['box'] = $error;
      $errors['address_other'] = $error;
    }

    if( !is_null( $record->box ) &&
        $record->box != (string)( (integer) $record->box ) )
      $errors['box'] = 'Must be a number only (do not include PO, # or Box).';

    if( !is_null( $record->rural_route ) &&
        $record->rural_route != (string)( (integer) $record->rural_route ) )
      $errors['rural_route'] = 'Must be a number only (do not include RR or #).';

    if( is_null( $record->city ) )
      $errors['city'] = 'This value cannot be left blank.';

    if( is_null( $record->region_id ) )
      $errors['region_id'] = 'This value cannot be left blank.';
    else if( 'Canada' != $record->get_region()->country )
      $errors['region_id'] = 'The address must be in Canada.';

    if( is_null( $record->postcode ) )
      $errors['postcode'] = 'This value cannot be left blank.';

    if( !is_null( $record->region_id ) && !is_null( $record->postcode ) )
    { // check that the postal code is valid
      $db_postcode = $postcode_class_name::get_match( $record->postcode );
      if( is_null( $db_postcode ) || $db_postcode->region_id != $record->region_id )
        $errors['postcode'] = 'The postal code does not exist in the selected province.';
    }

    // check for address duplicates in the same cohort
    $address = util::parse_address(
      $record->apartment_number,
      $record->street_number,
      $record->street_name,
      $record->box,
      $record->rural_route,
      $record->address_other );
    $postcode = 6 == strlen( $record->postcode )
              ? sprintf( '%s %s',
                         substr( $record->postcode, 0, 3 ),
                         substr( $record->postcode, 3, 3 ) )
              : $record->postcode;

    $address_mod = lib::create( 'database\modifier' );
    $address_mod->where( 'address1', '=', $address[0] );
    $address_mod->where( 'address2', '=', $address[1] );
    $address_mod->where( 'city', '=', $record->city );
    $address_mod->where( 'region_id', '=', $record->region_id );
    $address_mod->where( 'postcode', '=', $postcode );
    foreach( $address_class_name::select( $address_mod ) as $db_address )
    {
      $db_participant = $db_address->get_person()->get_participant();
      if( $db_participant && $db_participant->cohort_id == $record->cohort_id )
      {
        $message = sprintf( 'A %s participant already exists at this address.',
                            $record->get_cohort()->name );
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

    if( is_null( $record->home_phone ) && is_null( $record->mobile_phone ) )
    {
      $error = 'At least one phone number must be provided.';
      $errors['home_phone'] = $error;
      $errors['mobile_phone'] = $error;
    }

    $home_phone = NULL;
    if( !is_null( $record->home_phone ) )
    {
      if( util::validate_phone_number( $record->home_phone ) )
        $home_phone = $record->home_phone;
      else $errors['home_phone'] = 'Invalid phone number, please use XXX-XXX-XXXX format.';
    }

    $mobile_phone = NULL;
    if( !is_null( $record->mobile_phone ) )
    {
      if( util::validate_phone_number( $record->mobile_phone ) )
        $mobile_phone = $record->mobile_phone;
      else $errors['mobile_phone'] = 'Invalid phone number, please use XXX-XXX-XXXX format.';
    }

    if( !is_null( $record->first_name ) &&
        !is_null( $record->last_name ) &&
        ( !is_null( $home_phone ) || !is_null( $mobile_phone ) ) )
    { // look for duplicates
      $participant_mod = lib::create( 'database\modifier' );
      $participant_mod->where( 'first_name', '=', $record->first_name );
      $participant_mod->where( 'last_name', '=', $record->last_name );
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

    if( is_null( $record->gender ) )
      $errors['gender'] = 'This value cannot be left blank.';

    if( is_null( $record->age_bracket ) )
      $errors['age_bracket'] = 'This value cannot be left blank.';

    if( is_null( $record->cohort_id ) )
      $errors['cohort_id'] = 'This value cannot be left blank.';

    if( is_null( $record->code ) )
      $errors['code'] = 'This value cannot be left blank.';

    // make sure the cohort/code match
    if( !is_null( $record->cohort_id ) && !is_null( $record->code ) )
    {
      $cohort = $record->get_cohort()->name;

      // all comprehensive codes start with C, all tracking codes start with T
      if( 0 != strcasecmp( substr( $cohort, 0, 1 ), substr( $record->code, 0, 1 ) ) )
      {
        $error = sprintf(
          'Either the cohort or code is incorrect (all %s codes must begin with a "%s")',
          $cohort,
          strtoupper( substr( $cohort, 0, 1 ) ) );
        $errors['cohort_id'] = $error;
        $errors['code'] = $error;
      }
    }

    $this->data = $errors;
  }

  /**
   * Implements the parent's abstract method (data type is always json)
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @return string
   * @access public
   */
  public function get_data_type()
  {
    return 'json';
  }
}
