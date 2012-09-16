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
   * Processes arguments, preparing them for the operation.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @throws exception\notice
   * @access protected
   */
  protected function prepare()
  {
    parent::prepare();

    $this->exclude_list( array(
      'address',
      'alternate',
      'availability',
      'consent',
      'phone' ) );
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

    $operation_class_name = lib::get_class_name( 'database\operation' );

    $utilities = $this->get_variable( 'utilities' );

    // insert participant import into the utilities
    $db_operation = $operation_class_name::get_operation( 'widget', 'import', 'add' );
    if( lib::create( 'business\session' )->is_allowed( $db_operation ) )
      $utilities[] = array( 'heading' => 'Participant Import',
                            'type' => 'widget',
                            'subject' => 'import',
                            'name' => 'add' );

    // insert participant site reassign into the utilities
    $db_operation =
      $operation_class_name::get_operation( 'widget', 'participant', 'site_reassign' );
    if( lib::create( 'business\session' )->is_allowed( $db_operation ) )
      $utilities[] = array( 'heading' => 'Participant Reassign',
                            'type' => 'widget',
                            'subject' => 'participant',
                            'name' => 'site_reassign' );

    $this->set_variable( 'utilities', $utilities );
  }
}
?>
