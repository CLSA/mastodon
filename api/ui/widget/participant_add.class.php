<?php
/**
 * participant_add.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget participant add
 */
class participant_add extends \cenozo\ui\widget\base_view
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
    parent::__construct( 'participant', 'add', $args );
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

    $class_name = lib::get_class_name( 'database\participant' );
    $this->new_uid = $class_name::get_new_uid();
    
    // define all columns defining this record
    $this->add_item( 'active', 'boolean', 'Active' );
    $this->add_item( 'uid', is_null( $this->new_uid ) ? 'string' : 'hidden', 'Unique ID' );
    $this->add_item( 'first_name', 'string', 'First Name' );
    $this->add_item( 'last_name', 'string', 'Last Name' );
    $this->add_item( 'source_id', 'enum', 'Source' );
    $this->add_item( 'cohort', 'enum', 'Cohort' );
    $this->add_item( 'gender', 'enum', 'Gender' );
    $this->add_item( 'date_of_birth', 'date', 'Date of Birth' );
    $this->add_item( 'language', 'enum', 'Preferred Language' );
    $this->add_item( 'email', 'string', 'Email' );
    $this->add_item( 'status', 'enum', 'Condition' );
    $this->add_item( 'no_in_home', 'boolean', 'No in Home' );
    $this->add_item( 'prior_contact_date', 'date', 'Prior Contact Date' );
    $this->add_item( 'person_id', 'hidden' );
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
    
    // create enum arrays
    $participant_class_name = lib::get_class_name( 'database\participant' );
    $source_class_name = lib::get_class_name( 'database\source' );

    $sources = array();
    foreach( $source_class_name::select() as $db_source )
      $sources[$db_source->id] = $db_source->name;
    $genders = $participant_class_name::get_enum_values( 'gender' );
    $genders = array_combine( $genders, $genders );
    $languages = $participant_class_name::get_enum_values( 'language' );
    $languages = array_combine( $languages, $languages );
    $statuses = $participant_class_name::get_enum_values( 'status' );
    $statuses = array_combine( $statuses, $statuses );
    $cohorts = $participant_class_name::get_enum_values( 'cohort' );
    $cohorts = array_combine( $cohorts, $cohorts );
    $sources = $participant_class_name::get_enum_values( 'source' );
    $sources = array_combine( $sources, $sources );
    
    $sites = array();
    $site_class_name = lib::get_class_name( 'database\site' );
    $site_mod = lib::create( 'database\modifier' );
    $site_mod->order( 'name' );
    foreach( $site_class_name::select( $site_mod ) as $db_site ) 
      $sites[$db_site->id] = sprintf( '%s (%s)', $db_site->name, $db_site->cohort );

    // set the view's items
    $this->set_item( 'active', true, true );
    $this->set_item( 'uid', is_null( $this->new_uid ) ? '' : $this->new_uid, true );
    $this->set_item( 'first_name', '', true );
    $this->set_item( 'last_name', '', true );
    $this->set_item( 'source_id', key( $sources ), false, $sources );
    $this->set_item( 'cohort', key( $cohorts ), true, $cohorts );
    $this->set_item( 'gender', key( $genders ), true, $genders );
    $this->set_item( 'date_of_birth', '' );
    $this->set_item( 'language', '', false, $languages );
    $this->set_item( 'email', '' );
    $this->set_item( 'status', '', false, $statuses );
    $this->set_item( 'no_in_home', false, true );
    $this->set_item( 'prior_contact_date', '' );
    // this particular entry is filled in by the push/participant_new operation
    $this->set_item( 'person_id', 0 );
  }

  /**
   * The unique identifier to assign to the participant, or null if none are available.
   * @var string
   * @access protected
   */
  protected $new_uid = NULL;
}
?>
