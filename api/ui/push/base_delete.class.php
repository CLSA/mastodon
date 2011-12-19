<?php
/**
 * base_delete.class.php
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
 * Base class for all record "delete" push operations.
 * 
 * @package mastodon\ui
 */
abstract class base_delete extends base_record
{
  /**
   * Constructor.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param string $subject The widget's subject.
   * @param array $args Push arguments
   * @throws exception\argument
   * @access public
   */
  public function __construct( $subject, $args )
  {
    parent::__construct( $subject, 'delete', $args );

    // make sure we have an id (we don't actually need to use it since the parent does)
    $this->get_argument( 'id' );
  }
  
  /**
   * Executes the push.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function finish()
  {
    try
    {
      $this->get_record()->delete();
    }
    catch( exc\database $e )
    { // help describe exceptions to the user
      if( $e->is_constrained() )
      {
        throw new exc\notice(
          'Unable to delete the '.$this->get_subject().
          ' because it is being referenced by the database.', __METHOD__, $e );
      }

      throw $e;
    }
  }
}
?>
