<?php
/**
 * activity_list.class.php
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
 * widget activity list
 * 
 * @package mastodon\ui
 */
class activity_list extends site_restricted_list
{
  /**
   * Constructor
   * 
   * Defines all variables required by the activity list.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args An associative array of arguments to be processed by the widget
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'activity', $args );
    
    $this->add_column( 'user.name', 'string', 'User', true );
    $this->add_column( 'site.name', 'string', 'Site', true );
    $this->add_column( 'role.name', 'string', 'Role', true );
    $this->add_column( 'operation.type', 'string', 'Type', true );
    $this->add_column( 'operation.subject', 'string', 'Subject', true );
    $this->add_column( 'operation.name', 'string', 'Name', true );
    $this->add_column( 'elapsed', 'string', 'Elapsed', true );
    $this->add_column( 'datetime', 'datetime', 'Date', true );
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
    
    foreach( $this->get_record_list() as $record )
    {
      $this->add_row( $record->id,
        array( 'user.name' => $record->get_user()->name,
               'site.name' => $record->get_site()->name,
               'role.name' => $record->get_role()->name,
               'operation.type' => $record->get_operation()->type,
               'operation.subject' => $record->get_operation()->subject,
               'operation.name' =>$record->get_operation()->name,
               'elapsed' => sprintf( '%0.2fs', $record->elapsed ),
               'datetime' => $record->datetime ) );
    }

    $this->finish_setting_rows();
  }
}
?>
