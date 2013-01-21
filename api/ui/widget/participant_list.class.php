<?php
/**
 * participant_list.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget participant list
 */
class participant_list extends site_restricted_list
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
    
    $this->add_column( 'uid', 'string', 'UID', true );
    $this->add_column( 'first_name', 'string', 'First', true );
    $this->add_column( 'last_name', 'string', 'Last', true );
    $this->add_column( 'active', 'boolean', 'Active', true );
    $this->add_column( 'source.name', 'string', 'Source', true );
    if( is_null( $this->db_restrict_site ) )
      $this->add_column( 'cohort.name', 'string', 'Cohort', true );
    $this->add_column( 'site', 'string', 'Site', false );

    $this->extended_site_selection = true;

    $restrict_condition = $this->get_argument( 'restrict_condition', '' );
    if( $restrict_condition )
      $this->set_heading(
        sprintf( '%s, restricted to %s',
                 $this->get_heading(),
                 $restrict_condition ) );
  }
  
  /**
   * Set the rows array needed by the template.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function setup()
  {
    parent::setup();

    $participant_class_name = lib::get_class_name( 'database\participant' );
    $operation_class_name = lib::get_class_name( 'database\operation' );
    
    foreach( $this->get_record_list() as $record )
    {
      $db_source = $record->get_source();
      $source_name = is_null( $db_source ) ? '(none)' : $db_source->name;
      $db_site = $record->get_primary_site();
      $site_name = is_null( $db_site ) ? 'none' : $db_site->name;
      $columns = array(
        'uid' => $record->uid ? $record->uid : '(none)',
        'first_name' => $record->first_name,
        'last_name' => $record->last_name,
        'active' => $record->active,
        'source.name' => $source_name,
        'site' => $site_name,
        // note count isn't a column, it's used for the note button
        'note_count' => $record->get_note_count() );
      if( is_null( $this->db_restrict_site ) )
        $columns['cohort.name'] = $record->get_cohort()->name;
      $this->add_row( $record->id, $columns );
    }

    $this->set_variable( 'conditions', $participant_class_name::get_enum_values( 'status' ) );
    $this->set_variable( 'restrict_condition', $this->get_argument( 'restrict_condition', '' ) );

    // include the sync action if the widget isn't parented
    if( is_null( $this->parent ) )
    {
      $db_operation = $operation_class_name::get_operation( 'widget', 'import', 'add' );
      if( lib::create( 'business\session' )->is_allowed( $db_operation ) )
        $this->add_action( 'import', 'Import', $db_operation,
          'Import participants from an external CSV file' );

      $db_operation =
        $operation_class_name::get_operation( 'widget', 'participant', 'site_reassign' );
      if( lib::create( 'business\session' )->is_allowed( $db_operation ) )
        $this->add_action( 'reassign', 'Site Reassign', $db_operation,
          'Change the preferred site of multiple participants at once' );
    }
  }

  /**
   * Overrides the parent class method based on the restrict condition argument.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param database\modifier $modifier Modifications to the list.
   * @return int
   * @access protected
   */
  public function determine_record_count( $modifier = NULL )
  {
    $restrict_condition = $this->get_argument( 'restrict_condition', '' );
    if( $restrict_condition )
    {
      if( NULL == $modifier ) $modifier = lib::create( 'database\modifier' );
      $modifier->where( 'status', '=', $restrict_condition );
    }

    return parent::determine_record_count( $modifier );
  }

  /**
   * Overrides the parent class method based on the restrict condition argument.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param database\modifier $modifier Modifications to the list.
   * @return array( record )
   * @access protected
   */
  public function determine_record_list( $modifier = NULL )
  {
    $restrict_condition = $this->get_argument( 'restrict_condition', '' );
    if( $restrict_condition )
    {
      if( NULL == $modifier ) $modifier = lib::create( 'database\modifier' );
      $modifier->where( 'status', '=', $restrict_condition );
    }

    return parent::determine_record_list( $modifier );
  }
}
?>
