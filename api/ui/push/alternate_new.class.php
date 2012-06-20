<?php
/**
 * alternate_new.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: alternate new
 *
 * Create a new alternate.
 * @package mastodon\ui
 */
class alternate_new extends \cenozo\ui\push\base_new
{
  /**
   * Constructor.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Push arguments
   * @access public
   */
  public function __construct( $args )
  {
    if( array_key_exists( 'noid', $args ) )
    {
      // use the noid argument and remove it from the args input
      $noid = $args['noid'];
      unset( $args['noid'] );

      // make sure there is sufficient information
      if( !is_array( $noid ) ||
          !array_key_exists( 'participant.uid', $noid ) )
        throw lib::create( 'exception\argument', 'noid', $noid, __METHOD__ );
      
      $class_name = lib::get_class_name( 'database\participant' );
      $db_participant = $class_name::get_unique_record( 'uid', $noid['participant.uid'] );
      if( !$db_participant ) throw lib::create( 'exception\argument', 'noid', $noid, __METHOD__ );
      $args['columns']['participant_id'] = $db_participant->id;
    }

    parent::__construct( 'alternate', $args );
  }

  /**
   * Executes the push.
   * Since creating a new alternate requires first creating a new person this method overrides
   * its parent method without calling (which is the usual behaviour).
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function finish()
  {
    $alternate_class_name = lib::get_class_name( 'database\alternate' );

    // make sure the name and association columns aren't blank
    $columns = $this->get_argument( 'columns' );
    if( !array_key_exists( 'first_name', $columns ) || 0 == strlen( $columns['first_name'] ) )
      $columns['first_name'] = 'unknown';
    if( !array_key_exists( 'last_name', $columns ) || 0 == strlen( $columns['last_name'] ) )
      $columns['last_name'] = 'unknown';
    if( !array_key_exists( 'association', $columns ) || 0 == strlen( $columns['association'] ) )
      $columns['association'] = 'unknown';
    
    // special case: replace alternate's with the same first/last name
    $alternate_mod = lib::create( 'database\modifier' );
    $alternate_mod->where( 'participant_id', '=', $columns['participant_id'] );
    $alternate_mod->where( 'first_name', '=', $columns['first_name'] );
    $alternate_mod->where( 'last_name', '=', $columns['last_name'] );
    $alternate_list = $alternate_class_name::select( $alternate_mod );
    if( 0 < count( $alternate_list ) ) $this->set_record( current( $alternate_list ) );

    $db_alternate = $this->get_record();
    foreach( $columns as $column => $value ) $db_alternate->$column = $value;

    try
    {
      // create a person record if the alternate doesn't already have one
      if( is_null( $db_alternate->person_id ) )
      {
        $db_person = lib::create( 'database\person' );
        $db_person->save();
        $db_alternate->person_id = $db_person->id;
      }
      $db_alternate->save();
    }
    catch( \cenozo\exception\base_exception $e )
    {
      // failed to create alternate, delete the person record
      if( !is_null( $db_person->id ) ) $db_person->delete();

      if( 'database' == $e->get_type() )
      {
        if( $e->is_duplicate_entry() )
        {
          throw lib::create( 'exception\notice',
            'Unable to create the new '.$this->get_subject().' because it is not unique.',
            __METHOD__, $e );
        }
        else if( $e->is_missing_data() )
        {
          $matches = array();
          $found = preg_match( "/Column '[^']+'/", $e->get_raw_message(), $matches );
  
          if( $found )
          {
            $message = sprintf(
              'You must specify "%s" in order to create a new %s.',
              substr( $matches[0], 8, -1 ),
              $this->get_subject() );
          }
          else
          {
            $message = sprintf(
              'Unable to create the new %s, not all mandatory fields have been filled out.',
              $this->get_subect() );
          }
  
          throw lib::create( 'exception\notice', $message, __METHOD__, $e );
        }

        throw $e;
      }
    }

    // if address argument exists then add an address to the alternate
    $address_info = $this->get_argument( 'address', array() );
    if( is_array( $address_info ) && 0 < count( $address_info ) )
    {
      $args = array( 'columns' => $address_info );
      $args['columns']['person_id'] = $db_alternate->person_id;
      $operation = lib::create( 'ui\push\address_new', $args );
      $operation->finish();
    }

    // if phone argument exists then add a phone number to the alternate
    $phone_info = $this->get_argument( 'phone', array() );
    if( is_array( $phone_info ) && 0 < count( $phone_info ) )
    {
      $args = array( 'columns' => $phone_info );
      $args['columns']['person_id'] = $db_alternate->person_id;
      $operation = lib::create( 'ui\push\phone_new', $args );
      $operation->finish();
    }

    // if a form variable was included try to decode it and store it as a proxy form
    $form = $this->get_argument( 'form', NULL );
    if( !is_null( $form ) && ( $db_alternate->proxy || $db_alternate->informant ) )
    {
      $form_decoded = base64_decode( chunk_split( $form ) );
      if( false == $form_decoded )
        throw lib::create( 'exception\runtime', 'Unable to decode form argument.', __METHOD__ );

      // create a new proxy form
      $db_proxy_form = lib::create( 'database\proxy_form' );
      if( $db_alternate->proxy ) $db_proxy_form->proxy_alternate_id = $db_alternate->id;
      if( $db_alternate->informant ) $db_proxy_form->informant_alternate_id = $db_alternate->id;
      $db_proxy_form->date = util::get_datetime_object()->format( 'Y-m-d' );
      $db_proxy_form->scan = $form_decoded;
      $db_proxy_form->complete = true;
      $db_proxy_form->save();
    }
  }
}
?>
