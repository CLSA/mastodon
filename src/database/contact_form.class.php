<?php
/**
 * contact_form.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\database;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * contact_form: record
 */
class contact_form extends base_form
{
  /**
   * Implements the parent's abstract import method.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param database\form_entry $db_base_form_entry The entry to be used as the valid data.
   * @access public
   */
  public function import( $db_contact_form_entry )
  {
    if( is_null( $db_contact_form_entry ) || !$db_contact_form_entry->id )
    {
      throw lib::create( 'exception\runtime',
        'Tried to import invalid contact form entry.', __METHOD__ );
    }

    $event_type_class_name = lib::get_class_name( 'database\event_type' );
    $database_class_name = lib::get_class_name( 'database\database' );
    $participant_class_name = lib::get_class_name( 'database\participant' );
    $hin_class_name = lib::get_class_name( 'database\hin' );

    $db_participant =
      $participant_class_name::get_unique_record( 'uid', $db_contact_form_entry->uid );

    // link to the form
    $this->validated_contact_form_entry_id = $db_contact_form_entry->id;

    // add the contact signed event to the participant
    $db_event_type =
      $event_type_class_name::get_unique_record( 'name', 'contact signed' );
    if( !is_null( $db_event_type ) )
    {
      $db_event = lib::create( 'database\event' );
      $db_event->participant_id = $db_participant->id;
      $db_event->event_type_id = $db_event_type->id;
      $db_event->datetime = !is_null( $db_contact_form_entry->date )
                          ? $db_contact_form_entry->date
                          : util::get_datetime_object();
      $db_event->save();
    }

    // import the data to the contact table
    $accept = $db_contact_form_entry->option_1;
    $date = !is_null( $db_contact_form_entry->date )
          ? $db_contact_form_entry->date
          : util::get_datetime_object();

    // look for duplicates
    $db_contact = NULL;
    $contact_mod = lib::create( 'database\modifier' );
    $contact_mod->where( 'accept', '=', $accept );
    $contact_mod->where( 'written', '=', true );
    $contact_mod->where( 'date', '=', $date );
    $contact_list = $db_participant->get_contact_list( $contact_mod );
    if( 0 < count( $contact_list ) )
    { // found a duplicate, link the form to it
      $db_contact = current( $contact_list );
    }
    else
    { // no duplicate, create a new contact record
      $db_contact = lib::create( 'database\contact' );
      $db_contact->participant_id = $db_participant->id;
      $db_contact->accept = $accept;
      $db_contact->written = true;
      $db_contact->date = $date;
      $db_contact->note = 'Imported by data entry system.';
      $db_contact->save();

      // now find that new contact so we can link to its ID
      $contact_mod = lib::create( 'database\modifier' );
      $contact_mod->where( 'accept', '=', $accept );
      $contact_mod->where( 'written', '=', true );
      $contact_mod->where( 'date', '=', $date );
      $contact_list = $db_participant->get_contact_list( $contact_mod );
      if( 0 < count( $contact_list ) )
      {
        $db_contact = current( $contact_list );
      }
      else
      {
        log::warning( 'Consent entry not found after importing contact form.' );
      }
    }

    // import the data to the hin table
    $db_hin = $hin_class_name::get_unique_record( 'participant_id', $db_participant->id );
    if( is_null( $db_hin ) )
    {
      $db_hin = lib::create( 'database\hin' );
      $db_hin->participant_id = $db_participant->id;
    }
    $db_hin->access = $db_contact_form_entry->option_2;
    $db_hin->save();

    // import the form into the framework's form system
    $form_type_class_name = lib::get_class_name( 'database\form_type' );
    $db_form_type = $form_type_class_name::get_unique_record( 'name', 'contact' );

    $db_form = lib::create( 'database\form' );
    $db_form->participant_id = $db_participant->id;
    $db_form->form_type_id = $db_form_type->id;
    $db_form->date = $date;
    $db_form->record_id = $db_participant->id;
    $db_form->save();

    // TODO: need to move form's file from mastodon to cenozo

    // save the new contact record to the form
    $this->form_id = $db_form->id;
    $this->completed = true;
    if( !is_null( $db_contact ) ) $this->contact_id = $db_contact->id;
    $this->save();
  }
}
