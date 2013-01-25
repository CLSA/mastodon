<?php
/**
 * activity_chart.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * widget activity chart
 */
class activity_chart extends \cenozo\ui\widget\activity_chart
{
  /**
   * Add the cohort name to the site columns
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access protected
   */
  protected function setup()
  {
    parent::setup();

    $month_columns = $this->get_variable( 'month_columns' );
    foreach( $month_columns as $index => $column )
    {
      if( array_key_exists( 'site_id', $column ) )
      {
        $db_site = lib::create( 'database\site', $column['site_id'] );
        $month_columns[$index]['name'] .=
          sprintf( ' (%s)', $db_site->get_service()->get_cohort()->name );
      }
    }

    $year_columns = $this->get_variable( 'year_columns' );
    foreach( $year_columns as $index => $column )
    {
      if( array_key_exists( 'site_id', $column ) )
      {
        $db_site = lib::create( 'database\site', $column['site_id'] );
        $year_columns[$index]['name'] .=
          sprintf( ' (%s)', $db_site->get_service()->get_cohort()->name );
      }
    }

    $this->set_variable( 'month_columns', $month_columns );
    $this->set_variable( 'year_columns', $year_columns );
  }
}
