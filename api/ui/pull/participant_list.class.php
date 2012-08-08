<?php
/**
 * participant_list.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\pull;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Class for participant list pull operations.
 * 
 * @abstract
 */
class participant_list extends \cenozo\ui\pull\base_list
{
  /**
   * Constructor
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args Pull arguments.
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'participant', $args );
  }

  /**
   * Overrides the parent method to add participant address, phone and consent details.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param database\record $record
   * @return array
   * @access public
   */
  public function process_record( $record )
  {
    $source_class_name = lib::get_class_name( 'database\source' );
    $site_class_name = lib::get_class_name( 'database\site' );

    $item = parent::process_record( $record );

    // convert primary ids to unique
    $item['source_id'] = $source_class_name::get_unique_from_primary_key( $item['source_id'] );
    $item['site_id'] = $site_class_name::get_unique_from_primary_key( $item['site_id'] );

    // add full participant information if requested
    if( $this->get_argument( 'full', false ) )
    {
      $item['address_list'] = $this->prepare_list( $record->get_address_list() );
      $item['phone_list'] = $this->prepare_list( $record->get_phone_list() );
      $item['consent_list'] = $this->prepare_list( $record->get_consent_list() );
      $item['availability_list'] = $this->prepare_list( $record->get_availability_list() );
      $item['note_list'] = $this->prepare_list( $record->get_note_list() );
    }
    else
    {
      // add the primary address
      $db_address = $record->get_primary_address();
      if( !is_null( $db_address ) )
      {
        $item['street'] = is_null( $db_address->address2 )
                        ? $db_address->address1
                        : $db_address->address1.', '.$db_address->address2;
        $item['city'] = $db_address->city;
        $item['region'] = $db_address->get_region()->name;
        $item['postcode'] = $db_address->postcode;
      }
      
      // add the hin information
      $hin_info = $record->get_hin_information();
      
      if( count( $hin_info ) )
      {
        $item['hin_access'] = $hin_info['access'] ? 1 : 0;
        $item['hin_future_access'] = $hin_info['future_access'] ? 1 : 0;
        $item['hin_missing'] = $hin_info['missing'];
      }
      else
      {
        $item['hin_access'] = -1; // -1 means there is no access information
        $item['hin_future_access'] = -1; // -1 means there is no future access information
        $item['hin_missing'] = true;
      }
    }

    return $item;
  }

  /**
   * Converts a list of records into an array which can be transmitted without primary IDs
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array( record ) $record_list
   * @return array( array )
   * @access protected
   */
  protected function prepare_list( $record_list )
  {
    $participant_class_name = lib::get_class_name( 'database\participant' );

    $prepared_list = array();
    foreach( $record_list as $record )
    {
      $data = array();
      foreach( $record->get_column_names() as $column_name )
      {
        // ignore id, person_id and participant_id columns
        if( 'id' != $column_name &&
            'person_id' != $column_name &&
            'participant_id' != $column_name )
        {
          if( '_id' == substr( $column_name, -3 ) && !is_null( $record->$column_name ) )
          {
            $subject = substr( $column_name, 0, -3 );
            $class_name = lib::get_class_name( 'database\\'.$subject );
            $key = $class_name::get_unique_from_primary_key( $record->$column_name );

            // convert person keys to participant keys
            if( is_array( $key ) && array_key_exists( 'person_id', $key ) )
            {
              // replace person key with participant key
              $participant_id = $record->get_person()->get_participant()->id;
              unset( $key['person_id'] );
              $key['participant_id'] =
                $participant_class_name::get_unique_from_primary_key( $participant_id );
            }

            $data[$column_name] = $key;
          }
          else $data[$column_name] = $record->$column_name;
        }
      }
      $prepared_list[] = $data;
    }

    return $prepared_list;
  }
}
?>
