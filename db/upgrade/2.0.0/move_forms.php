#!/usr/bin/php
<?php
/**
 * This is a special script used when upgrading to version 2
 * This script should be run once after running patch_database.sql
 * It moves and renames all forms from Mastodon1 to Cenozo2
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
  /**
   * Reads the framework and application settings
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function read_settings()
  {
    // include the initialization settings
    global $SETTINGS;
    require_once '../../../settings.ini.php';
    require_once '../../../settings.local.ini.php';
    require_once $SETTINGS['path']['CENOZO'].'/src/initial.class.php';
    $initial = new \cenozo\initial();
    $this->settings = $initial->get_settings();
  }

  public function connect_database()
  {
    $server = $this->settings['db']['server'];
    $username = $this->settings['db']['username'];
    $password = $this->settings['db']['password'];
    $name = $this->settings['db']['database_prefix'] . $this->settings['general']['instance_name'];
    $this->db = new \mysqli( $server, $username, $password, $name );
    if( $this->db->connect_error )
    {
      error( $this->db->connect_error );
      die();
    }
  }

  /**
   * Executes the patch
   * 
   * @author Patrick Emond <emondpd@mcmaster.ca>
   * @access public
   */
  public function execute()
  {
    // make sure script is run with root access
    if( 0 != posix_getuid() )
    {
      error( 'This script must be run as the root user' );
      die();
    }

    out( 'Reading configuration parameters' );
    $this->read_settings();

    out( 'Connecting to database' );
    $this->connect_database();

    // make sure the destination directory is writable
    if( !is_writable( $this->settings['path']['FORM'] ) )
    {
      error( 'Destination folder "%s" is not writable', $this->settings['path']['FORM'] );
      die();
    }

    // include the application's initialization settings
    if( array_key_exists( 'path', $this->settings ) && array_key_exists( 'FORM_OUT', $this->settings['path'] ) )
    {
      $form_out_path = $this->settings['path']['FORM_OUT'];
      if( is_readable( $form_out_path ) )
      {
        // get the form_id for each file
        out( 'Getting form lookup values from the database' );
        $result = $this->db->query(
          'SELECT "consent" AS type, id, form_id FROM consent_form WHERE form_id IS NOT NULL '.
          'UNION SELECT "contact" AS type, id, form_id FROM contact_form WHERE form_id IS NOT NULL '.
          'UNION SELECT "hin" AS type, id, form_id FROM hin_form WHERE form_id IS NOT NULL '.
          'UNION SELECT "proxy" AS type, id, form_id FROM proxy_form WHERE form_id IS NOT NULL ' );
        if( false === $result )
        {
          error( $this->db->error );
          die();
        }

        $form_data = array( 'consent' => array(), 'contact' => array(), 'hin' => array(), 'proxy' => array() );
        while( $row = $result->fetch_assoc() ) $form_data[$row['type']][$row['id']] = $row['form_id'];

        // now parse all pdf files in the form out path
        out( sprintf( 'Reading all forms found in %s', $form_out_path ) );
        $file_list = array();

        $no_match_count = 0;
        $lines = array();
        exec( sprintf( 'find %s -type f | grep "\.pdf$"', $form_out_path ), $lines );
        foreach( $lines as $line )
        {
          $parts = explode( '/', str_replace( $form_out_path, '', $line ) );
          
          $type = NULL;
          $id = NULL;

          if( 5 == count( $parts ) )
          {
            $type = $parts[1];
            $id = intval( $parts[2].$parts[3].str_replace( '.pdf', '', $parts[4] ) );
          }

          if( !in_array( $type, array( 'consent', 'contact', 'hin', 'proxy' ) ) || 1 > $id )
          {
            error( sprintf( 'Cannot parse file "%s", skipping', $line ) );
          }
          else
          {
            if( !array_key_exists( $id, $form_data[$type] ) )
            {
              $no_match_count++;
            }
            else
            {
              $file_list[] = array( 'type' => $type, 'id' => $id, 'path' => $line );
            }
          }
        }

        out( sprintf( 'A total of %d forms have been found and %d are orphaned, processing',
                      count( $file_list ),
                      $no_match_count ) );

        // and finally move files
        $done = 0;
        foreach( $file_list as $file )
        {
          $form_id = $form_data[$file['type']][$file['id']];
          $padded_id = str_pad( $form_id, 7, '0', STR_PAD_LEFT );
          $directory = sprintf( '%s/%s/%s/%s',
                               $this->settings['path']['FORM'],
                               $file['type'],
                               substr( $padded_id, 0, 3 ),
                               substr( $padded_id, 3, 2 ) );
          $filename = sprintf( '%s.pdf', substr( $padded_id, 5 ) );

          if( !file_exists( $directory ) ) mkdir( $directory, 0777, true );
          exec( sprintf( 'mv "%s" "%s/%s"', $file['path'], $directory, $filename ) );
          if( 0 == ++$done % 10000 ) out( sprintf( 'Finished moving %d files', $done ) );
        }

        exec( sprintf( 'chown www-data.www-data -R %s/*', $this->settings['path']['FORM'] ) );
        if( 0 != $done % 10000 ) out( sprintf( 'Finished moving %d files', $done ) );
      }

      out( 'Done' );
    }
    else out( 'Done' );
  }

  /**
   * Contains all initialization parameters.
   * @var array
   * @access private
   */
  private $settings = array();
}

$patch = new patch();
$patch->execute();
