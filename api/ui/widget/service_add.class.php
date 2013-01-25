<?php
/**
 * service_add.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget service add
 */
class service_add extends \cenozo\ui\widget\base_view
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
    parent::__construct( 'service', 'add', $args );
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
    
    // define all columns defining this record
    $this->add_item( 'name', 'string', 'Name' );
    $this->add_item( 'cohort_id', 'enum', 'Cohort' );
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
    $this->set_item( 'name', '' );
    $this->set_item( 'cohort_id', key( $cohorts ), true, $cohorts );
  }
}
