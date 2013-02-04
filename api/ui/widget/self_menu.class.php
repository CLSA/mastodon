<?php
/**
 * self_menu.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget self menu
 */
class self_menu extends \cenozo\ui\widget\self_menu
{
  /**
   * Sets up the operation with any pre-execution instructions that may be necessary.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function setup()
  {
    parent::setup();

    $operation_class_name = lib::get_class_name( 'database\operation' );

    $utilities = $this->get_variable( 'utilities' );

    // insert participant import into the utilities
    $db_operation = $operation_class_name::get_operation( 'widget', 'import', 'add' );
    if( lib::create( 'business\session' )->is_allowed( $db_operation ) )
      $utilities[] = array( 'heading' => 'Participant Import',
                            'type' => 'widget',
                            'subject' => 'import',
                            'name' => 'add' );

    $this->set_variable( 'utilities', $utilities );
  }
}
