<?php
/**
 * address_new.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: address new
 *
 * Create a new address.
 * @package mastodon\ui
 */
class address_new extends base_new
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
      
      if( array_key_exists( 'region.abbreviation', $noid ) )
      {
        $db_region = db\region::get_unique_record( 'abbreviation', $noid['region.abbreviation'] );
        if( !$db_region ) throw lib::create( 'exception\argument', 'noid', $noid, __METHOD__ );
        $args['columns']['region_id'] = $db_region->id;
      }
    }

    parent::__construct( 'address', $args );
  }

  /**
   * Overrides the parent method to make sure the postcode is valid.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @throws exception\notice
   * @access public
   */
  public function finish()
  {
    $columns = $this->get_argument( 'columns' );
    $postcode = $columns['postcode'];
    
    // validate the postcode
    if( !preg_match( '/^[A-Z][0-9][A-Z] [0-9][A-Z][0-9]$/', $postcode ) && // postal code
        !preg_match( '/^[0-9]{5}$/', $postcode ) )  // zip code
      throw lib::create( 'exception\notice',
        'Postal codes must be in "A1A 1A1" format, zip codes in "01234" format.', __METHOD__ );

    parent::finish();
  }
}
?>
