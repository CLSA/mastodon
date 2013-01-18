<?php
/**
 * quota_chart.class.php
 * 
 * @author Dean Inglis <inglisd@mcmaster.ca>
 * @filesource
 */

namespace mastodon\ui\widget;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Mailout required chart data.
 * 
 * @abstract
 */
class quota_chart extends \cenozo\ui\widget
{
  /**
   * Constructor
   * 
   * @author Dean Inglis <inglisd@mcmaster.ca>
   * @param array $args Pull arguments.
   * @access public
   */
  public function __construct( $args )
  {
    parent::__construct( 'quota', 'chart', $args );
  }

  /**
   * Finish setting the variables in a widget.
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @throws exception\notice
   * @access protected
   */
  protected function setup()
  {
    parent::setup();

    $quota_class_name = lib::get_class_name( 'database\quota' );
    $age_group_class_name = lib::get_class_name( 'database\age_group' );
    $participant_class_name = lib::get_class_name( 'database\participant' );
    $cohort_class_name = lib::get_class_name( 'database\cohort' );
    $service_class_name = lib::get_class_name( 'database\service' );

    $chart_data = array();
    foreach( $cohort_class_name::select() as $db_cohort )
    {
      $value_list = array();

      // admin user may not actually have access to Beartooth/Sabretooth, use machine credentials
      $db_service = $service_class_name::get_unique_record( 'cohort_id', $db_cohort->id );
      $cenozo_manager = lib::create( 'business\cenozo_manager', $db_service->get_url() );
      $cenozo_manager->use_machine_credentials( true );

      foreach( array( 'male', 'female' ) as $gender )
      {
        // loop through all quotas by age group and gender
        $quota_mod = lib::create( 'database\modifier' );
        $quota_mod->where( 'site.service_id', '=', $db_service->id );
        $quota_mod->where( 'gender', '=', $gender );
        $quota_mod->order( 'age_group.lower' );
        foreach( $quota_class_name::select( $quota_mod ) as $db_quota )
        {
          $db_age_group = $db_quota->get_age_group();
          $category = sprintf( '%d to %d',
                               $db_age_group->lower,
                               $db_age_group->upper );

          if( !array_key_exists( $category, $value_list ) )
          {
            $value_list[$category] = 0;

            // get the number of interviews complete in this category
            $pull_mod = lib::create( 'database\modifier' );
            $pull_mod->where( 'age_group.lower', '=', $db_age_group->lower );
            $pull_mod->where( 'gender', '=', $db_quota->gender );

            $result = $cenozo_manager->pull( 'participant', 'list',
                array( 'count' => true,
                       'modifier' => $pull_mod,
                       'qnaire_rank' => 1, // quota only involves the first qnaire
                       'state' => 'completed' ) );
            $value_list[$category] = array(
              'numerator' => intval( $result->data ),
              'denominator' => 0 );
          }

          $value_list[$category]['denominator'] += intval( $db_quota->population );
        }

        foreach( $value_list as $category => $values )
        {
          $title = sprintf( '%s Participant Quota Progress (%s)',
                            ucfirst( $db_cohort->name ),
                            $gender );
          $chart_data[$title][] = array(
            'category' => $category,
            'value' => sprintf( '%0.1f', $values['numerator'] / $values['denominator'] * 100 ) );
        }
      }
    }
    
    $this->set_variable( 'chart_data', $chart_data );
  }
}
?>
