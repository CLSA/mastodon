<?php
/**
 * site_view.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget site view
 * 
 * @package mastodon\ui
 */
class site_view extends \cenozo\ui\widget\site_view
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
    parent::__construct( $args );
    
    // create an associative array with everything we want to display about the site
    $this->add_item( 'cohort', 'enum', 'Type' );
  }

  /**
   * Finish setting the variables in a widget.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function finish()
  {
    // create enum arrays
    $cohorts = db\site::get_enum_values( 'cohort' );
    $cohorts = array_combine( $cohorts, $cohorts );

    // set the view's items
    $this->set_item( 'cohort', $this->get_record()->cohort, true, $cohorts );

    parent::finish();
  }
}
?>
