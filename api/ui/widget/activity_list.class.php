<?php
/**
 * activity_list.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget activity list
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

    $this->add_column( 'user.name', 'string', 'User', true );
    $this->add_column( 'site.name', 'string', 'Site', true );
    $this->add_column( 'role.name', 'string', 'Role', true );
    $this->add_column( 'operation.type', 'string', 'Type', true );
    $this->add_column( 'operation.subject', 'string', 'Subject', true );
    $this->add_column( 'operation.name', 'string', 'Name', true );
    $this->add_column( 'elapsed', 'string', 'Time', true, false ); // not restrictable
    $this->add_column( 'error_code', 'string', 'Error', true );
    $this->add_column( 'datetime', 'datetime', 'Date', true );
  }

  /**
   * Defines all rows in the list.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function setup()
  {
    parent::setup();
    
    foreach( $this->get_record_list() as $record )
    {
      $db_operation = $record->get_operation();
      $this->add_row( $record->id,
        array( 'user.name' => $record->get_user()->name,
               'site.name' => $record->get_site()->name,
               'role.name' => $record->get_role()->name,
               'operation.type' => is_null( $db_operation ) ? 'n/a' : $db_operation->type,
               'operation.subject' => is_null( $db_operation ) ? 'n/a' : $db_operation->subject,
               'operation.name' => is_null( $db_operation ) ? 'n/a' : $db_operation->name,
               'elapsed' => sprintf( '%0.2fs', $record->elapsed ),
               'error_code' => is_null( $record->error_code ) ? '' : $record->error_code,
               'datetime' => $record->datetime ) );
    }
  }
}
?>
