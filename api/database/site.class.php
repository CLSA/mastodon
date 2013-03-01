<?php
/**
 * site.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\database;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * site: record
 */
class site extends \cenozo\database\site
{
  /**
   * Call parent method without restricting records by service.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param database\modifier $modifier Modifications to the selection.
   * @param boolean $count If true the total number of records instead of a list
   * @param boolean $full Do not use, parameter ignored.
   * @access public
   * @static
   */
  public static function select( $modifier = NULL, $count = false, $full = false )
  {
    return parent::select( $modifier, $count, true );
  }

  /**
   * Gives a complete name for the site in the form of "name (service)"
   * 
   * @author Patrick Emond <emondpd@mcamster.ca>
   * @access public
   */
  public function get_full_name()
  { 
    $db_service = $this->get_service();
    return sprintf( '%s (%s)', $this->name, is_null( $db_service ) ? 'none' : $this->get_service()->name );
  }  
}
