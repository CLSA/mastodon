<?php
/**
 * contact_form_entry_view.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget contact_form_entry view
 */
class contact_form_entry_view extends base_form_entry_view
{
  /**
   * Constructor
   * 
   * Defines all variables which need to be set for the associated template.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args An associative array of arguments to be processed by the widget
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'contact', $args );
  }

  /**
   * Processes arguments, preparing them for the operation.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @throws exception\notice
   * @access protected
   */
  protected function prepare()
  {
    parent::prepare();

    // add the entry values
    $this->add_item( 'first_name', 'string', 'First Name' );
    $this->add_item( 'last_name', 'string', 'Last Name' );
    $this->add_item( 'apartment_number', 'string', 'Apartment #' );
    $this->add_item( 'street_number', 'string', 'Street #' );
    $this->add_item( 'street_name', 'string', 'Street Name' );
    $this->add_item( 'box', 'string', 'Post Office Box #' );
    $this->add_item( 'rural_route', 'string', 'Rural Route #' );
    $this->add_item( 'address_other', 'string', 'Other Address' );
    $this->add_item( 'city', 'string', 'City' );
    $this->add_item( 'region_id', 'enum', 'Province' );
    $this->add_item( 'postcode', 'string', 'Postal Code' );
    $this->add_item( 'address_note', 'text', 'Address Note' );
    $this->add_item( 'home_phone', 'string', 'Home Phone' );
    $this->add_item( 'home_phone_note', 'text', 'Home Phone Note' );
    $this->add_item( 'mobile_phone', 'string', 'Mobile Phone' );
    $this->add_item( 'mobile_phone_note', 'text', 'Home Mobile Note' );
    $this->add_item( 'phone_preference', 'enum', 'Phone Preference' );
    $this->add_item( 'email', 'string', 'Email Address' );
    $this->add_item( 'gender', 'enum', 'Sex' );
    $this->add_item( 'age_bracket', 'enum', 'Age Bracket' );
    $this->add_item( 'monday', 'boolean', 'Monday' );
    $this->add_item( 'tuesday', 'boolean', 'Tuesday' );
    $this->add_item( 'wednesday', 'boolean', 'Wednesday' );
    $this->add_item( 'thursday', 'boolean', 'Thursday' );
    $this->add_item( 'friday', 'boolean', 'Friday' );
    $this->add_item( 'saturday', 'boolean', 'Saturday' );
    $this->add_item( 'time_9_10', 'boolean', '9am to 10am' );
    $this->add_item( 'time_10_11', 'boolean', '10am to 11am' );
    $this->add_item( 'time_11_12', 'boolean', '11am to 12pm' );
    $this->add_item( 'time_12_13', 'boolean', '12pm to 1pm' );
    $this->add_item( 'time_13_14', 'boolean', '1pm to 2pm' );
    $this->add_item( 'time_14_15', 'boolean', '2pm to 3pm' );
    $this->add_item( 'time_15_16', 'boolean', '3pm to 4pm' );
    $this->add_item( 'time_16_17', 'boolean', '4pm to 5pm' );
    $this->add_item( 'time_17_18', 'boolean', '5pm to 6pm' );
    $this->add_item( 'time_18_19', 'boolean', '6pm to 7pm' );
    $this->add_item( 'time_19_20', 'boolean', '7pm to 8pm' );
    $this->add_item( 'time_20_21', 'boolean', '8pm to 9pm' );
    $this->add_item( 'language', 'enum', 'Language' );
    $this->add_item( 'signed', 'boolean', 'Signed' );
    $this->add_item( 'date', 'date', 'Date Signed' );
    $this->add_item( 'cohort', 'enum', 'Cohort' );
    $this->add_item( 'note', 'text', 'Note' );
  }

  /**
   * Sets up the operation with any pre-execution instructions that may be necessary.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function setup()
  {
    parent::setup();

    $region_class_name = lib::get_class_name( 'database\region' );
    $contact_form_entry_class_name = lib::get_class_name( 'database\contact_form_entry' );

    // create enum arrays
    $region_mod = lib::create( 'database\modifier' );
    $region_mod->where( 'country', '=', 'Canada' );
    $region_list = array();
    foreach( $region_class_name::select( $region_mod ) as $db_region )
      $region_list[$db_region->id] = $db_region->name.', '.$db_region->country;
    $phone_preference_list = $contact_form_entry_class_name::get_enum_values( 'phone_preference' );
    $phone_preference_list = array_combine( $phone_preference_list, $phone_preference_list );
    $gender_list = $contact_form_entry_class_name::get_enum_values( 'gender' );
    $gender_list = array_combine( $gender_list, $gender_list );
    $age_bracket_list = $contact_form_entry_class_name::get_enum_values( 'age_bracket' );
    $age_bracket_list = array_combine( $age_bracket_list, $age_bracket_list );
    $language_list = $contact_form_entry_class_name::get_enum_values( 'language' );
    $language_list = array_combine( $language_list, $language_list );
    $cohort_list = $contact_form_entry_class_name::get_enum_values( 'cohort' );
    $cohort_list = array_combine( $cohort_list, $cohort_list );

    // set the entry values
    $record = $this->get_record();
    $this->set_item( 'first_name', $record->first_name, false );
    $this->set_item( 'last_name', $record->last_name, false );
    $this->set_item( 'apartment_number', $record->apartment_number, false );
    $this->set_item( 'street_number', $record->street_number, false );
    $this->set_item( 'street_name', $record->street_name, false );
    $this->set_item( 'box', $record->box, false );
    $this->set_item( 'rural_route', $record->rural_route, false );
    $this->set_item( 'address_other', $record->address_other, false );
    $this->set_item( 'city', $record->city, false );
    $this->set_item( 'region_id', $record->region_id, false, $region_list );
    $this->set_item( 'postcode', $record->postcode, false );
    $this->set_item( 'address_note', $record->address_note, false );
    $this->set_item( 'home_phone', $record->home_phone, false );
    $this->set_item( 'home_phone_note', $record->home_phone_note, false );
    $this->set_item( 'mobile_phone', $record->mobile_phone, false );
    $this->set_item( 'mobile_phone_note', $record->mobile_phone_note, false );
    $this->set_item( 'phone_preference', $record->phone_preference, true, $phone_preference_list );
    $this->set_item( 'email', $record->email, false );
    $this->set_item( 'gender', $record->gender, false, $gender_list );
    $this->set_item( 'age_bracket', $record->age_bracket, false, $age_bracket_list );
    $this->set_item( 'monday', $record->monday, true );
    $this->set_item( 'tuesday', $record->tuesday, true );
    $this->set_item( 'wednesday', $record->wednesday, true );
    $this->set_item( 'thursday', $record->thursday, true );
    $this->set_item( 'friday', $record->friday, true );
    $this->set_item( 'saturday', $record->saturday, true );
    $this->set_item( 'time_9_10', $record->time_9_10, true );
    $this->set_item( 'time_10_11', $record->time_10_11, true );
    $this->set_item( 'time_11_12', $record->time_11_12, true );
    $this->set_item( 'time_12_13', $record->time_12_13, true );
    $this->set_item( 'time_13_14', $record->time_13_14, true );
    $this->set_item( 'time_14_15', $record->time_14_15, true );
    $this->set_item( 'time_15_16', $record->time_15_16, true );
    $this->set_item( 'time_16_17', $record->time_16_17, true );
    $this->set_item( 'time_17_18', $record->time_17_18, true );
    $this->set_item( 'time_18_19', $record->time_18_19, true );
    $this->set_item( 'time_19_20', $record->time_19_20, true );
    $this->set_item( 'time_20_21', $record->time_20_21, true );
    $this->set_item( 'language', $record->language, true, $language_list );
    $this->set_item( 'signed', $this->get_record()->signed, true );
    $this->set_item( 'date', $record->date, false );
    $this->set_item( 'cohort', $record->cohort, false, $cohort_list );
    $this->set_item( 'note', $record->note, false );
  }
}
?>
