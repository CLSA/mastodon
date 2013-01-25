<?php
/**
 * site_add.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget site add
 */
class site_add extends \cenozo\ui\widget\site_add
{
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
    $this->add_item( 'service_id', 'enum', 'Service' );
  }

  /**
   * Sets up the operation with any pre-execution instructions that may be necessary.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function setup()
  {
    parent::setup();

    // create enum arrays
    $service_class_name = lib::get_class_name( 'database\service' );
    foreach( $service_class_name::select() as $db_service )
      $service_list[$db_service->id] = $db_service->name;

    // set the view's items
    $this->set_item( 'service_id', key( $service_list ), true, $service_list );
  }
}
