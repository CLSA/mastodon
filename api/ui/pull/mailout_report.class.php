<?php
/**
 * mailout_report.class.php
 * 
 * @author Dean Inglis <inglisd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\pull;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Mailout required report data.
 * 
 * @abstract
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

  /**
   * Builds the report.
   * @author Dean Inglis <inglisd@mcmaster.ca>
   * @access protected
   */
  protected function build()
  {
    // get the report arguments
    $mailed_to = $this->get_argument( 'mailed_to' );
    $cohort = $this->get_argument( 'restrict_cohort' );
    $source_id = $this->get_argument( 'restrict_source_id' );
    $db_source = $source_id ? lib::create( 'database\source', $source_id ) : NULL;
    $mark_mailout = $this->get_argument( 'mark_mailout' );
    $participant_class_name = lib::get_class_name( 'database\participant' );

    if( is_null( $db_source ) )
    {
      $this->add_title( 
        sprintf( $mailed_to ?
                 'List of all unsynched %s participants who have been mailed to.' :
                 'List of all %s participants who require a package mailed out.',
                 $cohort ) );
    }
    else
    {
      $this->add_title( 
        sprintf( $mailed_to ?
                 'List of all unsynched %s participants whose source is %s who have been mailed to.' :
                 'List of all %s participants whose source is %s and require a package mailed out.',
                 $cohort,
                 $db_source->name ) );
    }
    
    // modifiers common to each iteration of the following loops
    $participant_mod = lib::create( 'database\modifier' );
    if( $mailed_to )
    {
      $participant_mod->order_desc( 'status.datetime' );
      $participant_mod->where( 'sync_datetime', '=', NULL );
    }
    $participant_mod->where( 'cohort', '=', $cohort );
    if( !is_null( $db_source ) ) $participant_mod->where( 'source_id', '=', $db_source->id );

    $contents = array();
    $participant_list =
      $participant_class_name::select_for_event( 'package mailed', $mailed_to, $participant_mod );
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

      $row = array(
        'fr' == $db_participant->language ? 'fr' : 'en', // english if not set
        $db_participant->uid,
        $db_participant->first_name,
        $db_participant->last_name,
        $address,
        $db_address->city,
        $db_region->name,
        $db_address->postcode,
        $age );
      
      if( $mailed_to )
      { // remove the age column and include the mailout date and site columns
        $status_mod = lib::create( 'database\modifier' );
        $status_mod->where( 'event', '=', 'package mailed' );
        $status_mod->order_desc( 'datetime' );
        $status_mod->limit( 1 );
        $status_list = $db_participant->get_status_list( $status_mod );
        $db_site = $db_participant->get_primary_site();
        $site_name = is_null( $db_site ) ? 'None' : $db_site->name;
        $db_status = current( $status_list );
        array_unshift( $row, $site_name );
        array_unshift( $row, strstr( $db_status->datetime, ' ', true ) );
        array_pop( $row );
      }

      $contents[] = $row;

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
    
    if( $mailed_to )
    { // include the mailout date and site columns
      array_unshift( $header, 'Site' );
      array_unshift( $header, 'Mailout Date' );
      array_pop( $header );
    }

    $this->add_table( NULL, $header, $contents, NULL );
  }
}
?>
