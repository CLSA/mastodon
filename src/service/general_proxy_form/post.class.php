<?php
/**
 * post.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace mastodon\service\general_proxy_form;
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

    $general_proxy_form_entry_class_name = lib::get_class_name( 'database\general_proxy_form_entry' );

    $db_general_proxy_form = $this->get_leaf_record();
    $post_object = $this->get_file_as_object();

    // create an entry for the new proxy form
    $db_general_proxy_form_entry = lib::create( 'database\general_proxy_form_entry' );
    foreach( $db_general_proxy_form_entry->get_column_names() as $column_name )
      if( 'id' != $column_name && property_exists( $post_object, $column_name ) )
        $db_general_proxy_form_entry->$column_name = $post_object->$column_name;

    // write the form
    if( property_exists( $post_object, 'data' ) )
    {
      $form_decoded = base64_decode( chunk_split( $post_object->data ) );
      if( false == $form_decoded )
        throw lib::create( 'exception\runtime', 'Unable to decode form argument.', __METHOD__ );

      $db_general_proxy_form->write_form( $form_decoded );
      $db_general_proxy_form_entry->signed = true;
    }

    $db_general_proxy_form_entry->general_proxy_form_id = $db_general_proxy_form->id;
    $db_general_proxy_form_entry->submitted = true;
    $db_general_proxy_form_entry->save();

    // finally, check if the new entry is valid and import if it is
    if( 0 == count( $db_general_proxy_form_entry->get_errors() ) )
      $db_general_proxy_form->import( $db_general_proxy_form_entry );
  }
}
