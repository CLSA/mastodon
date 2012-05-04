<?php
/**
 * participant_list.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\pull;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Class for participant list pull operations.
 * 
 * @abstract
 * @package mastodon\ui
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
   * @access public
   */
  public function process_record( $record )
  {
    $item = parent::process_record( $record );

    // convert source_id to source (name)
    $item['source_name'] = is_null( $record->source_id )
                         ? NULL
                         : $record->get_source()->name;

    // convert site_id to site (name)
    $item['site_name'] = is_null( $item['site_id'] )
                         ? NULL
                         : $record->get_site()->name;

    // add full participant information if requested
    if( $this->get_argument( 'full', false ) )
    {
      // add the participant's address list
      $item['address_list'] = array();
      foreach( $record->get_address_list() as $db_address )
      {
        $address = array();
        foreach( $db_address->get_column_names() as $column )
        {
          if( 'person_id' == $column ) {} // do nothing
          else if( 'region_id' == $column )
            $address['region_abbreviation'] = $db_address->get_region()->abbreviation;
          else $address[$column] = $db_address->$column;
        }
        $item['address_list'][] = $address;
      }

      // add the participant's phone list
      $item['phone_list'] = array();
      foreach( $record->get_phone_list() as $db_phone )
      {
        $phone = array();
        foreach( $db_phone->get_column_names() as $column )
        {
          if( 'person_id' == $column ) {} // do nothing
          else if( 'address_id' == $column && !is_null( $db_phone->address_id ) )
            $phone['address_rank'] = $db_phone->get_address()->rank;
          else $phone[$column] = $db_phone->$column;
        }
        $item['phone_list'][] = $phone;
      }

      // add the participant's consent list
      $item['consent_list'] = array();
      foreach( $record->get_consent_list() as $db_consent )
      {
        $consent = array();
        foreach( $db_consent->get_column_names() as $column )
        {
          if( 'participant_id' == $column ) {} // do nothing
          else $consent[$column] = $db_consent->$column;
        }
        $item['consent_list'][] = $consent;
      }

      // add the participant's availability list
      $item['availability_list'] = array();
      foreach( $record->get_availability_list() as $db_availability )
      {
        $availability = array();
        foreach( $db_availability->get_column_names() as $column )
        {
          if( 'participant_id' == $column ) {} // do nothing
          else $availability[$column] = $db_availability->$column;
        }
        $item['availability_list'][] = $availability;
      }
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
}
?>
