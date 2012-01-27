<?php
/**
 * mailout_report.class.php
 * 
 * @author Dean Inglis <inglisd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\pull;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Mailout required report data.
 * 
 * @abstract
 * @package mastodon\ui
 */
class mailout_report extends \cenozo\ui\pull\base_report
{
  /**
   * Constructor
   * 
   * @author Dean Inglis <inglisd@mcmaster.ca>
   * @param array $args Pull arguments.
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'mailout', $args );
  }

  public function finish()
  {
    // get the report arguments
    $cohort = $this->get_argument( 'restrict_cohort' );
    $db_source = lib::create( 'database\source', $this->get_argument( 'restrict_source_id' ) );
    $mark_mailout = $this->get_argument( 'mark_mailout' );
    $participant_class_name = lib::get_class_name( 'database\participant' );

    $this->add_title( 
      sprintf( 'List of all %s participant whose source is %s and require a package mailed out.',
               $cohort,
               $db_source->name ) );
    
    // modifiers common to each iteration of the following loops
    $participant_mod = lib::create( 'database\modifier' );
    $participant_mod->where( 'cohort', '=', $cohort );
    $participant_mod->where( 'source_id', '=', $db_source->id );

    $contents = array();
    $participant_list =
      $participant_class_name::select_for_event( 'package mailed', true, $participant_mod );
    foreach( $participant_list as $db_participant )
    {
      $db_address = $db_participant->get_first_address();
      if( is_null( $db_address ) ) continue;
      $db_region = $db_address->get_region();

      $address = $db_address->address1;
      if( !is_null( $db_address->address2 ) ) $address .= ' '.$db_address->address2;

      $age = '';
      if( !is_null( $db_participant->date_of_birth ) )
      {
        $dob_datetime_obj = util::get_datetime_object( $db_participant->date_of_birth );
        $age = util::get_interval( $dob_datetime_obj )->y;
      }

      $contents[] = array(
        'fr' == $db_participant->language ? 'fr' : 'en', // english if not set
        $db_participant->uid,
        $db_participant->first_name,
        $db_participant->last_name,
        $address,
        $db_address->city,
        $db_region->name,
        $db_address->postcode,
        $age );

      // add packaged mailed status if requested to
      if( $mark_mailout )
      {
        $db_status = lib::create( 'database\status' );
        $db_status->participant_id = $db_participant->id;
        $db_status->datetime = util::get_datetime_object()->format( 'Y-m-d H:i:s' );
        $db_status->event = 'package mailed';
        $db_status->save();
      }
    }
    
    $header = array(
      'Language',
      'CLSA ID',
      'First Name',
      'Last Name',
      'Address',
      'City',
      'Province',
      'Postal Code',
      'Age' );
    
    $this->add_table( NULL, $header, $contents, NULL );

    return parent::finish();
  }
}
?>
