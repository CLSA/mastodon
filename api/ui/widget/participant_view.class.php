<?php
/**
 * participant_view.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\widget;
use mastodon\log, mastodon\util;
use mastodon\business as bus;
use mastodon\database as db;
use mastodon\exception as exc;

/**
 * widget participant view
 * 
 * @package mastodon\ui
 */
class participant_view extends base_view
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
    parent::__construct( 'participant', 'view', $args );
    
    // create an associative array with everything we want to display about the participant
    $this->add_item( 'active', 'boolean', 'Active' );
    $this->add_item( 'uid', 'string', 'Unique ID' );
    $this->add_item( 'first_name', 'string', 'First Name' );
    $this->add_item( 'last_name', 'string', 'Last Name' );
    $this->add_item( 'source', 'enum', 'Source' );
    $this->add_item( 'cohort', 'constant', 'Cohort' );
    $this->add_item( 'gender', 'enum', 'Gender' );
    $this->add_item( 'date_of_birth', 'date', 'Date of Birth' );
    $this->add_item( 'language', 'enum', 'Preferred Language' );
    $this->add_item( 'email', 'string', 'Email' );
    $this->add_item( 'site_id', 'enum', 'Prefered Site' );
    $this->add_item( 'status', 'enum', 'Condition' );
    $this->add_item( 'eligible', 'boolean', 'Eligible' );
    $this->add_item( 'no_in_home', 'boolean', 'No in Home' );
    $this->add_item( 'prior_contact_date', 'date', 'Prior Contact Date' );
    
    try
    {
      // create the address sub-list widget
      $this->address_list = new address_list( $args );
      $this->address_list->set_parent( $this );
      $this->address_list->set_heading( 'Addresses' );
    }
    catch( exc\permission $e )
    {
      $this->address_list = NULL;
    }

    try
    {
      // create the phone sub-list widget
      $this->phone_list = new phone_list( $args );
      $this->phone_list->set_parent( $this );
      $this->phone_list->set_heading( 'Phone numbers' );
    }
    catch( exc\permission $e )
    {
      $this->phone_list = NULL;
    }

    try
    {
      // create the appointment sub-list widget
      $this->appointment_list = new appointment_list( $args );
      $this->appointment_list->set_parent( $this );
      $this->appointment_list->set_heading( 'Appointments' );
    }
    catch( exc\permission $e )
    {
      $this->appointment_list = NULL;
    }

    try
    {
      // create the consent sub-list widget
      $this->consent_list = new consent_list( $args );
      $this->consent_list->set_parent( $this );
      $this->consent_list->set_heading( 'Consent information' );
    }
    catch( exc\permission $e )
    {
      $this->consent_list = NULL;
    }

    try
    {
      // create the alternate sub-list widget
      $this->alternate_list = new alternate_list( $args );
      $this->alternate_list->set_parent( $this );
      $this->alternate_list->set_heading( 'Alternate contacts' );
    }
    catch( exc\permission $e )
    {
      $this->alternate_list = NULL;
    }
  }

  /**
   * Finish setting the variables in a widget.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function finish()
  {
    parent::finish();

    // create enum arrays
    $sources = db\participant::get_enum_values( 'source' );
    $sources = array_combine( $sources, $sources );
    $genders = db\participant::get_enum_values( 'gender' );
    $genders = array_combine( $genders, $genders );
    $languages = db\participant::get_enum_values( 'language' );
    $languages = array_combine( $languages, $languages );
    $statuses = db\participant::get_enum_values( 'status' );
    $statuses = array_combine( $statuses, $statuses );

    $sites = array();
    $modifier = new db\modifier();
    $modifier->where( 'cohort', '=', $this->get_record()->cohort );
    foreach( db\site::select( $modifier ) as $db_site ) $sites[$db_site->id] = $db_site->name;
    $db_site = $this->get_record()->get_site();
    $site_id = is_null( $db_site ) ? '' : $db_site->id;
    
    // set the view's items
    $this->set_item( 'active', $this->get_record()->active, true );
    $this->set_item( 'uid', $this->get_record()->uid, true );
    $this->set_item( 'first_name', $this->get_record()->first_name );
    $this->set_item( 'last_name', $this->get_record()->last_name );
    $this->set_item( 'source', $this->get_record()->source, true, $sources );
    $this->set_item( 'cohort', $this->get_record()->cohort );
    $this->set_item( 'gender', $this->get_record()->gender, true, $genders );
    $this->set_item( 'date_of_birth', $this->get_record()->date_of_birth );
    $this->set_item( 'language', $this->get_record()->language, false, $languages );
    $this->set_item( 'email', $this->get_record()->email, false );
    $this->set_item( 'site_id', $site_id, false, $sites );
    $this->set_item( 'status', $this->get_record()->status, false, $statuses );
    $this->set_item( 'eligible', $this->get_record()->eligible, true );
    $this->set_item( 'no_in_home', $this->get_record()->no_in_home, true );
    $this->set_item( 'prior_contact_date', $this->get_record()->prior_contact_date, false );

    $this->finish_setting_items();

    if( !is_null( $this->address_list ) )
    {
      $this->address_list->finish();
      $this->set_variable( 'address_list', $this->address_list->get_variables() );
    }

    if( !is_null( $this->phone_list ) )
    {
      $this->phone_list->finish();
      $this->set_variable( 'phone_list', $this->phone_list->get_variables() );
    }

    if( !is_null( $this->appointment_list ) )
    {
      $this->appointment_list->finish();
      $this->set_variable( 'appointment_list', $this->appointment_list->get_variables() );
    }

    if( !is_null( $this->consent_list ) )
    {
      $this->consent_list->finish();
      $this->set_variable( 'consent_list', $this->consent_list->get_variables() );
    }

    if( !is_null( $this->alternate_list ) )
    {
      $this->alternate_list->finish();
      $this->set_variable( 'alternate_list', $this->alternate_list->get_variables() );
    }
  }
  
  /**
   * The address list widget.
   * @var address_list
   * @access protected
   */
  protected $address_list = NULL;
  
  /**
   * The phone list widget.
   * @var phone_list
   * @access protected
   */
  protected $phone_list = NULL;
  
  /**
   * The appointment list widget.
   * @var appointment_list
   * @access protected
   */
  protected $appointment_list = NULL;
  
  /**
   * The consent list widget.
   * @var consent_list
   * @access protected
   */
  protected $consent_list = NULL;
  
  /**
   * The alternate contact person list widget.
   * @var alternate_list
   * @access protected
   */
  protected $alternate_list = NULL;
}
?>
