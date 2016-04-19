<?php
/**
 * base_form.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @filesource
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
    $path = sprintf( '%s/%s', FORM_IN_PATH, str_replace( '_form', '', static::get_table_name() ) );
    foreach( scandir( $path ) as $filename )
    {
      $filename = $path.'/'.$filename;
      if( '.pdf' == substr( $filename, -4 ) )
      {
        // open and read the pdf file
        $resource = fopen( $filename, 'rb' );
        if( false === $resource )
        {
          log::err( sprintf( 'Unable to open %s file: "%s"',
                             str_replace( '_', ' ', static::get_table_name() ),
                             $filename ) );
          continue;
        }

        $scan = fread( $resource, filesize( $filename ) );
        if( false === $scan )
        {
          log::err( sprintf( 'Unable to read %s file: "%s"',
                             str_replace( '_', ' ', static::get_table_name() ),
                             $filename ) );
          continue;
        }

        if( false === fclose( $resource ) )
        {
          log::err( sprintf( 'Unable to close %s file: "%s"',
                             str_replace( '_', ' ', static::get_table_name() ),
                             $filename ) );
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
   * Get the number of forms which have a certain number of entries associated with it
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param integer $entries The number of entries to test for
   * @param string $comparison An integer-based comparison operator (eg: <, >, >=, =, etc)
   * @access public
   */
  static public function count_for_entries( $entries, $comparison = '=', $modifier = NULL )
  {
    // requires custom sql
    return static::db()->get_one( sprintf(
      'SELECT COUNT(*) '.
      'FROM ( '.
      '  SELECT %s.id, IF( %s_entry.id IS NULL, 0, COUNT(*) ) count '.
      '  FROM %s '.
      '  LEFT JOIN %s_entry ON %s.id = %s_entry.%s_id '.
      '  %s '.
      '  GROUP BY %s.id '.
      '  HAVING count %s %d '.
      ') temp',
      static::get_table_name(),
      static::get_table_name(),
      static::get_table_name(),
      static::get_table_name(),
      static::get_table_name(),
      static::get_table_name(),
      static::get_table_name(),
      is_null( $modifier ) ? '' : $modifier->get_sql(),
      static::get_table_name(),
      $comparison,
      $entries ) );
  }

  /**
   * Gets the file associated with this form
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
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

    $padded_id = str_pad( $this->id, 7, '0', STR_PAD_LEFT );
    $filename = sprintf( '%s/%s/%s/%s.pdf',
                         sprintf( '%s/%s', FORM_OUT_PATH, str_replace( '_form', '', static::get_table_name() ) ),
                         substr( $padded_id, 0, 3 ),
                         substr( $padded_id, 3, 2 ),
                         substr( $padded_id, 5 ) );

    return $filename;
  }

  /**
   * Writes the file associciated with this form to the disk
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param string The contents of the form (as a binary string)
   * @abstract
   * @access public
   */
  public function write_form( $contents )
  {
    $filename = $this->get_filename();
    $table_name = static::get_table_name();
    $type = substr( $table_name, 0, strrpos( $table_name, '_' ) );

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

  /**
   * Imports the form into the system.
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @param database\form_entry $db_base_form_entry The entry to be used as the valid data.
   * @abstract
   * @access public
   */
  abstract public function import( $db_base_form_entry );
}
