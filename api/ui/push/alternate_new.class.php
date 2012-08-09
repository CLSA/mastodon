<?php
/**
 * alternate_new.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: alternate new
 *
 * Create a new alternate.
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
    parent::__construct( 'alternate', $args );
  }

  /**
   * Processes arguments, preparing them for the operation.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function prepare()
  {
    parent::prepare();

    // make sure the name and association columns aren't blank
    $columns = $this->get_argument( 'columns' );
    if( !array_key_exists( 'first_name', $columns ) || 0 == strlen( $columns['first_name'] ) )
      $this->arguments['columns']['first_name'] = 'unknown';
    if( !array_key_exists( 'last_name', $columns ) || 0 == strlen( $columns['last_name'] ) )
      $this->arguments['columns']['last_name'] = 'unknown';
    if( !array_key_exists( 'association', $columns ) || 0 == strlen( $columns['association'] ) )
      $this->arguments['columns']['association'] = 'unknown';
  }

  /**
   * Sets up the operation with any pre-execution instructions that may be necessary.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function setup()
  {
    parent::setup();

    $alternate_class_name = lib::get_class_name( 'database\alternate' );

    $columns = $this->get_argument( 'columns' );

    // special case: replace alternate's with the same first/last name
    $alternate_mod = lib::create( 'database\modifier' );
    $alternate_mod->where( 'participant_id', '=', $columns['participant_id'] );
    $alternate_mod->where( 'first_name', '=', $columns['first_name'] );
    $alternate_mod->where( 'last_name', '=', $columns['last_name'] );
    $alternate_list = $alternate_class_name::select( $alternate_mod );
    if( 0 < count( $alternate_list ) ) $this->set_record( current( $alternate_list ) );

    // create a person record if the alternate doesn't already have one
    if( is_null( $this->get_record()->person_id ) )
    {
      $db_person = lib::create( 'database\person' );
      $db_person->save();
      $this->get_record()->person_id = $db_person->id;
    }
  }

  /**
   * Finishes the operation with any post-execution instructions that may be necessary.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function finish()
  {
    parent::finish();

    $record = $this->get_record();

    // if address argument exists then add an address to the alternate
    $address_info = $this->get_argument( 'address', array() );
    if( is_array( $address_info ) && 0 < count( $address_info ) )
    {
      $args = array( 'columns' => $address_info );
      $args['columns']['person_id'] = $record->person_id;
      $operation = lib::create( 'ui\push\address_new', $args );
      $operation->process();
    }

    // if phone argument exists then add a phone number to the alternate
    $phone_info = $this->get_argument( 'phone', array() );
    if( is_array( $phone_info ) && 0 < count( $phone_info ) )
    {
      $args = array( 'columns' => $phone_info );
      $args['columns']['person_id'] = $record->person_id;
      $operation = lib::create( 'ui\push\phone_new', $args );
      $operation->process();
    }

    // if a form variable was included try to decode it and store it as a proxy form
    $form = $this->get_argument( 'form', NULL );
    if( !is_null( $form ) && ( $record->proxy || $record->informant ) )
    {
      $form_decoded = base64_decode( chunk_split( $form ) );
      if( false == $form_decoded )
        throw lib::create( 'exception\runtime', 'Unable to decode form argument.', __METHOD__ );

      // create a new proxy form
      $db_proxy_form = lib::create( 'database\proxy_form' );
      if( $record->proxy ) $db_proxy_form->proxy_alternate_id = $record->id;
      if( $record->informant ) $db_proxy_form->informant_alternate_id = $record->id;
      $db_proxy_form->date = util::get_datetime_object()->format( 'Y-m-d' );
      $db_proxy_form->scan = $form_decoded;
      $db_proxy_form->complete = true;
      $db_proxy_form->save();
    }
  }
}
?>
