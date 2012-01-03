<?php
/**
 * participant_delete_alternate.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: participant delete_alternate
 * 
 * @package mastodon\ui
 */
class participant_delete_alternate extends \cenozo\ui\push\base_delete_record
{
  /**
   * Constructor.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Push arguments
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'participant', 'alternate', $args );
  }

  /**
   * Override parent method to delete the alternate's person record
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function finish()
  {
    // get the alternate's person record
    $db_alternate = lib::create( 'database\alternate', $this->get_argument( 'remove_id' ) );
    $db_person = $db_alternate->get_person();

    // call the parent method to delete the alternate
    parent::finish();

    // now delete the person
    try
    {
      $db_person->delete();
    }
    catch( exc\database $e )
    { // help describe exceptions to the user
      if( $e->is_constrained() )
      {
        throw lib::create( 'exception\notice',
          'Unable to delete the '.$this->child_subject.
          ' because it is being referenced by the database.', __METHOD__, $e );
      }

      throw $e;
    } 
  }
}
?>
