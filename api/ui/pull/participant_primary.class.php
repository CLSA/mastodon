<?php
/**
 * participant_primary.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\pull;
use mastodon\log, mastodon\util;
use mastodon\business as bus;
use mastodon\database as db;
use mastodon\exception as exc;

/**
 * pull: participant primary
 * 
 * @package mastodon\ui
 */
class participant_primary extends base_primary
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
    // if the uid is provided instead of the id  then fetch the participant id based on the uid
    if( isset( $args['uid'] ) )
    {
      $db_participant = db\participant::get_unique_record( 'uid', $args['uid'] );

      if( is_null( $db_participant ) )
        throw new exc\argument( 'uid', $args['uid'], __METHOD__ );
      $args['id'] = $db_participant->id;
    }

    parent::__construct( 'participant', $args );
  }

  /**
   * Overrides the parent class' base functionality by adding more data.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @return associative array
   * @access public
   */
  public function finish()
  {
    $data = parent::finish();

    // add the primary address
    $db_address = $this->get_record()->get_primary_address();
    if( !is_null( $db_address ) )
    {
      $data['street'] = is_null( $db_address->address2 )
                      ? $db_address->address1
                      : $db_address->address1.', '.$db_address->address2;
      $data['city'] = $db_address->city;
      $data['region'] = $db_address->get_region()->name;
      $data['postcode'] = $db_address->postcode;
    }
    
    // add the hin information
    $hin_info = $this->get_record()->get_hin_information();
    
    if( count( $hin_info ) )
    {
      $data['hin_access'] = $hin_info['access'];
      $data['hin_missing'] = !$hin_info['missing'];
    }
    else
    {
      $data['hin_access'] = false;
      $data['hin_missing'] = true;
    }

    return $data;
  }
}
?>
