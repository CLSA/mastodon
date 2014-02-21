<?php
/**
 * participant_delink.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: participant delink
 *
 * Edit a participant.
 */
class participant_delink extends \cenozo\ui\push\participant_delink
{
  /**
   * This method executes the operation's purpose.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function execute()
  {
    parent::execute();

    $participant_class_name = lib::get_class_name( 'database\participant' );
    $address_class_name = lib::get_class_name( 'database\address' );
    $util_class_name = lib::get_class_name( 'util' );

    // re-associate the import entry with the new participant
    foreach( $this->get_record()->get_import_entry_list() as $db_import_entry )
    {
      $db_import_entry->participant_id = $this->db_new_participant->id;
      $db_import_entry->save();
    }

    // re-associate the contact form with the new participant
    foreach( $this->get_record()->get_contact_form_list() as $db_contact_form )
    {
      $db_contact_form->participant_id = $this->db_new_participant->id;
      $db_contact_form->save();
    }
    
    // remove the association between alternate and proxy forms
    foreach( $this->get_record()->get_alternate_list() as $db_alternate )
    {
      $db_proxy_form = $db_alternate->get_proxy_form();
      if( !is_null( $db_proxy_form ) )
      {
        $db_proxy_form->proxy_alternate_id = NULL;
        $db_proxy_form->informant_alternate_id = NULL;
        $db_proxy_form->save();
      }
    }
  }
}
