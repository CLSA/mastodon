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

    $file = $this->get_file_as_array();

    // write the form
    if( array_key_exists( 'data', $file ) ) $this->get_leaf_record()->write_form( $file['data'] );

    // create the entry as a distinct service
    $proxy_form_entry_service = lib::create(
      'service\post',
      'proxy_form_entry',
      NULL,
      $this->get_file_as_raw() );
    $proxy_form_entry_service->process();

    // finally, check if the new entry is valid and import if it is
    $db_proxy_form_entry = $proxy_form_entry_service->get_leaf_record();
    if( 0 == count( $db_proxy_form_entry->get_errors() ) )
      $db_proxy_form_entry->get_proxy_form()->import( $db_proxy_form_entry );
  }
}
