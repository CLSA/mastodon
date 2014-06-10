#!/usr/bin/php
<?php
/**
 * This is a special script used when upgrading to version 1.2.7
 * This script should be run once and only one after running patch_database.sql
 * It exports all PDF contact forms from the database to the filesystem.
 * 
 * @author Patrick Emond <emondpd@mcmaster.ca>
 */

ini_set( 'display_errors', '1' );
error_reporting( E_ALL | E_STRICT );
ini_set( 'date.timezone', 'US/Eastern' );

// utility functions
function out( $msg ) { printf( '%s: %s'."\n", date( 'Y-m-d H:i:s' ), $msg ); }
function error( $msg ) { out( sprintf( 'ERROR! %s', $msg ) ); }

class patch
{
  public function add_settings( $settings, $replace = false )
  {
    if( $replace )
    {
      $this->settings = $settings;
    }
    else
    {
      foreach( $settings as $category => $setting )
      {
        if( !array_key_exists( $category, $this->settings ) )
        {
          $this->settings[$category] = $setting;
        }
        else
        {
          foreach( $setting as $key => $value )
            if( !array_key_exists( $key, $this->settings[$category] ) )
              $this->settings[$category][$key] = $value;
        }
      }
    }
  }

  function write_form( $id, $contents, $type )
  {
    $path_constant = sprintf( '%s_FORM_DATA_PATH', strtoupper( $type ) );
    $padded_id = str_pad( $id, 7, '0', STR_PAD_LEFT );
    $filename = sprintf( '%s/%s/%s/%s.pdf',
                         constant( $path_constant ),
                         substr( $padded_id, 0, 3 ),
                         substr( $padded_id, 3, 2 ),
                         substr( $padded_id, 5 ) );

    // create directory if necessary
    $directory = substr( $filename, 0, strrpos( $filename, '/' ) );
    if( !is_dir( $directory ) )
      if( false === mkdir( $directory, 0777, true ) )
        error( sprintf( 'Unable to create directory "%s" for file "%s"',
                        $directory,
                        $filename ) );

    $resource = fopen( $filename, 'w' );
    if( false !== $resource )
    {
      fwrite( $resource, $contents );
      fclose( $resource );
    }
  }

  public function execute()
  {
    $error_count = 0;
    $file_count = 0;

    out( 'Reading configuration parameters' );
    // fake server parameters
    $_SERVER['HTTPS'] = false;
    $_SERVER['HTTP_HOST'] = 'localhost';

    require_once '../../../web/settings.ini.php';
    require_once '../../../web/settings.local.ini.php';

    // include the application's initialization settings
    global $SETTINGS;
    $this->add_settings( $SETTINGS, true );
    unset( $SETTINGS );

    // include the framework's initialization settings
    require_once $this->settings['path']['CENOZO'].'/app/settings.local.ini.php';
    $this->add_settings( $settings );
    require_once $this->settings['path']['CENOZO'].'/app/settings.ini.php';
    $this->add_settings( $settings );

    if( !array_key_exists( 'general', $this->settings ) ||
        !array_key_exists( 'application_name', $this->settings['general'] ) )
      die( 'Error, application name not set!' );

    define( 'APPNAME', $this->settings['general']['application_name'] );
    define( 'SERVICENAME', $this->settings['general']['service_name'] );
    $this->settings['path']['CENOZO_API'] = $this->settings['path']['CENOZO'].'/api';
    $this->settings['path']['CENOZO_TPL'] = $this->settings['path']['CENOZO'].'/tpl';

    $this->settings['path']['API'] = $this->settings['path']['APPLICATION'].'/api';
    $this->settings['path']['DOC'] = $this->settings['path']['APPLICATION'].'/doc';
    $this->settings['path']['TPL'] = $this->settings['path']['APPLICATION'].'/tpl';

    // the web directory cannot be extended
    $this->settings['path']['WEB'] = $this->settings['path']['CENOZO'].'/web';

    foreach( $this->settings['path'] as $path_name => $path_value )
      define( $path_name.'_PATH', $path_value );
    foreach( $this->settings['url'] as $path_name => $path_value )
      define( $path_name.'_URL', $path_value );

    // open connection to the database
    out( 'Connecting to database' );
    require_once $this->settings['path']['ADODB'].'/adodb.inc.php';
    $db = ADONewConnection( $this->settings['db']['driver'] );
    $db->SetFetchMode( ADODB_FETCH_ASSOC );
    $database = sprintf( '%s%s',
                         $this->settings['db']['database_prefix'],
                         $this->settings['general']['application_name'] );
                        
    $result = $db->Connect( $this->settings['db']['server'],
                            $this->settings['db']['username'],
                            $this->settings['db']['password'],
                            $database );
    if( false == $result )
    {
      error( 'Unable to connect, quiting' );
      die();
    }

    // select every form and write it to the appropriate place
    $total = $db->GetOne( 'SELECT COUNT(*) FROM consent_form' );
    out( sprintf( 'Processing %d consent forms', $total ) );
    $base = 0;
    $increment = 1000;
    while( $base < $total )
    {
      $rows = $db->GetAll( sprintf( 'SELECT id, scan FROM consent_form ORDER BY id LIMIT %d, %d', $base, $increment ) );
      foreach( $rows as $index => $row )
        $this->write_form( $row['id'], $row['scan'], 'consent' );
      out( sprintf( 'Finished %d of %d forms', $base + count( $rows ), $total ) );
      
      $base += $increment;
    }

    $total = $db->GetOne( 'SELECT COUNT(*) FROM contact_form' );
    out( sprintf( 'Processing %d contact forms', $total ) );
    $base = 0;
    $increment = 1000;
    while( $base < $total )
    {
      $rows = $db->GetAll( sprintf( 'SELECT id, scan FROM contact_form ORDER BY id LIMIT %d, %d', $base, $increment ) );
      foreach( $rows as $index => $row )
        $this->write_form( $row['id'], $row['scan'], 'contact' );
      out( sprintf( 'Finished %d of %d forms', $base + count( $rows ), $total ) );
      
      $base += $increment;
    }

    $total = $db->GetOne( 'SELECT COUNT(*) FROM proxy_form' );
    out( sprintf( 'Processing %d proxy forms', $total ) );
    $base = 0;
    $increment = 1000;
    while( $base < $total )
    {
      $rows = $db->GetAll( sprintf( 'SELECT id, scan FROM proxy_form ORDER BY id LIMIT %d, %d', $base, $increment ) );
      foreach( $rows as $index => $row )
        $this->write_form( $row['id'], $row['scan'], 'proxy' );
      out( sprintf( 'Finished %d of %d forms', $base + count( $rows ), $total ) );
      
      $base += $increment;
    }
  }
}

$patch = new patch();
$patch->execute();
