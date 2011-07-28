<?php
/**
 * person.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\database
 * @filesource
 */

namespace mastodon\database;
use mastodon\log, mastodon\util;
use mastodon\business as bus;
use mastodon\exception as exc;

/**
 * A base class for all records which have a one-to-one relationship to `person`
 *
 * @package mastodon\database
 */
class person extends has_note
{
  /**
   * Override get_address_list()
   * 
   * Since addresses are related to the person table and not the participant or alternate
   * tables this method allows for direct access to the addresses.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param modifier $modifier A modifier to apply to the list
   * @return array( record )
   * @access public
   */
  public function get_address_list( $modifier = NULL )
  {
    return 'person' == $this->get_class_name()
         ? parent::get_address_list( $modifier )
         : $this->get_person()->get_address_list( $modifier );
  }
  
  /**
   * Override get_address_count()
   * 
   * Since addresses are related to the person table and not the participant or alternate
   * tables this method allows for direct access to the addresses.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param modifier $modifier A modifier to apply to the count
   * @return array( record )
   * @access public
   */
  public function get_address_count( $modifier = NULL )
  {
    return 'person' == $this->get_class_name()
         ? parent::get_address_count( $modifier )
         : $this->get_person()->get_address_count( $modifier );
  }
  
  /**
   * Override remove_address()
   * 
   * Since addresses are related to the person table and not the participant or alternate
   * tables this method allows for direct access to remove addresses.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param integer $id The id of the address to remove.
   * @access public
   */
  public function remove_address( $id )
  {
    if( 'person' == $this->get_class_name() ) parent::remove_address( $id );
    else $this->get_person()->remove_address( $id );
  }

  /**
   * Override get_phone_list()
   * 
   * Since phones are related to the person table and not the participant or alternate
   * tables this method allows for direct access to the phones.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param modifier $modifier A modifier to apply to the list
   * @return array( record )
   * @access public
   */
  public function get_phone_list( $modifier = NULL )
  {
    return 'person' == $this->get_class_name()
         ? parent::get_phone_list( $modifier )
         : $this->get_person()->get_phone_list( $modifier );
  }
  
  /**
   * Override get_phone_count()
   * 
   * Since phones are related to the person table and not the participant or alternate
   * tables this method allows for direct access to the phones.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param modifier $modifier A modifier to apply to the count
   * @return array( record )
   * @access public
   */
  public function get_phone_count( $modifier = NULL )
  {
    return 'person' == $this->get_class_name()
         ? parent::get_phone_count( $modifier )
         : $this->get_person()->get_phone_count( $modifier );
  }

  /**
   * Override remove_phone()
   * 
   * Since phones are related to the person table and not the participant or alternate
   * tables this method allows for direct access to remove phones.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param integer $id The id of the phone to remove.
   * @access public
   */
  public function remove_phone( $id )
  {
    if( 'person' == $this->get_class_name() ) parent::remove_phone( $id );
    else $this->get_person()->remove_phone( $id );
  }

  /**
   * Override parent method (since note are related to person)
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param modifier $modifier
   * @return int
   * @access public
   */
  public function get_note_count( $modifier = NULL )
  {
    $note_class_name = '\\mastodon\\database\\person_note';
    if ( is_null( $modifier ) ) $modifier = new modifier();
    $modifier->where( 'person_id', '=', $this->id );
    return $note_class_name::count( $modifier );
  }

  /**
   * Override parent method (since note are related to person)
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param modifier $modifier
   * @return array( record )
   * @access public
   */
  public function get_note_list( $modifier = NULL )
  {
    $note_class_name = '\\mastodon\\database\\person_note';
    if ( is_null( $modifier ) ) $modifier = new modifier();
    $modifier->where( 'person_id', '=', $this->id );
    $modifier->order( 'sticky', true );
    $modifier->order( 'datetime' );
    return $note_class_name::select( $modifier );
  }

  /**
   * Override parent method (since note are related to person)
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param user $user
   * @param string $note
   * @access public
   */
  public function add_note( $user, $note )
  {
    $date_obj = util::get_datetime_object();
    $note_class_name = '\\mastodon\\database\\person_note';
    $db_note = new $note_class_name();
    $db_note->user_id = $user->id;
    $db_note->person_id = $this->id;
    $db_note->datetime = $date_obj->format( 'Y-m-d H:i:s' );
    $db_note->note = $note;
    $db_note->save();
  }

  /**
   * Override parent method (since note are related to person)
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param integer $id
   * @return note record
   * @static
   * @access public
   */
  public static function get_note( $id = NULL )
  {
    return new person_note( $id );
  }
}
?>
