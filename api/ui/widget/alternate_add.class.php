<?php
/**
 * alternate_add.class.php
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
 * widget alternate add
 * 
 * @package mastodon\ui
 */
class alternate_add extends base_view
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
    parent::__construct( 'alternate', 'add', $args );
    
    // define all columns defining this record
    $this->add_item( 'participant_id', 'hidden' );
    $this->add_item( 'first_name', 'string', 'First Name' );
    $this->add_item( 'last_name', 'string', 'Last Name' );
    $this->add_item( 'association', 'string', 'Association' );
    $this->add_item( 'alternate', 'boolean', 'Alternate' );
    $this->add_item( 'informant', 'boolean', 'Informant' );
    $this->add_item( 'proxy', 'boolean', 'Proxy' );
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
    
    // this widget must have a parent, and it's subject must be a participant
    if( is_null( $this->parent ) || 'participant' != $this->parent->get_subject() )
      throw new exc\runtime(
        'Association widget must have a parent with participant as the subject.', __METHOD__ );

    // set the view's items
    $this->set_item( 'participant_id', $this->parent->get_record()->id );
    $this->set_item( 'first_name', '', true );
    $this->set_item( 'last_name', '', true );
    $this->set_item( 'association', '', true );
    $this->set_item( 'alternate', true, true );
    $this->set_item( 'informant', false, true );
    $this->set_item( 'proxy', false, true );

    $this->finish_setting_items();
  }
}
?>
