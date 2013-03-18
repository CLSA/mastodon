<?php
/**
 * participant_list.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget participant list
 */
class participant_list extends \cenozo\ui\widget\participant_list
{
  /**
   * Set the rows array needed by the template.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function setup()
  {
    parent::setup();

    // include the participant import action if the widget isn't parented
    if( is_null( $this->parent ) && false )
    {
      $operation_class_name = lib::get_class_name( 'database\operation' );
      $db_operation = $operation_class_name::get_operation( 'widget', 'import', 'add' );
      if( lib::create( 'business\session' )->is_allowed( $db_operation ) )
        $this->add_action( 'import', 'Import', $db_operation,
          'Import participants from an external CSV file' );
    }

    // remove the site column since it is meaningless here
    $this->remove_column( 'site' );
  }
}
