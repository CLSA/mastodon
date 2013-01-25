<?php
/**
 * base_report.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Base class for all report widgets
 * 
 * @abstract
 */
abstract class base_report extends \cenozo\ui\widget\base_report
{
  /**
   * Constructor
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param string $subject The subject being viewed.
   * @param string $name The name of the operation.
   * @param array $args An associative array of arguments to be processed by the widget
   * @access public
   */
  public function __construct( $subject, $args )
  {
    parent::__construct( $subject, 'report', $args );
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
  
    $this->restrictions['cohort'] = false;
    $this->restrictions['source'] = false;
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

    if( $this->restrictions[ 'cohort' ] )
    {
      $participant_class_name = lib::get_class_name( 'database\participant' );
      $session = lib::create( 'business\session' );

      if( 'administrator' == $session->get_role()->name )
      {
        $cohort_list = array( 0 => 'all' );
        $class_name = lib::get_class_name( 'database\cohort' );
        foreach( $class_name::select() as $db_cohort )
          $cohort_list[ $db_cohort->id ] = $db_cohort->name;
      }
      else
      {
        $db_cohort = $session->get_site()->get_service()->get_cohort();
        $cohort_list = array( $db_cohort->id => $db_cohort->name );
      }
      $this->set_parameter( 'restrict_cohort_id', key( $cohort_list ), true, $cohort_list );
    }

    if( $this->restrictions[ 'source' ] )
    {
      $source_list = array( 0 => 'all' );
      $class_name = lib::get_class_name( 'database\source' );
      foreach( $class_name::select() as $db_source )
        $source_list[ $db_source->id ] = $db_source->name;
      
      $this->set_parameter( 'restrict_source_id', key( $source_list ), true, $source_list );
    }
  }

  /**
   * Adds more restrictions to reports.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param string $restriction_type The type of restriction requested.
   * @throws exception\argument
   * @access protected
   */
  protected function add_restriction( $restriction_type )
  {
    parent::add_restriction( $restriction_type );

    if( 'cohort' == $restriction_type )
    {
      $this->restrictions[ 'cohort' ] = true;
      $this->add_parameter( 'restrict_cohort_id', 'enum', 'Cohort' );
    }
    else if( 'source' == $restriction_type )
    {
      $this->restrictions[ 'source' ] = true;
      $this->add_parameter( 'restrict_source_id', 'enum', 'Source' );
    }
  }
}
