<?php
/**
 * participant_import.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget participant import
 * 
 * @package mastodon\ui
 */
class participant_import extends \mastodon\ui\widget
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
    parent::__construct( 'participant', 'import', $args );
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

    $quexf_manager = lib::create( 'business\quexf_manager' );

    $this->set_variable(
      'participant_count', $quexf_manager->get_participant_count() );
    $this->set_variable(
      'invalid_participant_count', $quexf_manager->get_invalid_participant_count() );
    $this->set_variable(
      'valid_participant_count', $quexf_manager->get_valid_participant_count() );
    
    $class_name = lib::get_class_name( 'database\participant' );
    $this->set_variable(
      'pool_size', $class_name::get_uid_pool_count() );
  }
}
?>
