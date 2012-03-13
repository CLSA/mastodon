#!/usr/bin/php
<?php
/**
 * This is a special script used when upgrading to version 1.1.1
 * This script should be run once and only one after running patch_database.sql
 * It imports any of the file-based PDF contact forms and reads them into the contact_form table.
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

ini_set( 'display_errors', '1' );
error_reporting( E_ALL | E_STRICT );
ini_set( 'date.timezone', 'US/Eastern' );

// utility functions
function out( $msg ) { printf( '%s: %s'."\n", date( 'Y-m-d H:i:s' ), $msg ); }
function error( $msg ) { out( sprintf( 'ERROR! %s', $msg ) ); }
function format_string( $string )
{
  if( is_null( $string ) ) return 'NULL';
  if( is_bool( $string ) ) return $string ? 'true' : 'false';
  if( is_string( $string ) ) $string = trim( $string );
  return 0 == strlen( $string ) ? 'NULL' : '"'.mysql_real_escape_string( $string ).'"';
}

$error_count = 0;
$file_count = 0;

out( 'Reading configuration parameters' );
// fake server parameters
$_SERVER['HTTPS'] = false;
$_SERVER['HTTP_HOST'] = 'localhost';
require_once '../../../web/settings.ini.php';
require_once '../../../web/settings.local.ini.php';
require_once $SETTINGS['path']['CENOZO'].'/app/settings.ini.php';

// open connection to the database
out( 'Connecting to database' );
require_once $SETTINGS['path']['ADODB'].'/adodb.inc.php';
$db = ADONewConnection( $SETTINGS['db']['driver'] );
$db->SetFetchMode( ADODB_FETCH_ASSOC );
$result = $db->Connect( $SETTINGS['db']['server'],
                        $SETTINGS['db']['username'],
                        $SETTINGS['db']['password'],
                        $SETTINGS['db']['database'] );
if( false == $result )
{
  error( 'Unable to connect, quiting' );
  die();
}

// loop through every file in the contact form directory (which should be in the web directory)
$directory = '../../../web/contact';
out( 'Importing PDF files found in '.$directory );
if( !file_exists( $directory ) )
{
  out( 'Directory does not exist, quiting' );
  die();
}

foreach( scandir( $directory ) as $filename )
{
  $full_filename = sprintf( '%s/%s', $directory, $filename );
  if( 0 != strcasecmp( '.pdf', substr( $filename, -4 ) ) ) continue;

  out( 'Found file '.$filename );

  // open and read the file
  $handle = fopen( $full_filename, 'rb' );
  
  if( false === $handle )
  {
    error( 'Unable to open file, skipping' );
    $error_count++;
    continue;
  }

  $contents = fread( $handle, filesize( $full_filename ) );
  if( false == $contents )
  {
    error( 'Unable to read file, skipping' );
    $error_count++;
    continue;
  }

  if( false == fclose( $handle ) )
  {
    error( 'Unable to close file, skipping' );
    $error_count++;
    continue;
  }

  // write the data to the database
  $participant_id = $db->GetOne( sprintf(
    'SELECT id FROM participant WHERE uid = %s',
    format_string( substr( $filename, 0, -4 ) ) ) );
  if( is_null( $participant_id ) )
  {
    error( sprintf( 'Cannot find participant matching UID %s',
           format_string( substr( $filename, 0, -4 ) ) ) );
    $error_count++;
    continue;
  }
  out( sprintf( '%d', $participant_id ) );

  $sql = sprintf(
    'INSERT INTO %scontact_form SET '.
    'complete = 1, '.
    'invalid = 0, '.
    'participant_id = ( SELECT id FROM participant WHERE uid = %s ), '.
    'validated_contact_form_entry_id = NULL, '.
    'date = %s, '.
    'scan = %s',
    $SETTINGS['db']['prefix'],
    format_string( substr( $filename, 0, -4 ) ),
    format_string( date( 'Y-m-d', fileatime( $full_filename ) ) ),
    format_string( $contents ) );

  if( false == $db->Execute( $sql ) )
  {
    error( 'Unable to write to the database, skipping' );
    $error_count++;
    continue;
  }
  out( 'File imported to the database, id '.$db->Insert_ID() );

  // delete the file now that we're done with it
  if( false == unlink( $full_filename ) )
  {
    error( 'Unable to delete the file, this must now be done manually!' );
    $error_count++;
  }

  $file_count++;
}

out( $file_count ? sprintf( 'Processed %d files', $file_count ) : 'No PDF files found' );
out( sprintf( 'Import complete (%s)',
     $error_count ?
     $error_count.' error'.( 1 < $error_count ? 's' : '' ) :
     'you may not delete the contact directory' ) );
?>
