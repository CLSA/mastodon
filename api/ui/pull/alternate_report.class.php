<?php
/**
 * alternate_report.class.php
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
class alternate_report extends \cenozo\ui\pull\base_report
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
    parent::__construct( 'alternate', $args );
  }

  /**
   * TODO: implement and document
   */
  public function finish()
  {
    $participant_class_name = lib::get_class_name( 'database\participant' );

    $this->add_title( 
      'List of all participant who have an alternate contact with missing information.' );
    
    $contents = array();
    foreach( $participant_class_name::select() as $db_participant )
    {
    }
    
    $header = array();
    
    $this->add_table( NULL, $header, $contents, NULL );

    return parent::finish();
  }
}
?>
