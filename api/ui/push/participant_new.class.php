<?php
/**
 * participant_new.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: participant new
 *
 * Create a new participant.
 * @package mastodon\ui
 */
class participant_new extends \cenozo\ui\push\base_new
{
  /**
   * Constructor.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Push arguments
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'participant', $args );

    // never send a machine request since this is a new participant
    $this->set_machine_request_enabled( false );
  }

  /**
   * Executes the push.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function finish()
  {
    // make sure the name column isn't blank
    $columns = $this->get_argument( 'columns' );
    if( !array_key_exists( 'first_name', $columns ) || 0 == strlen( $columns['first_name'] ) )
      throw lib::create( 'exception\notice', 'The participant\'s first name cannot be left blank.', __METHOD__ );
    if( !array_key_exists( 'last_name', $columns ) || 0 == strlen( $columns['last_name'] ) )
      throw lib::create( 'exception\notice', 'The participant\'s last name cannot be left blank.', __METHOD__ );

    if( 0 == $columns['person_id'] )
    {
      $db_person = lib::create( 'database\person' );
      $db_person->save();
      // direct access to the parent operation class's protected arguments ivar
      // is necessary in this isolated case since a person id is required
      // to manually create a new particpant via the widget interface.
      // in future, it may be necessary to create a set_arguments method 
      // in the operation class and make arguments a private ivar
      $this->arguments['columns']['person_id'] = $db_person->id;
    }

    parent::finish();
  }
}
?>
