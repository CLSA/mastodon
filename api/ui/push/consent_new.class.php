<?php
/**
 * consent_new.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\push;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * push: consent new
 *
 * Create a new consent.
 * @package mastodon\ui
 */
class consent_new extends \cenozo\ui\push\base_new
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

    parent::__construct( 'consent', $args );
  }

  /**
   * Executes the push.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function finish()
  {
    // make sure the date column isn't blank
    $columns = $this->get_argument( 'columns' );
    if( !array_key_exists( 'date', $columns ) || 0 == strlen( $columns['date'] ) )
      throw lib::create( 'exception\notice', 'The date cannot be left blank.', __METHOD__ );
    parent::finish();

    // if a form variable was included try to decode it and store it as a consent form
    $form = $this->get_argument( 'form', NULL );
    if( !is_null( $form ) )
    {
      $form_decoded = base64_decode( chunk_split( $form ) );
      if( false == $form_decoded )
        throw lib::create( 'exception\runtime', 'Unable to decode form argument.', __METHOD__ );

      // create a new consent form
      $db_consent_form = lib::create( 'database\consent_form' );
      $db_consent_form->consent_id = $this->get_record()->id;
      $db_consent_form->date = util::get_datetime_object()->format( 'Y-m-d' );
      $db_consent_form->scan = $form_decoded;
      $db_consent_form->complete = true;
      $db_consent_form->save();
    }
  }
}
?>
