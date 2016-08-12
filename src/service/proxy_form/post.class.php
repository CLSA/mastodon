<?php
/**
 * post.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\service\proxy_form;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * The base class of all post services.
 */
class post extends \cenozo\service\post
{
  /**
   * Extends parent method
   */
  protected function execute()
  {
    parent::execute();

    // now create the entry as a distinct service
    $proxy_form_entry_service = lib::create(
      'service\post',
      'proxy_form_entry',
      NULL,
      $this->get_file_as_raw() );
    $proxy_form_entry_service->process();

    // finally, check if the new entry is valid and import if it is
  }
}
