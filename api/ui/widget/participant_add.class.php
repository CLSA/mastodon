<?php
/**
 * participant_add.class.php
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
 * widget participant add
 * 
 * @package mastodon\ui
 */
class participant_add extends base_view
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

    $this->new_uid = db\participant::get_new_uid();
    
    // define all columns defining this record
    $this->add_item( 'active', 'boolean', 'Active' );
    $this->add_item( 'uid', is_null( $this->new_uid ) ? 'string' : 'hidden', 'Unique ID' );
    $this->add_item( 'first_name', 'string', 'First Name' );
    $this->add_item( 'last_name', 'string', 'Last Name' );
    $this->add_item( 'source', 'enum', 'Source' );
    $this->add_item( 'cohort', 'enum', 'Cohort' );
    $this->add_item( 'gender', 'enum', 'Gender' );
    $this->add_item( 'date_of_birth', 'date', 'Date of Birth' );
    $this->add_item( 'language', 'enum', 'Preferred Language' );
    $this->add_item( 'email', 'string', 'Email' );
    $this->add_item( 'status', 'enum', 'Condition' );
    $this->add_item( 'eligible', 'boolean', 'Eligible' );
    $this->add_item( 'no_in_home', 'boolean', 'No in Home' );
    $this->add_item( 'prior_contact_date', 'date', 'Prior Contact Date' );
    $this->add_item( 'person_id', 'hidden' );
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
    $genders = db\participant::get_enum_values( 'gender' );
    $genders = array_combine( $genders, $genders );
    $languages = db\participant::get_enum_values( 'language' );
    $languages = array_combine( $languages, $languages );
    $statuses = db\participant::get_enum_values( 'status' );
    $statuses = array_combine( $statuses, $statuses );
    $cohorts = db\participant::get_enum_values( 'cohort' );
    $cohorts = array_combine( $cohorts, $cohorts );
    $sources = db\participant::get_enum_values( 'source' );
    $sources = array_combine( $sources, $sources );
    
    $sites = array();
    foreach( db\site::select() as $db_site ) 
      $sites[$db_site->id] = sprintf( '%s (%s)', $db_site->name, $db_site->cohort );

    // set the view's items
    $this->set_item( 'active', true, true );
    $this->set_item( 'uid', is_null( $this->new_uid ) ? '' : $this->new_uid, true );
    $this->set_item( 'first_name', '', true );
    $this->set_item( 'last_name', '', true );
    $this->set_item( 'source', key( $sources ), true, $sources );
    $this->set_item( 'cohort', key( $cohorts ), true, $cohorts );
    $this->set_item( 'gender', key( $genders ), true, $genders );
    $this->set_item( 'date_of_birth', '' );
    $this->set_item( 'language', '', false, $languages );
    $this->set_item( 'email', '' );
    $this->set_item( 'status', '', false, $statuses );
    $this->set_item( 'eligible', true, true );
    $this->set_item( 'no_in_home', false, true );
    $this->set_item( 'prior_contact_date', '' );
    // this particular entry is filled in during the push in particpant_new finish()
    $this->set_item( 'person_id', 0 );

    $this->finish_setting_items();
  }

  /**
   * The unique identifier to assign to the participant, or null if none are available.
   * @var string
   * @access protected
   */
  protected $new_uid = NULL;
}
?>
