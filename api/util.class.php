<?php
/**
 * util.class.php
 * 
 * @author Dean Inglis <inglisd@mcmaster.ca>
 * @filesource
 */

namespace mastodon;

/**
 * util: utility class of static methods
 *
 * Extends cenozo's util class with additional functionality.
 */
class util extends \cenozo\util
{
  /**
   * Given address details this method returns an array with two elements which map to
   * the address table's address1 and address2 lines.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param string apartment_number The apartment number only.
   * @param string street_number The street number only.
   * @param string street_name The street name.
   * @param string box The post office box only.
   * @param string rural_route The rural route number only.
   * @param string other Any other address information not in the other arguments
   * @return array( two strings )
   * @access public
   */
  public static function parse_address(
    $value_number = NULL,
    $street_number = NULL,
    $street_name = NULL,
    $box = NULL,
    $rural_route = NULL,
    $other = NULL )
  {
    // import data to the address table
    $address[0] = NULL;
    $address[1] = NULL;
    
    if( !is_null( $street_number ) &&
        !is_null(  $street_name ) )
    {
      $address[0] = $street_number.' '.$street_name;
    }

    if( !is_null( $value_number ) )
    {
      $value = 'Apt '.$value_number;
      $address[0] = is_null( $address[0] ) ? $value : $address[0] = $value.', '.$address[0];
    }

    if( !is_null( $box ) )
    {
      $value = 'PO Box '.$box;
      if( is_null( $address[0] ) ) $address[0] = $value;
      else $address[1] = $value;
    }

    if( !is_null( $rural_route ) )
    {
      $value = 'RR '.$rural_route;
      if( is_null( $address[0] ) ) $address[0] = $value;
      else $address[0] = is_null( $address[0] ) ? $value : $address[0] = $address[0].', '.$value;
    }

    if( !is_null( $other ) )
    {
      if( is_null( $address[0] ) ) $address[0] = $other;
      else $address[1] = is_null( $address[1] ) ? $other : $address[1] = $address[1].', '.$other;
    }
    
    return $address;
  }
}
?>
