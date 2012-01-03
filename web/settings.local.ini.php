<?php
/**
 * settings.local.ini.php
 * 
 * Defines local initialization settings for mastodon, overriding default settings found in
 * settings.ini.php
 */

global $SETTINGS;

// Whether or not to run the application in development mode
$SETTINGS['general']['development_mode'] = true;

// The web url of the Cenozo framework
$SETTINGS['url']['CENOZO'] = sprintf( 'http%s://%s/patrick/cenozo',
                                          'on' == $_SERVER["HTTPS"] ? 's' : '',
                                          $_SERVER["HTTP_HOST"] );

// The file path to the application
$SETTINGS['path']['CENOZO'] = '/home/patrick/files/repositories/cenozo';
$SETTINGS['path']['APPLICATION'] = '/home/patrick/files/repositories/mastodon';

// The path to the log file
$SETTINGS['path']['LOG_FILE'] = $SETTINGS['path']['APPLICATION'].'/log';

// The database name, username and password
$SETTINGS['db']['database'] = 'patrick_mastodon';
$SETTINGS['db']['username'] = 'patrick';
$SETTINGS['db']['password'] = '1qaz2wsx';

// audit database settings (null values use the limesurvey database settings)
// NOTE: either the prefix or the database must not different from limesurvey's prefix
// and server, otherwise auditing will not work.
$SETTINGS['audit_db']['enabled'] = false;
$SETTINGS['audit_db']['prefix'] = 'audit_';
?>
