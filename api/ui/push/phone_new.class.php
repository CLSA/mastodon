<?php
/**
 * phone_new.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: phone new
 *
 * Create a new phone.
 * @package mastodon\ui
 */
class phone_new extends base_new
{
  /**
   * Constructor.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Push arguments
   * @access public
   */
  public function __construct( $args )
  {
    if( array_key_exists( 'noid', $args ) )
    {
      // use the noid argument and remove it from the args input
      $noid = $args['noid'];
      unset( $args['noid'] );

      // make sure there is sufficient information
      if( !is_array( $noid ) ||
          !array_key_exists( 'participant.uid', $noid ) )
        throw lib::create( 'exception\argument', 'noid', $noid, __METHOD__ );
      
      $db_participant = db\participant::get_unique_record( 'uid', $noid['participant.uid'] );
      if( !$db_participant ) throw lib::create( 'exception\argument', 'noid', $noid, __METHOD__ );
      $args['columns']['person_id'] = $db_participant->person_id;
      
      if( array_key_exists( 'address.rank', $noid ) )
      {
        $db_address = db\address::get_unique_record(
          array( 'person_id', 'rank' ),
          array( $db_participant->person_id, $noid['address.rank'] ) );
        if( !$db_address ) throw lib::create( 'exception\argument', 'noid', $noid, __METHOD__ );
        $args['columns']['address_id'] = $db_address->id;
      }
    }

    parent::__construct( 'phone', $args );
  }

  /**
   * Overrides the parent method to make sure the number isn't blank and is a valid number
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @throws exception\notice
   * @access public
   */
  public function finish()
  {
    $columns = $this->get_argument( 'columns' );
    $number = $columns['number'];
    
    // validate the number
    if( 10 != strlen( preg_replace( '/[^0-9]/', '', $columns['number'] ) ) )
      throw lib::create( 'exception\notice',
        'Phone numbers must have exactly 10 digits.', __METHOD__ );

    parent::finish();
  }
}
?>
