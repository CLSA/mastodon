<?php
/**
 * participant_delete_alternate.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: participant delete_alternate
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
   * Processes arguments, preparing them for the operation.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function prepare()
  {
    parent::prepare();

    // get the alternate's person record
    $db_alternate = lib::create( 'database\alternate', $this->get_argument( 'remove_id' ) );
    $this->db_person = $db_alternate->get_person();
  }

  /**
   * Finishes the operation with any post-execution instructions that may be necessary.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function finish()
  {
    parent::finish();

    // delete the alternate's person record
    try
    {
      $this->db_person->delete();
    }
    catch( \cenozo\exception\database $e )
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

  /**
   * The person record associated with the alternate being deleted
   * @var database\person
   * @access private
   */
  private $db_person = NULL;
}
?>
