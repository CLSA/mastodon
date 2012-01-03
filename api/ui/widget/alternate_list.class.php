<?php
/**
 * alternate_list.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget alternate list
 * 
 * @package mastodon\ui
 */
class alternate_list extends site_restricted_list
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
    
    $this->add_column( 'first_name', 'string', 'First Name', true );
    $this->add_column( 'last_name', 'string', 'Last Name', true );
    $this->add_column( 'alternate', 'boolean', 'Alternate', true );
    $this->add_column( 'informant', 'boolean', 'Informant', true );
    $this->add_column( 'proxy', 'boolean', 'Proxy', true );
  }
  
  /**
   * Set the rows array needed by the template.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function finish()
  {
    parent::finish();
    
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

    $this->finish_setting_rows();
  }
}
?>
