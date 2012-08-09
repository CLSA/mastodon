<?php
/**
 * system_message_list.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget system_message list
 */
class system_message_list extends site_restricted_list
{
  /**
   * Constructor
   * 
   * Defines all variables required by the system_message list.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param array $args An associative array of arguments to be processed by the widget
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'system_message', $args );
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

    $this->add_column( 'site.name', 'string', 'Site', true );
    $this->add_column( 'role.name', 'string', 'Role', true );
    $this->add_column( 'title', 'string', 'Title', true );
  }
  
  /**
   * Defines all rows in the list.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function setup()
  {
    parent::setup();
    
    foreach( $this->get_record_list() as $record )
    {
      $db_site = $record->get_site();
      $db_role = $record->get_role();

      // assemble the row for this record
      $this->add_row( $record->id,
        array( 'site.name' => $db_site ? $db_site->name : 'all',
               'role.name' => $db_role ? $db_role->name : 'all',
               'title' => $record->title ) );
    }
  }

  /**
   * Overrides the parent class method to also include system messages with no site
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param database\modifier $modifier Modifications to the list.
   * @return int
   * @access protected
   */
  public function determine_record_count( $modifier = NULL )
  {
    if( !is_null( $this->db_restrict_site ) )
    {
      if( NULL == $modifier ) $modifier = lib::create( 'database\modifier' );
      $modifier->where( 'site_id', '=', $this->db_restrict_site->id );
      $modifier->or_where( 'site_id', '=', NULL );
    }
    
    // skip the parent method
    // php doesn't allow parent::parent::method() so we have to do the less safe code below
    $base_list_class_name = lib::get_class_name( 'ui\widget\base_list' );
    return $base_list_class_name::determine_record_count( $modifier );
  }

  /**
   * Overrides the parent class method based on the restrict site member.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param database\modifier $modifier Modifications to the list.
   * @return array( record )
   * @access protected
   */
  public function determine_record_list( $modifier = NULL )
  {
    if( !is_null( $this->db_restrict_site ) )
    {
      if( NULL == $modifier ) $modifier = lib::create( 'database\modifier' );
      $modifier->where( 'site_id', '=', $this->db_restrict_site->id );
      $modifier->or_where( 'site_id', '=', NULL );
    }
    
    // skip the parent method
    // php doesn't allow parent::parent::method() so we have to do the less safe code below
    $base_list_class_name = lib::get_class_name( 'ui\widget\base_list' );
    return $base_list_class_name::determine_record_list( $modifier );
  }
}
?>
