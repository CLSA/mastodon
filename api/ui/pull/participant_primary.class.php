<?php
/**
 * participant_primary.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\pull;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * pull: participant primary
 */
class participant_primary extends \cenozo\ui\pull\base_primary
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
   * Processes arguments, preparing them for the operation.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function prepare()
  {
    // if the uid is provided instead of the id  then fetch the participant id based on the uid
    // NOTE: this must be done before calling the parent prepare() method
    if( isset( $this->arguments['uid'] ) )
    {
      $class_name = lib::get_class_name( 'database\participant' );
      $db_participant = $class_name::get_unique_record( 'uid', $this->arguments['uid'] );

      if( is_null( $db_participant ) )
        throw lib::create( 'exception\argument', 'uid', $this->arguments['uid'], __METHOD__ );

      // make sure not to mix up comprehensive and tracking participants
      if( $db_participant->cohort != lib::create( 'business\session' )->get_site()->cohort )
        throw lib::create( 'exception\runtime',
          'Tried to get participant from wrong cohort.', __METHOD__ );

      $this->arguments['id'] = $db_participant->id;
    }

    parent::prepare();
  }

  /**
   * This method executes the operation's purpose.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function execute()
  {
    parent::execute();

    $db_participant = $this->get_record();

    // restrict by cohort, if asked to
    $cohort = $this->get_argument( 'cohort', false );
    if( $cohort && $cohort != $db_participant->cohort )
      throw lib::create( 'exception\argument', 'uid', $args['uid'], __METHOD__ );

    // convert source_id to source (name)
    $this->data['source_name'] = is_null( $this->data['source_id'] )
                         ? NULL
                         : $db_participant->get_source()->name;

    // convert site_id to site (name)
    $this->data['site_name'] = is_null( $this->data['site_id'] )
                         ? NULL
                         : $db_participant->get_site()->name;

    // add full participant information if requested
    if( $this->get_argument( 'full', false ) )
    {
      // add the participant's address list
      $this->data['address_list'] = array();
      foreach( $db_participant->get_address_list() as $db_address )
      {
        $item = array();
        foreach( $db_address->get_column_names() as $column )
        {
          if( 'person_id' == $column ) {} // do nothing
          else if( 'region_id' == $column )
            $item['region_abbreviation'] = $db_address->get_region()->abbreviation;
          else $item[$column] = $db_address->$column;
        }
        $this->data['address_list'][] = $item;
      }

      // add the participant's phone list
      $this->data['phone_list'] = array();
      foreach( $db_participant->get_phone_list() as $db_phone )
      {
        $item = array();
        foreach( $db_phone->get_column_names() as $column )
        {
          if( 'person_id' == $column ) {} // do nothing
          else if( 'address_id' == $column && !is_null( $db_phone->address_id ) )
            $item['address_rank'] = $db_phone->get_address()->rank;
          else $item[$column] = $db_phone->$column;
        }
        $this->data['phone_list'][] = $item;
      }

      // add the participant's consent list
      $this->data['consent_list'] = array();
      foreach( $db_participant->get_consent_list() as $db_consent )
      {
        $item = array();
        foreach( $db_consent->get_column_names() as $column )
        {
          if( 'participant_id' == $column ) {} // do nothing
          else $item[$column] = $db_consent->$column;
        }
        $this->data['consent_list'][] = $item;
      }
    }
    else
    {
      // add the primary address
      $db_address = $db_participant->get_primary_address();
      if( !is_null( $db_address ) )
      {
        $this->data['street'] = is_null( $db_address->address2 )
                        ? $db_address->address1
                        : $db_address->address1.', '.$db_address->address2;
        $this->data['city'] = $db_address->city;
        $this->data['region'] = $db_address->get_region()->name;
        $this->data['postcode'] = $db_address->postcode;
      }
      
      // add the hin information
      $hin_info = $db_participant->get_hin_information();
      
      if( count( $hin_info ) )
      {
        $this->data['hin_access'] = $hin_info['access'] ? 1 : 0;
        $this->data['hin_future_access'] = $hin_info['future_access'] ? 1 : 0;
        $this->data['hin_missing'] = $hin_info['missing'];
      }
      else
      {
        $this->data['hin_access'] = -1; // -1 means there is no access information
        $this->data['hin_future_access'] = -1; // -1 means there is no future access information
        $this->data['hin_missing'] = true;
      }
    }
  }
}
?>
