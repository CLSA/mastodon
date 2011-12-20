<?php
/**
 * participant_import.class.php
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

    $quexf_manager = new bus\quexf_manager();

    $this->set_variable(
      'participant_count', $quexf_manager->get_participant_count() );
    $this->set_variable(
      'invalid_participant_count', $quexf_manager->get_invalid_participant_count() );
    $this->set_variable(
      'valid_participant_count', $quexf_manager->get_valid_participant_count() );
    $this->set_variable(
      'pool_size', db\participant::get_uid_pool_count() );
  }
}
?>
