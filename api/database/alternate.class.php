<?php
/**
 * alternate.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\database;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * alternate: record
 */
class alternate extends person
{
  /**
   * This is a convenience method to get a alternate's proxy form, if it exists.
   * For design reasons the alternate and proxy_form tables do not have a one-to-one
   * relationship, therefor the base class will refuse a call to get_proxy_form(), so
   * this method fakes it for us.
   * NOTE: alternates this method will automatically resolve the proxy form whether it
   *       is from being a proxy or informant.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @return database\proxy_form
   * @access public
   */
  public function get_proxy_form()
  {
    $proxy_form_class_name = lib::get_class_name( 'database\proxy_form' );
    $modifier = lib::create( 'database\modifier' );
    
    // check for the appropriate alternate type
    if( $this->proxy )
      $modifier->where( 'proxy_alternate_id', '=', $this->id );
    else if( $this->informant )
      $modifier->where( 'informant_alternate_id', '=', $this->id );
    else return NULL;

    $proxy_form_list = $proxy_form_class_name::select( $modifier );
    return count( $proxy_form_list ) ? current( $proxy_form_list ) : NULL;
  }
}
?>
