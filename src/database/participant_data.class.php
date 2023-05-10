<?php
/**
 * participant_data.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @fileparticipant_data
 */

namespace mastodon\database;
use cenozo\lib, cenozo\log, cenozo\util;

/**
 * participant_data: record
 */
class participant_data extends \cenozo\database\record
{
  /**
   * Determines whether the data exists for a particular participant
   * 
   * @param database\participant @db_participant The participant to generate all forms for
   * @return boolean
   * @access public
   */
  public function is_available( $db_participant )
  {
    // make sure the input is a valid database\participant object
    if( !is_a( $db_participant, lib::get_class_name( 'database\participant' ) ) )
      throw lib::create( 'exception\argument', 'db_participant', $db_participant, __METHOD__ );

    if( !is_null( $this->path ) )
    {
      // look for supplementary data in the given path
      $filename = $this->get_filename( $db_participant );
      return is_null( $filename ) ? false : file_exists( $filename );
    }

    $opal_manager = lib::create( 'business\opal_manager' );
    if( $opal_manager->get_enabled() )
    {
      // check if the participant has a row in any of the opal views
      $select = lib::create( 'database\select' );
      $select->add_column( 'opal_view' );
      $modifier = lib::create( 'database\modifier' );
      $modifier->order( 'rank' );
      foreach( $this->get_participant_data_template_list( $select, $modifier ) as $template )
      {
        try
        {
          // if the participant has no data then an argument exception is thrown
          // (silently caught below effectively preventing the form from being created)
          $opal_manager->get_values( 'mastodon', $template['opal_view'], $db_participant );
          return true;
        }
        catch( \cenozo\exception\argument $e )
        {
          // ignore argument errors as they simply mean the participant does not have data
        }
      }
    }

    return false;
  }

  /**
   * Creates the opal form for the given participant
   * 
   * @param database\participant @db_participant The participant to generate all forms for
   * @return string The raw contents of the PDF file (NULL if no form is created)
   * @access public
   */
  public function generate( $db_participant )
  {
    // make sure the input is a valid database\participant object
    if( !is_a( $db_participant, lib::get_class_name( 'database\participant' ) ) )
      throw lib::create( 'exception\argument', 'db_participant', $db_participant, __METHOD__ );

    $data = NULL;

    if( !is_null( $this->path ) )
    {
      // return the full path to the supplementary data
      return $this->get_filename( $db_participant );
    }

    $opal_manager = lib::create( 'business\opal_manager' );
    if( $opal_manager->get_enabled() )
    {
      $form_data = NULL;

      // check if the participant has a row in any of the opal views
      $select = lib::create( 'database\select' );
      $select->add_column( 'id' );
      $select->add_column( 'opal_view' );
      $modifier = lib::create( 'database\modifier' );
      $modifier->order( 'rank' );
      foreach( $this->get_participant_data_template_list( $select, $modifier ) as $template )
      {
        try
        {
          // if the participant has no data then an argument exception is thrown
          // (silently caught below effectively preventing the form from being created)
          $form_data = $opal_manager->get_values( 'mastodon', $template['opal_view'], $db_participant );

          array_walk( $form_data, function( &$item, $key ) { $item = '' == $item ? 'NA' : $item; } );
          $form_data['NAME'] = sprintf( '%s %s', $db_participant->first_name, $db_participant->last_name );

          // write the template data to disk
          $db_participant_data_template =
            lib::create( 'database\participant_data_template', $template['id'] );
          $db_participant_data_template->create_template_file();

          // fill in the template and write it to disk
          $filename = $this->get_filename( $db_participant );
          $pdf_writer = lib::create( 'business\pdf_writer' );
          $pdf_writer->set_template( $db_participant_data_template->get_filename() );
          $pdf_writer->fill_form( $form_data );
          if( !$pdf_writer->save( $filename ) )
          {
            $db_study_phase = $this->get_study_phase();
            throw lib::create( 'exception\runtime',
              sprintf(
                'Failed to generate participant data "%s %s %s %s" for participant %s',
                $db_study_phase->get_study()->name,
                strtoupper( $db_study_phase->code ),
                $this->category,
                $this->name,
                $db_participant->uid
              ),
              __METHOD__
            );
          }

          return $filename;
        }
        catch( \cenozo\exception\argument $e )
        {
          // ignore argument errors as they simply mean the participant does not have data
        }
      }
    }

    return NULL;
  }

  /**
   * Gets the full path to the participant data file
   * @param database\participant @db_participant The participant to generate all forms for
   * @return string
   * @access public
   */
  public function get_filename( $db_participant )
  {
    // make sure the input is a valid database\participant object
    if( !is_a( $db_participant, lib::get_class_name( 'database\participant' ) ) )
      throw lib::create( 'exception\argument', 'db_participant', $db_participant, __METHOD__ );

    if( !is_null( $this->path ) && !is_null( SUPPLEMENTARY_DATA_PATH ) )
    {
      return sprintf(
        '%s/%s',
        SUPPLEMENTARY_DATA_PATH,
        preg_replace( '/<UID>/', $db_participant->uid, $this->path )
      );
    }

    return sprintf(
      '%s/participant_data_%d_%s.pdf',
      TEMP_PATH,
      $this->id,
      $db_participant->uid
    );
  }
}
