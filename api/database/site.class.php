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
   * Gives a complete name for the site in the form of "name (application)"
   * 
   * @author Patrick Emond <emondpd@mcamster.ca>
   * @access public
   */
  public function get_full_name()
  { 
    $db_application = $this->get_application();
    return sprintf( '%s (%s)', $this->name, is_null( $db_application ) ? 'none' : $this->get_application()->name );
  }  
}
