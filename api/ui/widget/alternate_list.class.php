<?php
/**
 * alternate_list.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget alternate list
 */
class alternate_list extends \cenozo\ui\widget\base_list
{
  /**
   * Constructor
   * 
   * Defines all variables required by the alternate list.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args An associative array of arguments to be processed by the widget
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'alternate', $args );
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
    
    $this->add_column( 'first_name', 'string', 'First', true );
    $this->add_column( 'last_name', 'string', 'Last', true );
    $this->add_column( 'alternate', 'boolean', 'Alternate', true );
    $this->add_column( 'informant', 'boolean', 'Informant', true );
    $this->add_column( 'proxy', 'boolean', 'Proxy', true );
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
      $this->add_row( $record->id,
        array( 'first_name' => $record->first_name,
               'last_name' => $record->last_name,
               'alternate' => $record->alternate,
               'informant' => $record->informant,
               'proxy' => $record->proxy,
               // note count isn't a column, it's used for the note button
               'note_count' => $record->get_note_count() ) );
    }
  }
}
?>
