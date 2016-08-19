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

    $proxy_form_entry_class_name = lib::get_class_name( 'database\proxy_form_entry' );

    $db_proxy_form = $this->get_leaf_record();
    $post_object = $this->get_file_as_object();

    // create an entry for the new proxy form
    $db_proxy_form_entry = lib::create( 'database\proxy_form_entry' );
    foreach( $db_proxy_form_entry->get_column_names() as $column_name )
      if( 'id' != $column_name && property_exists( $post_object, $column_name ) )
        $db_proxy_form_entry->$column_name = $post_object->$column_name;

    // write the form
    if( property_exists( $post_object, 'data' ) )
    {
      $form_decoded = base64_decode( chunk_split( $post_object->data ) );
      if( false == $form_decoded )
        throw lib::create( 'exception\runtime', 'Unable to decode form argument.', __METHOD__ );

      $db_proxy_form->write_form( $form_decoded );
      $db_proxy_form_entry->signed = true;
    }

    $db_proxy_form_entry->proxy_form_id = $db_proxy_form->id;
    $db_proxy_form_entry->submitted = true;
    $db_proxy_form_entry->save();

    // finally, check if the new entry is valid and import if it is
    if( 0 == count( $db_proxy_form_entry->get_errors() ) ) $db_proxy_form->import( $db_proxy_form_entry );
  }
}
