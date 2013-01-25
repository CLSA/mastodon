<?php
/**
 * service_add_cohort.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget service add_cohort
 */
class service_add_cohort extends \cenozo\ui\widget\base_add_list
{
  /**
   * Constructor
   * 
   * Defines all variables which need to be set for the associated template.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param string $name The name of the cohort.
   * @param array $args An associative array of arguments to be processed by the widget
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'service', 'cohort', $args );
  }

  /**
   * Overrides the cohort list widget's method.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param database\modifier $modifier Modifications to the list.
   * @return int
   * @access protected
   */
  public function determine_cohort_count( $modifier = NULL )
  {
    $cohort_class_name = lib::get_class_name( 'database\cohort' );
    if( is_null( $modifier ) ) $modifier = lib::create( 'database\modifier' );
    $modifier->where( 'id', '!=', $this->get_record()->id );
    return $cohort_class_name::count( $modifier );
  }

  /**
   * Overrides the cohort list widget's method.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param database\modifier $modifier Modifications to the list.
   * @return array( record )
   * @access protected
   */
  public function determine_cohort_list( $modifier = NULL )
  {
    $cohort_class_name = lib::get_class_name( 'database\cohort' );
    if( is_null( $modifier ) ) $modifier = lib::create( 'database\modifier' );
    $modifier->where( 'id', '!=', $this->get_record()->id );
    return $cohort_class_name::select( $modifier );
  }
}
