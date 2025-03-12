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
   * Determines the number of files available for download for a particular participant
   * 
   * @param database\participant @db_participant The participant to generate all forms for
   * @return integer
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
      return is_null( $filename ) ? 0 : count( glob( $filename ) );
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
          return 1;
        }
        catch( \cenozo\exception\argument $e )
        {
          // ignore argument errors as they simply mean the participant does not have data
        }
      }
    }

    return 0;
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
      $glob_list = glob( $this->get_filename( $db_participant ) );

      // if there are multiple files then zip them and provide that zip file
      if( 1 < count( $glob_list ) )
      {
        $zip_file_list = [];
        foreach( $glob_list as $index => $file )
        {
          $temp_filename = sprintf(
            '%s %d.%s',
            $this->name,
            $index + 1,
            pathinfo( $file, PATHINFO_EXTENSION )
          );
          copy( $file, sprintf( '%s/%s', TEMP_PATH, $temp_filename ) );
          $zip_file_list[] = $temp_filename;
        }

        $zip_filename = sprintf( '%s/%s.zip', TEMP_PATH, bin2hex( openssl_random_pseudo_bytes( 8 ) ) );
        $zip = new \ZipArchive();
        if( true !== $zip->open( $zip_filename, \ZipArchive::CREATE | \ZipArchive::OVERWRITE ) )
        {
          throw lib::create( 'exception\runtime',
            sprintf(
              'Unable to create zip file "%s" for participant %s data (%s)',
              $zip_filename,
              $db_participant->uid,
              $this->name
            ),
            __METHOD__
          );
        }

        // zip the temporary files then delete them
        foreach( $zip_file_list as $file ) $zip->addFile( sprintf( '%s/%s', TEMP_PATH, $file ), $file );
        $zip->close();
        foreach( $zip_file_list as $file ) unlink( sprintf( '%s/%s', TEMP_PATH, $file ) );

        // now return the zip filename
        return $zip_filename;
      }
      else if( 1 == count( $glob_list ) )
      {
        // directly return the only matching file
        return current( $glob_list );
      }

      // there is not file
      return NULL;
    }

    $opal_manager = lib::create( 'business\opal_manager' );
    if( $opal_manager->get_enabled() )
    {
      $form_data = NULL;

      // check if the participant has a row in any of the opal views
      $select = lib::create( 'database\select' );
      $select->add_column( 'id' );
      $select->add_column( 'opal_view' );
      $select->add_table_column( 'language', 'code', 'language' );
      $modifier = lib::create( 'database\modifier' );
      $modifier->join( 'language', 'participant_data_template.language_id', 'language.id' );
      $modifier->order( 'rank' );
      foreach( $this->get_participant_data_template_list( $select, $modifier ) as $template )
      {
        try
        {
          // if the participant has no data then an argument exception is thrown
          // (silently caught below effectively preventing the form from being created)
          $form_data = $opal_manager->get_values( 'mastodon', $template['opal_view'], $db_participant );

          // check the form data to make sure the template language matches the view data's language
          if( $form_data['LANGUAGE'] != $template['language'] ) continue;

          array_walk( $form_data, function( &$item, $key ) { $item = '' == $item ? 'NA' : $item; } );
          $form_data['NAME'] = sprintf( '%s %s', $db_participant->first_name, $db_participant->last_name );

          // determine the participant's age at the time of the DCS visit
          $dob = $db_participant->date_of_birth;
          $dcs_date = $form_data['DATE'];
          $form_data['AGE'] = !is_null( $dob ) && preg_match( '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $dcs_date ) ?
            $dob->diff( util::get_datetime_object( $form_data['DATE'] ) )->y : '';

          // write the template data to disk
          $db_participant_data_template =
            lib::create( 'database\participant_data_template', $template['id'] );
          $db_participant_data_template->create_data_file();

          // fill in the template and write it to disk
          $filename = $this->get_filename( $db_participant );
          $pdf_writer = lib::create( 'business\pdf_writer' );
          $pdf_writer->set_template( $db_participant_data_template->get_data_filename() );
          $pdf_writer->fill_form( $form_data );
          $success = $pdf_writer->save( $filename );
          $db_participant_data_template->delete_data_file();

          if( !$success )
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
