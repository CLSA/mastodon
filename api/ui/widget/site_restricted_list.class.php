<?php
/**
 * site_restricted_list.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Base class for all list widgets which may be restricted by site.
 */
abstract class site_restricted_list extends \cenozo\ui\widget\site_restricted_list
{
  /**
   * Constructor
   * 
   * Defines all variables required by the site restricted list.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param string $subject The subject being listed.
   * @param array $args An associative array of arguments to be processed by the widget
   * @access public
   */
  public function __construct( $subject, $args )
  {
    parent::__construct( $subject, $args );
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
    
    // if restricted, show the site's name and cohort in the heading
    $predicate = is_null( $this->db_restrict_site )
               ? 'all sites'
               : sprintf( '%s (%s)',
                          $this->db_restrict_site->name,
                          $this->db_restrict_site->cohort );

    $this->set_heading( $this->get_subject().' list for '.$predicate );
  }
  
  /**
   * Sets up the operation with any pre-execution instructions that may be necessary.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function setup()
  {
    parent::setup();

    // we're restricting to a site, so remove the site and cohort columns
    if( !is_null( $this->db_restrict_site ) )
    {
      $this->remove_column( 'site' );
      $this->remove_column( 'cohort' );
    }

    if( static::may_restrict() )
    {
      // if this is a top tier role, give them a list of sites to choose from
      // (for lists with no parent only!)
      if( is_null( $this->parent ) )
      {
        $site_class_name = lib::get_class_name( 'database\site' );
        $site_mod = lib::create( 'database\modifier' );
        $site_mod->order( 'cohort' );
        $site_mod->order( 'name' );
        $sites = array();
        foreach( $site_class_name::select( $site_mod ) as $db_site )
          $sites[$db_site->id] = sprintf( '%s (%s)', $db_site->name, $db_site->cohort );
        $this->set_variable( 'sites', $sites );
      }
    }
  }

  /**
   * Overrides the parent class method based on the restrict site member.
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
      if( is_null( $modifier ) ) $modifier = lib::create( 'database\modifier' );
      $site_column = ( $this->extended_site_selection ? 'participant_site.' : '' ).'site_id';
      $modifier->where( $site_column, '=', $this->db_restrict_site->id );
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
      if( is_null( $modifier ) ) $modifier = lib::create( 'database\modifier' );
      $site_column = ( $this->extended_site_selection ? 'participant_site.' : '' ).'site_id';
      $modifier->where( $site_column, '=', $this->db_restrict_site->id );
    }

    // skip the parent method
    // php doesn't allow parent::parent::method() so we have to do the less safe code below
    $base_list_class_name = lib::get_class_name( 'ui\widget\base_list' );
    return $base_list_class_name::determine_record_list( $modifier );
  }

  /**
   * Whether the subject is jurisdiction based.
   * @var boolean
   * @access protected
   */
  protected $extended_site_selection = false;
}
?>
