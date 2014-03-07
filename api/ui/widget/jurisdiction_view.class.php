<?php
/**
 * jurisdiction_view.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget jurisdiction view
 */
class jurisdiction_view extends \cenozo\ui\widget\jurisdiction_view
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

    // specify in the heading which service this jurisdiction belongs to
    $this->set_heading(
      sprintf( '%s for %s',
               $this->get_heading(),
               $this->get_record()->get_service()->title ) );
  }
}
