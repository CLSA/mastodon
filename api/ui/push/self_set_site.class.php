<?php
/**
 * self_set_site.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: self set_site
 *
 * Changes the current user's site.
 * Arguments must include 'site'.
 * @package mastodon\ui
 */
class self_set_site extends \cenozo\ui\push\self_set_site
{
  /**
   * Constructor.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Push arguments
   * @access public
   */
  public function __construct( $args )
  {
    // if the name and cohort is provided instead of the id then fetch the site id
    if( array_key_exists( 'noid', $args ) )
    {
      // use the noid argument and remove it from the args input
      $noid = $args['noid'];
      unset( $args['noid'] );
      
      // make sure there is sufficient information
      if( !is_array( $noid ) ||
          !array_key_exists( 'site.name', $noid ) ||
          !array_key_exists( 'site.cohort', $noid ) )
        throw lib::create( 'exception\argument', 'noid', $noid, __METHOD__ );

      $class_name = lib::get_class_name( 'database\site' );  
      $db_site = $class_name::get_unique_record(
        array( 'name', 'cohort' ),
        array( $noid['site.name'], $noid['site.cohort'] ) );

      if( !$db_site ) throw lib::create( 'exception\argument', 'noid', $noid, __METHOD__ );
      $args['id'] = $db_site->id;
    }

    parent::__construct( $args );
  }
}
?>
