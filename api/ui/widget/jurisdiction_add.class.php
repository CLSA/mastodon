<?php
/**
 * jurisdiction_add.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget jurisdiction add
 */
class jurisdiction_add extends \cenozo\ui\widget\jurisdiction_add
{
  /**
   * Processes arguments, preparing them for the operation.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @throws exception\notice
   * @access protected
   */
  protected function prepare()
  {
    parent::prepare();
    
    // specify in the heading which application this jurisdiction belongs to
    $this->set_heading(
      sprintf( '%s for %s',
               $this->get_heading(),
               $this->parent->get_record()->title ) );
  }
}
