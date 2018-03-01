<?php
/**
 * base_form.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

namespace mastodon\database;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Base class for all form records
 */
abstract class base_form extends \cenozo\database\record
{
  /**
   * Overrides the parent method in order to read in any PDF files in the
   * form's directory into the database.
   */
  public static function select( $select = NULL, $modifier = NULL, $return_alternate = '' )
  {
    // first load any scans in the form directory into the database
    $table_name = static::get_table_name();
    $path = sprintf( '%s/%s', FORM_IN_PATH, str_replace( '_form', '', $table_name ) );
    foreach( scandir( $path ) as $filename )
    {
      $filename = $path.'/'.$filename;
      if( '.pdf' == substr( $filename, -4 ) )
      {
        // open and read the pdf file
        $resource = fopen( $filename, 'rb' );
        if( false === $resource )
        {
          log::error( sprintf( 'Unable to open %s file: "%s"', str_replace( '_', ' ', $table_name ), $filename ) );
          continue;
        }

        $scan = fread( $resource, filesize( $filename ) );
        if( false === $scan )
        {
          log::error( sprintf( 'Unable to read %s file: "%s"', str_replace( '_', ' ', $table_name ), $filename ) );
          continue;
        }

        if( false === fclose( $resource ) )
        {
          log::error( sprintf( 'Unable to close %s file: "%s"', str_replace( '_', ' ', $table_name ), $filename ) );
          continue;
        }

        // create a new form
        $db_form = new static();
        $db_form->date = util::get_datetime_object();
        $db_form->save();

        // write the data to disk
        $db_form->write_form( $scan );

        // now delete the PDF file from the disk
        unlink( $filename );
      }
    }

    // now copmlete the constructor
    return parent::select( $select, $modifier, $return_alternate );
  }

  /**
   * Gets the file associated with this form
   * 
   * Before forms are imported (before entry and adjudication) they will be located in the application's
   * local form storage (FORM_OUT_PATH), after being imported they are moved to the framework's form
   * storage (FORM_PATH).  This method will return the correct path based on whether the record's form_id
   * column is set.
   * @return string
   * @access public
   */
  public function get_filename()
  {
    if( is_null( $this->id ) )
    {
      log::warning(
        'Tried to get filename of form without a primary id.' );
      return NULL;
    }

    if( !is_null( $this->form_id ) )
    {
      $filename = $this->get_form()->get_filename();
    }
    else
    {
      $padded_id = str_pad( $this->id, 7, '0', STR_PAD_LEFT );
      $filename = sprintf( '%s/%s/%s/%s/%s.pdf',
                           FORM_OUT_PATH,
                           str_replace( '_form', '', static::get_table_name() ),
                           substr( $padded_id, 0, 3 ),
                           substr( $padded_id, 3, 2 ),
                           substr( $padded_id, 5 ) );
    }

    return $filename;
  }

  /**
   * Imports the form into the system.
   * @param database\form_entry $db_form_entry The entry to be used as the valid data.
   * @access public
   */
  public function import( $db_form_entry )
  {
    $form_type_class_name = lib::get_class_name( 'database\form_type' );
    $participant_class_name = lib::get_class_name( 'database\participant' );

    $table_name = static::get_table_name();
    $type = str_replace( '_form', '', $table_name );
    if( 'contact_form' == $table_name )
      throw lib::create( 'exception\runtime', 'Importing contact forms is not implemented.', __METHOD__ );
    if( is_null( $db_form_entry ) || !$db_form_entry->id )
      throw lib::create( 'exception\runtime',
        sprintf( 'Tried to import invalid %s form.', $type ),
        __METHOD__ );
    if( !$db_form_entry->submitted )
      throw lib::create( 'exception\runtime',
        sprintf( 'Tried to import %s form entry that hasn\'t been submitted.', $type ),
        __METHOD__ );
    if( 0 < count( $db_form_entry->get_errors() ) )
      throw lib::create( 'exception\runtime',
        sprintf( 'Tried to import %s form entry that has errors.', $type ),
        __METHOD__ );

    $filename = $this->get_filename();
    $db_participant = $participant_class_name::get_unique_record( 'uid', $db_form_entry->uid );

    // create the form
    $db_form_type = $form_type_class_name::get_unique_record( 'name', $type );
    $db_form = lib::create( 'database\form' );
    $db_form->participant_id = $db_participant->id;
    $db_form->form_type_id = $db_form_type->id;
    $db_form->date = !is_null( $db_form_entry->date ) ? $db_form_entry->date : util::get_datetime_object();
    $db_form->save();

    // save the new form to the hin form and set the validated form entry
    $column_name = sprintf( 'validated_%s_entry_id', $table_name );
    $this->$column_name = $db_form_entry->id;
    $this->form_id = $db_form->id;
    $this->completed = true;
    $this->save();

    // move the file from the application to the framework
    if( !file_exists( $filename ) )
    {
      log::warning( sprintf( 'Data entry %s form file (%s) is missing.', $type, $filename ) );
    }
    else
    {
      if( !$db_form->copy_file( $filename ) )
      {
        throw lib::create( 'exception\runtime',
          sprintf( 'Unable to copy %s form file (%s).', $type, $filename ),
          __METHOD__ );
      }
      else unlink( $filename );
    }
  }

  /**
   * Writes the file associciated with this form to the disk
   * 
   * @param string The contents of the form (as a binary string)
   * @abstract
   * @access public
   */
  public function write_form( $contents )
  {
    $filename = $this->get_filename();
    $table_name = static::get_table_name();
    $type = substr( $table_name, 0, strrpos( $table_name, '_form' ) );

    // create directory if necessary
    $directory = substr( $filename, 0, strrpos( $filename, '/' ) );
    if( !is_dir( $directory ) )
      if( false === mkdir( $directory, 0777, true ) )
        throw lib::create( 'exception\runtime',
          sprintf( 'Unable to create directory for %s form pdf file "%s"',
                   $type,
                   $filename ),
          __METHOD__ );

    $resource = fopen( $this->get_filename(), 'w' );
    if( false === fwrite( $resource, $contents ) )
      throw lib::create( 'exception\runtime',
        sprintf( 'Unable to write %s form pdf file "%s"',
                 $type,
                 $filename ),
        __METHOD__ );

    fclose( $resource );
  }
}
