<?php
/**
 * site_add.class.php
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
 * widget site add
 * 
 * @package mastodon\ui
 */
class site_add extends base_view
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
    parent::__construct( 'site', 'add', $args );
    
    // define all columns defining this record
    $this->add_item( 'name', 'string', 'Name' );
    $this->add_item( 'cohort', 'enum', 'Type' );
    $this->add_item( 'timezone', 'enum', 'Time Zone' );
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
    $cohorts = db\site::get_enum_values( 'cohort' );
    $cohorts = array_combine( $cohorts, $cohorts );
    $timezones = db\site::get_enum_values( 'timezone' );
    $timezones = array_combine( $timezones, $timezones );

    // set the view's items
    $this->set_item( 'name', '', true );
    $this->set_item( 'cohort', key( $cohorts ), true, $cohorts );
    $this->set_item( 'timezone', key( $timezones ), true, $timezones );

    $this->finish_setting_items();
  }
}
?>
