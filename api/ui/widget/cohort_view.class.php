<?php
/**
 * cohort_view.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget cohort view
 */
class cohort_view extends \cenozo\ui\widget\base_view
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
    parent::__construct( 'cohort', 'view', $args );
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

    // create an associative array with everything we want to display about the cohort
    $this->add_item( 'name', 'string', 'Name' );
    $this->add_item( 'grouping', 'enum', 'Grouping' );
    $this->add_item( 'participants', 'constant', 'Participants' );
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
    $groupings = $cohort_class_name::get_enum_values( 'grouping' );
    $groupings = array_combine( $groupings, $groupings );

    // set the view's items
    $this->set_item( 'name', $this->get_record()->name );
    $this->set_item( 'grouping', $this->get_record()->grouping, true, $groupings );
    $this->set_item( 'participants', $this->get_record()->get_participant_count() );
  }
}
?>
