<?php
/**
 * address_list.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget address list
 */
class address_list extends \cenozo\ui\widget\base_list
{
  /**
   * Constructor
   * 
   * Defines all variables required by the address list.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args An associative array of arguments to be processed by the widget
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'address', $args );
  }

  /**
   * Processes arguments, preparing them for the operation.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @throws exception\notice
   * @access protected
   */
  protected function prepare()
  {
    parent::prepare();
    
    $this->add_column( 'active', 'boolean', 'Active', true );
    $this->add_column( 'available', 'boolean', 'Available', false );
    $this->add_column( 'rank', 'number', 'Rank', true );
    $this->add_column( 'city', 'string', 'City', false );
  }
  
  /**
   * Finish setting the variables in a widget.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function setup()
  {
    parent::setup();
    
    foreach( $this->get_record_list() as $record )
    {
      // check if the address is available this month
      $month = strtolower( util::get_datetime_object( NULL, true )->format( 'F' ) );

      $db_region = $record->get_region();
      $this->add_row( $record->id,
        array( 'active' => $record->active,
               'available' => $record->$month,
               'rank' => $record->rank,
               'city' => $record->city.', '.$db_region->name ) );
    }
  }
}
?>
