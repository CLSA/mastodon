<?php
/**
 * base_form.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\database
 * @filesource
 */

namespace mastodon\database;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * Base class for all form records
 *
 * @package mastodon\database
 */
abstract class base_form extends \cenozo\database\record
{
  // TODO: document
  public static function select( $modifier = NULL, $count = false )
  {
    // first load any scans in the form directory into the database
    $path_constant = sprintf( '%s_PATH', strtoupper( static::get_table_name() ) );
    $path = constant( $path_constant );
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
        $db_form->date = util::get_datetime_object()->format( 'Y-m-d' );
        $db_form->scan = $scan;
        $db_form->save();

        // now delete the PDF file from the disk
        unlink( $filename );
      }
    }

    // now copmlete the constructor
    return parent::select( $modifier, $count );
  }

  // TODO: document
  public function __get( $column_name )
  {
    // only override if the column is "scan"
    if( 'scan' != $column_name ) return parent::__get( $column_name );

    // the record does not read mediumblob types, so custom sql is needed
    if( !is_null( $this->id ) )
    { // read the scan from the database
      $modifier = lib::create( 'database\modifier' );
      $modifier->where( 'id', '=', $this->id );
      $this->scan_value = static::db()->get_one( sprintf(
        'SELECT scan FROM %s %s',
        static::get_table_name(),
        $modifier->get_sql() ) );
    }

    return $this->scan_value;
  }

  // TODO: document
  public function __set( $column_name, $value )
  {
    if( 'scan' != $column_name ) parent::__set( $column_name, $value );
    else
    {
      $this->scan_value = $value;
      $this->scan_changed = true;
    }
  }

  // TODO: document
  public function save()
  {
    // first save the record as usual
    parent::save();

    if( $this->read_only )
    {
      log::warning( 'Tried to save read-only record.' );
      return;
    }

    // now save the scan if it is not null
    if( $this->scan_changed && !is_null( $this->id ) )
    {
      $database_class_name = lib::get_class_name( 'database\database' );

      $modifier = lib::create( 'database\modifier' );
      $modifier->where( 'id', '=', $this->id );
      static::db()->execute( sprintf(
        'UPDATE %s SET scan = %s %s',
        static::get_table_name(),
        $database_class_name::format_string( $this->scan_value ),
        $modifier->get_sql() ) );
    }
  }

  // TODO: document
  abstract public function import( $db_base_form_entry );

  // TODO: document
  protected $scan_changed = false;

  // TODO: document
  protected $scan_value = NULL;
}
?>
