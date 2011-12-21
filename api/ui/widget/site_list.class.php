<?php
/**
 * site_list.class.php
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
 * widget site list
 * 
 * @package mastodon\ui
 */
class site_list extends \cenozo\ui\widget\base_list
{
  /**
   * Constructor
   * 
   * Defines all variables required by the site list.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args An associative array of arguments to be processed by the widget
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'site', $args );
    
    $this->add_column( 'name', 'string', 'Name', true );
    $this->add_column( 'cohort', 'string', 'Type', true );
    $this->add_column( 'users', 'number', 'Users', false );
    $this->add_column( 'last', 'fuzzy', 'Last activity', false );
  }

  /**
   * Set the rows array needed by the template.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function finish()
  {
    parent::finish();
    
    // get all sites
    foreach( $this->get_record_list() as $record )
    {
      // determine the last activity
      $db_activity = $record->get_last_activity();
      $last = is_null( $db_activity ) ? null : $db_activity->datetime;

      $this->add_row( $record->id,
        array( 'name' => $record->name,
               'cohort' => $record->cohort,
               'users' => $record->get_user_count(),
               'last' => $last ) );
    }

    $this->finish_setting_rows();
  }
}
?>
