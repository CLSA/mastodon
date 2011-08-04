<?php
/**
 * mastodon.inc.php
 * 
 * Functions and setup code required by all web scripts.
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */
namespace mastodon;
session_name( dirname( __FILE__ ) );
session_start();
$_SESSION['time']['script_start_time'] = microtime( true );

// set up error handling (error_reporting is also called in session's constructor)
ini_set( 'display_errors', '0' );
error_reporting( E_ALL | E_STRICT );

// Function to gracefully handle missing require_once files
function include_file( $file, $no_error = false )
{
  if( !file_exists( $file ) )
  {
    if( !$no_error )
    {
      throw new \Exception( "<pre>\n".
           "FATAL ERROR: Unable to find required file '$file'.\n".
           "Please check that paths in the mastodon ini are set correctly.\n".
           "</pre>\n" );
    }
  }
  include $file;
}

// load the default, then local settings, then define various settings
include_file( 'mastodon.ini.php' );
include_file( 'mastodon.local.ini.php', true );

$SETTINGS[ 'path' ][ 'API' ] = $SETTINGS[ 'path' ][ 'MASTODON' ].'/api';
$SETTINGS[ 'path' ][ 'DOC' ] = $SETTINGS[ 'path' ][ 'MASTODON' ].'/doc';
$SETTINGS[ 'path' ][ 'SQL' ] = $SETTINGS[ 'path' ][ 'MASTODON' ].'/sql';
$SETTINGS[ 'path' ][ 'TPL' ] = $SETTINGS[ 'path' ][ 'MASTODON' ].'/tpl';
$SETTINGS[ 'path' ][ 'WEB' ] = $SETTINGS[ 'path' ][ 'MASTODON' ].'/web';

foreach( $SETTINGS[ 'path' ] as $path_name => $path_value ) define( $path_name.'_PATH', $path_value );
foreach( $SETTINGS[ 'url' ] as $path_name => $path_value ) define( $path_name.'_URL', $path_value );

// include necessary files
include_file( API_PATH.'/exception/error_codes.inc.php' );
include_file( API_PATH.'/autoloader.class.php' );

// registers an autoloader so classes don't have to be included manually
autoloader::register();

// set up the logger and session
log::self();
$session = business\session::self( $SETTINGS );
$session->initialize();
?>
