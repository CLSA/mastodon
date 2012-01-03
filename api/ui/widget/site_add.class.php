<?php
/**
 * site_add.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget site add
 * 
 * @package mastodon\ui
 */
class site_add extends \cenozo\ui\widget\site_add
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
    
    // define all columns defining this record
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
    $this->set_item( 'cohort', key( $cohorts ), true, $cohorts );

    parent::finish();
  }
}
?>
