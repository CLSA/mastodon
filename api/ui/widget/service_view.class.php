<?php
/**
 * service_view.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget service view
 */
class service_view extends \cenozo\ui\widget\base_view
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
    parent::__construct( 'service', 'view', $args );
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

    // create an associative array with everything we want to display about the service
    $this->add_item( 'name', 'string', 'Name' );
    $this->add_item( 'cohort_id', 'enum', 'Cohort' );
    $this->add_item( 'sites', 'constant', 'Sites' );

    // create the cohort sub-list widget
    $this->cohort_list = lib::create( 'ui\widget\cohort_list', $this->arguments );
    $this->cohort_list->set_parent( $this );
    $this->cohort_list->set_heading( 'Additional Cohorts' );
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

    $cohort_class_name = lib::get_class_name( 'database\cohort' );

    // create enum arrays
    $cohorts = array();
    $cohort_mod = lib::create( 'database\modifier' );
    $cohort_mod->order( 'name' );
    foreach( $cohort_class_name::select( $cohort_mod ) as $db_cohort )
      $cohorts[$db_cohort->id] = $db_cohort->name;

    // set the view's items
    $this->set_item( 'name', $this->get_record()->name );
    $this->set_item( 'cohort_id', $this->get_record()->cohort_id, true, $cohorts );
    $this->set_item( 'sites', $this->get_record()->get_site_count() );

    try
    {
      $this->cohort_list->process();
      $this->set_variable( 'cohort_list', $this->cohort_list->get_variables() );
    }
    catch( \cenozo\exception\permission $e ) {}
  }

  /**
   * The cohort list widget.
   * @var cohort_list
   * @access protected
   */
  protected $cohort_list = NULL;
}
