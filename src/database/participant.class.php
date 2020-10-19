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
  public static function get_valid_identifier_list( $db_identifier, $identifier_list, $db_application = NULL, $unreleased = false )
  {
    $setting_manager = lib::create( 'business\setting_manager' );
    $regex = is_null( $db_identifier ) ? $setting_manager->get_setting( 'general', 'uid_regex' ) : $db_identifier->regex;

    $output_identifier_list = array();

    if( !is_array( $identifier_list ) )
    {
      // sanitize the entries
      $identifier_list =
        explode( ' ', // delimite string by spaces and create array from result
        preg_replace( '/[^a-zA-Z0-9_ ]/', '', // remove anything that isn't a letter, number, underscore or space
        preg_replace( '/[\s,;|\/]/', ' ', // replace whitespace and separation chars with a space
        strtoupper( $identifier_list ) ) ) ); // convert to uppercase
    }

    // match identifiers based on regex
    if( !is_null( $regex ) )
    {
      $identifier_list = array_filter( $identifier_list, function( $string ) {
        global $regex;
        return 1 == preg_match( sprintf( '/%s/', $regex ), $string );
      } );
    }

    if( 0 < count( $identifier_list ) )
    {
      $session = lib::create( 'business\session' );
      $db_site = $session->get_site();
      $db_role = $session->get_role();

      // make list unique and sort it
      $identifier_list = array_unique( $identifier_list );
      sort( $identifier_list );

      $select = lib::create( 'database\select' );
      $modifier = lib::create( 'database\modifier' );

      // go through the list and remove invalid UIDs
      if( is_null( $db_identifier ) )
      {
        $select->add_column( 'uid', 'identifier' );
        $select->from( 'participant' );
        $modifier->where( 'uid', 'IN', $identifier_list );
        $modifier->order( 'uid' );
      }
      else
      {
        $select->add_table_column( 'participant_identifier', 'value', 'identifier' );
        $select->from( 'participant' );
        $modifier->join( 'participant_identifier', 'participant.id', 'participant_identifier.participant_id' );
        $modifier->where( 'participant_identifier.identifier_id', '=', $db_identifier->id );
        $modifier->where( 'participant_identifier.value', 'IN', $identifier_list );
        $modifier->order( 'participant_identifier.value' );
      }

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

      foreach( static::select( $select, $modifier ) as $row ) $output_identifier_list[] = $row['identifier'];
    }

    return $output_identifier_list;
  }
}
