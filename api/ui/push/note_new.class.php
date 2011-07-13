<?php
/**
 * note_new.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\push;
use mastodon\log, mastodon\util;
use mastodon\business as bus;
use mastodon\database as db;
use mastodon\exception as exc;

/**
 * push: note new
 * 
 * Add a new note to the provided category.
 * @package mastodon\ui
 */
class note_new extends \mastodon\ui\push
{
  /**
   * Constructor.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Push arguments
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'note', 'new', $args );
  }
  
  /**
   * Executes the push.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @throws exception\runtime
   * @access public
   */
  public function finish()
  {
    // make sure there is a valid note category
    $category = $this->get_argument( 'category' );
    $category_id = $this->get_argument( 'category_id' );
    $note = $this->get_argument( 'note' );
    $category_class = '\\mastodon\\database\\'.$category;
    $db_record = new $category_class( $category_id );
    if( !is_a( $db_record, '\\mastodon\\database\\has_note' ) )
      throw new exc\runtime(
        sprintf( 'Tried to create new note to %s which cannot have notes.', $category ),
        __METHOD__ );

    $db_record->add_note( bus\session::self()->get_user(), $note );
  }
}
?>
