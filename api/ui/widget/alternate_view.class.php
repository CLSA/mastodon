<?php
/**
 * alternate_view.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget alternate view
 */
class alternate_view extends \cenozo\ui\widget\alternate_view
{
  /**
   * Sets up the operation with any pre-execution instructions that may be necessary.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function setup()
  {
    parent::setup();

    // add a proxy form download action
    $db_proxy_form = $this->get_record()->get_proxy_form();
    if( !is_null( $db_proxy_form ) )
      $this->set_variable( 'proxy_form_id', $db_proxy_form->id );
    $this->add_action( 'proxy_form', 'Proxy Form', NULL,
      'Download this alternate\'s consent for proxy form, if available' );
  }
}
