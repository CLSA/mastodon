<?php
/**
 * region_site_view.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget region_site view
 */
class region_site_view extends \cenozo\ui\widget\region_site_view
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

    // specify in the heading which application this region_site belongs to
    $this->set_heading(
      sprintf( '%s for %s',
               $this->get_heading(),
               $this->get_record()->get_application()->title ) );
  }
}
