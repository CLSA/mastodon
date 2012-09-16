<?php
/**
 * participant_view.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget participant view
 */
class participant_view extends \cenozo\ui\widget\base_view
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
    
    // create an associative array with everything we want to display about the participant
    $this->add_item( 'active', 'boolean', 'Active' );
    $this->add_item( 'uid', 'constant', 'Unique ID' );
    $this->add_item( 'first_name', 'string', 'First Name' );
    $this->add_item( 'last_name', 'string', 'Last Name' );
    $this->add_item( 'source', 'constant', 'Source' );
    $this->add_item( 'cohort', 'constant', 'Cohort' );
    $this->add_item( 'default_site', 'constant', 'Default Site' );
    $this->add_item( 'site_id', 'enum', 'Prefered Site' );
    $this->add_item( 'gender', 'enum', 'Gender' );
    $this->add_item( 'date_of_birth', 'date', 'Date of Birth' );
    $this->add_item( 'age_group', 'constant', 'Age Group' );
    $this->add_item( 'language', 'enum', 'Preferred Language' );
    $this->add_item( 'email', 'string', 'Email' );
    $this->add_item( 'status', 'enum', 'Condition' );
    $this->add_item( 'no_in_home', 'boolean', 'No in Home' );
    $this->add_item( 'prior_contact_date', 'date', 'Prior Contact Date' );

    // create the address sub-list widget
    $this->address_list = lib::create( 'ui\widget\address_list', $this->arguments );
    $this->address_list->set_parent( $this );
    $this->address_list->set_heading( 'Addresses' );

    // create the phone sub-list widget
    $this->phone_list = lib::create( 'ui\widget\phone_list', $this->arguments );
    $this->phone_list->set_parent( $this );
    $this->phone_list->set_heading( 'Phone numbers' );

    // create the availability sub-list widget
    $this->availability_list = lib::create( 'ui\widget\availability_list', $this->arguments );
    $this->availability_list->set_parent( $this );
    $this->availability_list->set_heading( 'Availability' );

    // create the consent sub-list widget
    $this->consent_list = lib::create( 'ui\widget\consent_list', $this->arguments );
    $this->consent_list->set_parent( $this );
    $this->consent_list->set_heading( 'Consent information' );

    // create the alternate sub-list widget
    $this->alternate_list = lib::create( 'ui\widget\alternate_list', $this->arguments );
    $this->alternate_list->set_parent( $this );
    $this->alternate_list->set_heading( 'Alternate contacts' );
  }

  /**
   * Finish setting the variables in a widget.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function setup()
  {
    parent::setup();

    // create enum arrays
    $participant_class_name = lib::get_class_name( 'database\participant' );
    $record = $this->get_record();

    $sites = array();
    $site_class_name = lib::get_class_name( 'database\site' );
    $site_mod = lib::create( 'database\modifier' );
    $site_mod->where( 'cohort', '=', $record->cohort );
    foreach( $site_class_name::select( $site_mod ) as $db_site )
      $sites[$db_site->id] = $db_site->name;
    $db_site = $record->get_site();
    $site_id = is_null( $db_site ) ? '' : $db_site->id;
    $genders = $participant_class_name::get_enum_values( 'gender' );
    $genders = array_combine( $genders, $genders );
    $languages = $participant_class_name::get_enum_values( 'language' );
    $languages = array_combine( $languages, $languages );
    $statuses = $participant_class_name::get_enum_values( 'status' );
    $statuses = array_combine( $statuses, $statuses );

    $db_default_site = $this->get_record()->get_default_site();
    $default_site = is_null( $db_default_site ) ? 'None' : $db_default_site->name;

    $age_group = '';
    if( !is_null( $record->age_group_id ) )
    {
      $db_age_group = lib::create( 'database\age_group', $record->age_group_id );
      $age_group = sprintf( '%d to %d', $db_age_group->lower, $db_age_group->upper );
    }

    // set the view's items
    $this->set_item( 'active', $record->active, true );
    $this->set_item( 'uid', $record->uid, true );
    $this->set_item( 'first_name', $record->first_name );
    $this->set_item( 'last_name', $record->last_name );
    $this->set_item( 'source', $record->get_source()->name );
    $this->set_item( 'cohort', $record->cohort );
    $this->set_item( 'default_site', $default_site );
    $this->set_item( 'site_id', $site_id, false, $sites );
    $this->set_item( 'gender', $record->gender, true, $genders );
    $this->set_item( 'date_of_birth', $record->date_of_birth );
    $this->set_item( 'age_group', $age_group );
    $this->set_item( 'language', $record->language, false, $languages );
    $this->set_item( 'email', $record->email, false );
    $this->set_item( 'status', $record->status, false, $statuses );
    $this->set_item( 'no_in_home', $record->no_in_home, true );
    $this->set_item( 'prior_contact_date', $record->prior_contact_date, false );

    // add a contact form download action
    $db_contact_form = $record->get_contact_form();
    if( !is_null( $db_contact_form ) )
      $this->set_variable( 'contact_form_id', $db_contact_form->id );
    $this->add_action( 'contact_form', 'Contact Form', NULL,
      'Download this participant\'s contact form, if available' );

    try
    {
      $this->address_list->process();
      $this->set_variable( 'address_list', $this->address_list->get_variables() );
    }
    catch( \cenozo\exception\permission $e ) {}

    try
    {
      $this->phone_list->process();
      $this->set_variable( 'phone_list', $this->phone_list->get_variables() );
    }
    catch( \cenozo\exception\permission $e ) {}

    try
    {
      $this->availability_list->process();
      $this->set_variable( 'availability_list', $this->availability_list->get_variables() );
    }
    catch( \cenozo\exception\permission $e ) {}

    try
    {
      $this->consent_list->process();
      $this->set_variable( 'consent_list', $this->consent_list->get_variables() );
    }
    catch( \cenozo\exception\permission $e ) {}

    try
    {
      $this->alternate_list->process();
      $this->set_variable( 'alternate_list', $this->alternate_list->get_variables() );
    }
    catch( \cenozo\exception\permission $e ) {}
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
   * The availability list widget.
   * @var availability_list
   * @access protected
   */
  protected $availability_list = NULL;
  
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
