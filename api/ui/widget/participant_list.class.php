<?php
/**
 * participant_list.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\ui
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget participant list
 * 
 * @package mastodon\ui
 */
class participant_list extends \cenozo\ui\widget\base_list
{
  /**
   * Constructor
   * 
   * Defines all variables required by the participant list.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args An associative array of arguments to be processed by the widget
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'participant', $args );
    
    $this->add_column( 'uid', 'string', 'Unique ID', true );
    $this->add_column( 'first_name', 'string', 'First Name', true );
    $this->add_column( 'last_name', 'string', 'Last Name', true );
    $this->add_column( 'active', 'boolean', 'Active', true );
    $this->add_column( 'source.name', 'string', 'Source', true );
    $this->add_column( 'cohort', 'string', 'Cohort', true );
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
      $db_source = $record->get_source();
      $source_name = is_null( $db_source ) ? '(none)' : $db_source->name;
      $this->add_row( $record->id,
        array( 'uid' => $record->uid ? $record->uid : '(none)',
               'first_name' => $record->first_name,
               'last_name' => $record->last_name,
               'active' => $record->active,
               'source.name' => $source_name,
               'cohort' => $record->cohort,
               // note count isn't a column, it's used for the note button
               'note_count' => $record->get_note_count() ) );
    }

    $this->finish_setting_rows();
  }
}
?>
