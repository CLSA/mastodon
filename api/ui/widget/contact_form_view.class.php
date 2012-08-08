<?php
/**
 * contact_form_view.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget contact_form view
 */
class contact_form_view extends base_form_view
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
    parent::__construct( 'contact_form', $args );
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

    // add entry values
    $this->add_item( 'first_name', 'First Name' );
    $this->add_item( 'last_name', 'Last Name' );
    $this->add_item( 'apartment_number', 'Apartment #' );
    $this->add_item( 'street_number', 'Street #' );
    $this->add_item( 'street_name', 'Street Name' );
    $this->add_item( 'box', 'Post Office Box #' );
    $this->add_item( 'rural_route', 'Rural Route #' );
    $this->add_item( 'address_other', 'Other Address' );
    $this->add_item( 'city', 'City' );
    $this->add_item( 'region_id', 'Province' );
    $this->add_item( 'postcode', 'Postal Code' );
    $this->add_item( 'address_note', 'Address Note' );
    $this->add_item( 'home_phone', 'Home Phone' );
    $this->add_item( 'home_phone_note', 'Home Phone Note' );
    $this->add_item( 'mobile_phone', 'Mobile Phone' );
    $this->add_item( 'mobile_phone_note', 'Mobile Phone Note' );
    $this->add_item( 'phone_preference', 'Phone Preference' );
    $this->add_item( 'email', 'Email Address' );
    $this->add_item( 'gender', 'Sex' );
    $this->add_item( 'age_bracket', 'Age Bracket' );
    $this->add_item( 'monday', 'Monday' );
    $this->add_item( 'tuesday', 'Tuesday' );
    $this->add_item( 'wednesday', 'Wednesday' );
    $this->add_item( 'thursday', 'Thursday' );
    $this->add_item( 'friday', 'Friday' );
    $this->add_item( 'saturday', 'Saturday' );
    $this->add_item( 'time_9_10', '9am to 10am' );
    $this->add_item( 'time_10_11', '10am to 11am' );
    $this->add_item( 'time_11_12', '11am to 12pm' );
    $this->add_item( 'time_12_13', '12pm to 1pm' );
    $this->add_item( 'time_13_14', '1pm to 2pm' );
    $this->add_item( 'time_14_15', '2pm to 3pm' );
    $this->add_item( 'time_15_16', '3pm to 4pm' );
    $this->add_item( 'time_16_17', '4pm to 5pm' );
    $this->add_item( 'time_17_18', '5pm to 6pm' );
    $this->add_item( 'time_18_19', '6pm to 7pm' );
    $this->add_item( 'time_19_20', '7pm to 8pm' );
    $this->add_item( 'time_20_21', '8pm to 9pm' );
    $this->add_item( 'language', 'Language' );
    $this->add_item( 'signed', 'Signed' );
    $this->add_item( 'date', 'Date Signed' );
    $this->add_item( 'cohort', 'Cohort' );
    $this->add_item( 'note', 'Note' );
  }
}
?>
