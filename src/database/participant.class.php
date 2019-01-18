<?php
/**
 * participant.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace mastodon\database;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * participant: record
 */
class participant extends \cenozo\database\participant
{
  /**
   * Replace parent method
   */
  public static function get_valid_uid_list( $uid_list, $db_application = NULL, $unreleased = false )
  {
    $setting_manager = lib::create( 'business\setting_manager' );
    $uid_regex = $setting_manager->get_setting( 'general', 'uid_regex' );

    $output_uid_list = array();

    if( !is_array( $uid_list ) )
    {
      // sanitize the entries
      $uid_list = explode( ' ', // delimite string by spaces and create array from result
                  preg_replace( '/[^a-zA-Z0-9 ]/', '', // remove anything that isn't a letter, number of space
                  preg_replace( '/[\s,;|\/]/', ' ', // replace whitespace and separation chars with a space
                  strtoupper( $uid_list ) ) ) ); // convert to uppercase
    }

    // match UIDs (eg: A123456)
    $uid_list = array_filter( $uid_list, function( $string ) {
      global $uid_regex;
      return 1 == preg_match( sprintf( '/%s/', $uid_regex ), $string );
    } );

    if( 0 < count( $uid_list ) )
    {
      $session = lib::create( 'business\session' );
      $db_site = $session->get_site();
      $db_role = $session->get_role();

      // make list unique and sort it
      $uid_list = array_unique( $uid_list );
      sort( $uid_list );

      // go through the list and remove invalid UIDs
      $select = lib::create( 'database\select' );
      $select->add_column( 'uid' );
      $select->from( 'participant' );
      $modifier = lib::create( 'database\modifier' );
      $modifier->where( 'uid', 'IN', $uid_list );
      $modifier->order( 'uid' );

      if( !is_null( $db_application ) )
      {
        // restrict to participant cohorts in the given application
        $modifier->join( 'application_has_cohort', 'participant.cohort_id', 'application_has_cohort.cohort_id' );
        $modifier->join( 'application', 'application_has_cohort.application_id', 'application.id' );
        $modifier->where( 'application.id', '=', $db_application->id );

        // restrict to released or unreleased participants
        if( $unreleased )
        {
          $sub_mod = lib::create( 'database\modifier' );
          $sub_mod->where( 'participant.id', '=', 'application_has_participant.participant_id', false );
          $sub_mod->where( 'application_has_participant.application_id', '=', 'application.id', false );
          $modifier->join_modifier( 'application_has_participant', $sub_mod, 'left' );
          $modifier->where( 'application_has_participant.datetime', '=', NULL );
        }
      }

      foreach( static::select( $select, $modifier ) as $row ) $output_uid_list[] = $row['uid'];
    }

    return $output_uid_list;
  }
}
