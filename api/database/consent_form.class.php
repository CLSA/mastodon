<?php
/**
 * consent_form.class.php
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 * @package mastodon\database
 * @filesource
 */

namespace mastodon\database;
use cenozo\lib, cenozo\log, mastodon\util;

/**
 * consent_form: record
 *
 * @package mastodon\database
 */
class consent_form extends \cenozo\database\record
{
  // TODO: document
  public static function select( $modifier = NULL, $count = false )
  {
    // first load any scans in the consent form directory into the database
    foreach( scandir( CONSENT_FORM_PATH ) as $filename )
    {
      $filename = CONSENT_FORM_PATH.'/'.$filename;
      if( '.pdf' == substr( $filename, -4 ) )
      {
        // open and read the pdf file
        $resource = fopen( $filename, 'rb' );
        if( false === $resource )
        {
          log::err( sprintf( 'Unable to open consent form file: "%s"', $filename ) );
          continue;
        }

        $scan = fread( $resource, filesize( $filename ) );
        if( false === $scan )
        {
          log::err( sprintf( 'Unable to read consent form file: "%s"', $filename ) );
          continue;
        }

        if( false === fclose( $resource ) )
        {
          log::err( sprintf( 'Unable to close consent form file: "%s"', $filename ) );
          continue;
        }

        // create a new consent form
        $db_consent_form = lib::create( 'database\consent_form' );
        $db_consent_form->date = util::get_datetime_object()->format( 'Y-m-d' );
        $db_consent_form->scan = $scan;
        $db_consent_form->save();

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
  public function import( $db_consent_form_entry )
  {
    if( is_null( $db_consent_form_entry ) || !$db_consent_form_entry->id )
    {
      throw lib::create( 'exception\runtime',
        'Tried to import invalid consent form entry.', __METHOD__ );
    }

    $database_class_name = lib::get_class_name( 'database\database' );
    $participant_class_name = lib::get_class_name( 'database\participant' );

    // link to the form
    $this->validated_consent_form_entry_id = $db_consent_form_entry->id;

    // import the data to the consent table
    $db_participant =
      $participant_class_name::get_unique_record( 'uid', $db_consent_form_entry->uid );
    $db_consent = lib::create( 'database\consent' );
    $db_consent->participant_id = $db_participant->id;
    $db_consent->event =
      sprintf( 'written %s', $db_consent_form_entry->option_1 ? 'accept' : 'deny' );
    $db_consent->date = util::get_datetime_object()->format( 'Y-m-d' );
    $db_consent->note = 'Imported by data entry system.';
    $db_consent->save();

    // import the data to the hin table
    static::db()->execute( sprintf(
      'INSERT INTO hin SET uid = %s, access = %s '.
      'ON DUPLICATE KEY '.
      'UPDATE uid = VALUES( uid ), access = VALUES( access )',
      $database_class_name::format_string( $db_consent_form_entry->uid ),
      $database_class_name::format_string( $db_consent_form_entry->option_2 ) ) );

    $this->consent_id = $db_consent->id;
    $this->save();
  }

  // TODO: document
  protected $scan_changed = false;

  // TODO: document
  protected $scan_value = NULL;
}
?>
