<?php
/**
 * proxy_form.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\database
 * @filesource
 */

namespace mastodon\database;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * proxy_form: record
 *
 * @package mastodon\database
 */
class proxy_form extends base_form
{
  /**
   * Implements the parent's abstract import method.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param database\form_entry $db_base_form_entry The entry to be used as the valid data.
   * @access public
   */
  public function import( $db_proxy_form_entry )
  {
    if( is_null( $db_proxy_form_entry ) || !$db_proxy_form_entry->id )
    {
      throw lib::create( 'exception\runtime',
        'Tried to import invalid proxy form entry.', __METHOD__ );
    }

    $database_class_name = lib::get_class_name( 'database\database' );
    $participant_class_name = lib::get_class_name( 'database\participant' );

    // link to the form
    $this->validated_proxy_form_entry_id = $db_proxy_form_entry->id;

    // import data to the participant table
    // TODO: implement

    // import data to the address table
    // TODO: implement

    // import data to the phone table
    // TODO: implement

    // save the new alternate record to the form
    $this->alternate_id = $db_alternate->id;
    $this->save();
  }
}
?>
