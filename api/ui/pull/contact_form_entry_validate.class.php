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
      $postcode_class_name = lib::get_class_name( 'database\postcode' );
      $db_postcode = $postcode_class_name::get_match( $record->postcode );
      if( is_null( $db_postcode ) || $db_postcode->region_id != $record->region_id )
        $errors['postcode'] = 'The postal code does not exist in the selected province.';
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
      $participant_class_name = lib::get_class_name( 'database\participant' );
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

    if( is_null( $record->cohort ) )
      $errors['cohort'] = 'This value cannot be left blank.';

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
?>
