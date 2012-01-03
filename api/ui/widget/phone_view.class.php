<?php
/**
 * phone_view.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget phone view
 * 
 * @package mastodon\ui
 */
class phone_view extends \cenozo\ui\widget\base_view
{
  /**
   * Constructor
   * 
   * Defines all variables which need to be set for the associated template.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args An associative array of arguments to be processed by the widget
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'phone', 'view', $args );
    
    // add items to the view
    $this->add_item( 'address_id', 'enum', 'Associated address' );
    $this->add_item( 'active', 'boolean', 'Active' );
    $this->add_item( 'rank', 'enum', 'Rank' );
    $this->add_item( 'type', 'enum', 'Type' );
    $this->add_item( 'number', 'string', 'Phone' );
    $this->add_item( 'note', 'text', 'Note' );
  }

  /**
   * Finish setting the variables in a widget.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function finish()
  {
    parent::finish();
    
    $db_person = $this->get_record()->get_person();

    // create enum arrays
    $modifier = lib::create( 'database\modifier' );
    $modifier->where( 'person_id', '=', $db_person->id );
    $modifier->order( 'rank' );
    $addresses = array();
    foreach( db\address::select( $modifier ) as $db_address )
    {
      $db_region = $db_address->get_region();
      $addresses[$db_address->id] = sprintf( '%d. %s, %s, %s',
        $db_address->rank,
        $db_address->city,
        $db_region->name,
        $db_region->country );
    }

    $num_phones = $db_person->get_phone_count();
    $ranks = array();
    for( $rank = 1; $rank <= $num_phones; $rank++ ) $ranks[] = $rank;
    $ranks = array_combine( $ranks, $ranks );
    $types = db\phone::get_enum_values( 'type' );
    $types = array_combine( $types, $types );

    // set the view's items
    $this->set_item( 'address_id', $this->get_record()->address_id, false, $addresses );
    $this->set_item( 'active', $this->get_record()->active, true );
    $this->set_item( 'rank', $this->get_record()->rank, true, $ranks );
    $this->set_item( 'type', $this->get_record()->type, true, $types );
    $this->set_item( 'number', $this->get_record()->number );
    $this->set_item( 'note', $this->get_record()->note );

    $this->finish_setting_items();
  }
}
?>
