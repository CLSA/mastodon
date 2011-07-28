<?php
/**
 * alternate_new.class.php
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
 * push: alternate new
 *
 * Create a new alternate.
 * @package mastodon\ui
 */
class alternate_new extends base_new
{
  /**
   * Constructor.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Push arguments
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'alternate', $args );
  }

  /**
   * Executes the push.
   * Since creating a new alternate requires first creating a new person this method overrides
   * its parent method without calling (which is the usual behaviour).
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function finish()
  {
    // make sure the name and association columns aren't blank
    $columns = $this->get_argument( 'columns' );
    if( !array_key_exists( 'first_name', $columns ) || 0 == strlen( $columns['first_name'] ) )
      throw new exc\notice( 'The alternate\'s first name cannot be left blank.', __METHOD__ );
    if( !array_key_exists( 'last_name', $columns ) || 0 == strlen( $columns['last_name'] ) )
      throw new exc\notice( 'The alternate\'s last name cannot be left blank.', __METHOD__ );
    if( !array_key_exists( 'association', $columns ) || 0 == strlen( $columns['association'] ) )
      throw new exc\notice( 'The alternate\'s association cannot be left blank.', __METHOD__ );
    
    foreach( $columns as $column => $value ) $this->get_record()->$column = $value;

    try
    {
      // create a person record and like the new record to it
      $db_person = new db\person();
      $db_person->save();
      $this->get_record()->person_id = $db_person->id;
      $this->get_record()->save();
    }
    catch( db\base_exception $e )
    {
      // failed to create alternate, delete the person record
      if( !is_null( $db_person->id ) ) $db_person->delete();

      if( 'database' == $e->get_type() )
      {
        if( $e->is_duplicate_entry() )
        {
          throw new exc\notice(
            'Unable to create the new '.$this->get_subject().' because it is not unique.',
            __METHOD__, $e );
        }
        else if( $e->is_missing_data() )
        {
          $matches = array();
          $found = preg_match( "/Column '[^']+'/", $e->get_raw_message(), $matches );
  
          if( $found )
          {
            $message = sprintf(
              'You must specify "%s" in order to create a new %s.',
              substr( $matches[0], 8, -1 ),
              $this->get_subject() );
          }
          else
          {
            $message = sprintf(
              'Unable to create the new %s, not all mandatory fields have been filled out.',
              $this->get_subect() );
          }
  
          throw new exc\notice( $message, __METHOD__, $e );
        }

        throw $e;
      }
    }
  }
}
?>
