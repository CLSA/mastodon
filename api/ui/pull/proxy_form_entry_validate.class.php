<?php
/**
 * proxy_form_entry_validate.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\pull;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * pull: proxy_form_entry validate
 */
class proxy_form_entry_validate extends \cenozo\ui\pull\base_record
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
    parent::__construct( 'proxy_form_entry', 'validate', $args );
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

    $participant_class_name = lib::get_class_name( 'database\participant' );
    $postcode_class_name = lib::get_class_name( 'database\postcode' );
    $errors = array();
    $record = $this->get_record();

    // validate each entry value in the form
    if( is_null( $record->uid ) ) $errors['uid'] = 'This value cannot be left blank.';
    else
    {
      $db_participant = $participant_class_name::get_unique_record( 'uid', $record->uid );
      if( is_null( $db_participant ) ) $errors['uid'] = 'No such participant exists.';
    }

    if( $record->proxy )
    {
      if( is_null( $record->proxy_first_name ) )
        $errors['proxy_first_name'] = 'This value cannot be left blank.';

      if( is_null( $record->proxy_last_name ) )
        $errors['proxy_last_name'] = 'This value cannot be left blank.';

      if( is_null( $record->proxy_street_number ) xor is_null( $record->proxy_street_name ) )
      {
        $name = is_null( $record->proxy_street_number )
              ? 'proxy_street_number' : 'proxy_street_name';
        $errors[$name] = 'Street address must include both the number and name.';
      }

      if( is_null( $record->proxy_street_name ) &&
          is_null( $record->proxy_box ) &&
          is_null( $record->proxy_address_other ) )
      {
        $error = 'At least one of "Street Name", "PO Box" or "Other Address" must be specified.';
        $errors['proxy_street_name'] = $error;
        $errors['proxy_box'] = $error;
        $errors['proxy_address_other'] = $error;
      }

      if( !is_null( $record->proxy_box ) &&
          $record->proxy_box != (string)( (integer) $record->proxy_box ) )
        $errors['proxy_box'] = 'Must be a number only (do not include PO, # or Box).';

      if( !is_null( $record->proxy_rural_route ) &&
          $record->proxy_rural_route != (string)( (integer) $record->proxy_rural_route ) )
        $errors['proxy_rural_route'] = 'Must be a number only (do not include RR or #).';

      if( is_null( $record->proxy_city ) )
        $errors['proxy_city'] = 'This value cannot be left blank.';

      if( is_null( $record->proxy_region_id ) )
        $errors['proxy_region_id'] = 'This value cannot be left blank.';

      if( is_null( $record->proxy_postcode ) )
        $errors['proxy_postcode'] = 'This value cannot be left blank.';

      if( !is_null( $record->proxy_region_id ) && !is_null( $record->proxy_postcode ) )
      { // check that the postal code is valid
        $db_postcode = $postcode_class_name::get_match( $record->proxy_postcode );
        if( is_null( $db_postcode ) || $db_postcode->region_id != $record->proxy_region_id )
          $errors['proxy_postcode'] = 'The postal code does not exist in the selected province.';
      }

      if( is_null( $record->proxy_phone ) )
        $errors['proxy_phone'] = 'This value cannot be left blank.';
      else if( !util::validate_phone_number( $record->proxy_phone ) )
        $errors['proxy_phone'] = 'Invalid phone number, please use XXX-XXX-XXXX format.';
    }

    if( $record->informant && !$record->same_as_proxy )
    {
      if( is_null( $record->informant_first_name ) )
        $errors['informant_first_name'] = 'This value cannot be left blank.';

      if( is_null( $record->informant_last_name ) )
        $errors['informant_last_name'] = 'This value cannot be left blank.';

      if( is_null( $record->informant_street_number ) xor is_null( $record->informant_street_name ) )
      {
        $name = is_null( $record->informant_street_number )
              ? 'informant_street_number' : 'informant_street_name';
        $errors[$name] = 'Street address must include both the number and name.';
      }

      if( is_null( $record->informant_street_name ) &&
          is_null( $record->informant_box ) &&
          is_null( $record->informant_address_other ) )
      {
        $error = 'At least one of "Street Name", "PO Box" or "Other Address" must be specified.';
        $errors['informant_street_name'] = $error;
        $errors['informant_box'] = $error;
        $errors['informant_address_other'] = $error;
      }

      if( !is_null( $record->informant_box ) &&
          $record->informant_box != (string)( (integer) $record->informant_box ) )
        $errors['informant_box'] = 'Must be a number only (do not include PO, # or Box).';

      if( !is_null( $record->informant_rural_route ) &&
          $record->informant_rural_route != (string)( (integer) $record->informant_rural_route ) )
        $errors['informant_rural_route'] = 'Must be a number only (do not include RR or #).';

      if( is_null( $record->informant_city ) )
        $errors['informant_city'] = 'This value cannot be left blank.';

      if( is_null( $record->informant_region_id ) )
        $errors['informant_region_id'] = 'This value cannot be left blank.';

      if( is_null( $record->informant_postcode ) )
        $errors['informant_postcode'] = 'This value cannot be left blank.';

      if( !is_null( $record->informant_region_id ) && !is_null( $record->informant_postcode ) )
      { // check that the postal code is valid
        $db_postcode = $postcode_class_name::get_match( $record->informant_postcode );
        if( is_null( $db_postcode ) || $db_postcode->region_id != $record->informant_region_id )
          $errors['informant_postcode'] = 'The postal code does not exist in the selected province.';
      }

      if( is_null( $record->informant_phone ) )
        $errors['informant_phone'] = 'This value cannot be left blank.';
      else if( !util::validate_phone_number( $record->informant_phone ) )
        $errors['informant_phone'] = 'Invalid phone number, please use XXX-XXX-XXXX format.';
    }

    if( is_null( $record->date ) )
      $errors['date'] = 'This value cannot be left blank.';

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
