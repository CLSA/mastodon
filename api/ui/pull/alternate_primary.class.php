<?php
/**
 * alternate_primary.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\pull;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * pull: alternate primary
 */
class alternate_primary extends \cenozo\ui\pull\base_primary
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
    parent::__construct( 'alternate', $args );
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

    // add the primary address
    $db_address = $this->get_record()->get_primary_address();
    if( !is_null( $db_address ) )
    {
      $this->data['street'] = is_null( $db_address->address2 )
                            ? $db_address->address1
                            : $db_address->address1.', '.$db_address->address2;
      $this->data['city'] = $db_address->city;
      $this->data['region'] = $db_address->get_region()->name;
      $this->data['postcode'] = $db_address->postcode;
    }
  }
}
?>
