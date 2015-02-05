<?php
/**
 * settings.local.ini.php
 * 
 * Defines local initialization settings for mastodon, overriding default settings found in
 * settings.ini.php
 */

global $SETTINGS;

// whether or not to run the application in development mode
$SETTINGS['general']['development_mode'] = true;

// defines the username and password used by mastodon when communicating as a machine
$SETTINGS['general']['machine_user'] = 'mastodon';
$SETTINGS['general']['machine_password'] = '1qaz2wsx';

// the file path to the application
$SETTINGS['path']['CENOZO'] = '/home/patrick/files/repositories/cenozo';
$SETTINGS['path']['APPLICATION'] = '/home/patrick/files/repositories/mastodon';

// the url of Sabretooth (cannot be relative)
$SETTINGS['url']['SABRETOOTH'] = 'https://localhost/patrick/sabretooth';

// the url of Beartooth (cannot be relative)
$SETTINGS['url']['BEARTOOTH'] = 'https://localhost/patrick/beartooth';

// the path to the log file
$SETTINGS['path']['LOG_FILE'] = $SETTINGS['path']['APPLICATION'].'/log';

// database settings (the driver, server and prefixes are set in the framework's settings)
$SETTINGS['db']['username'] = 'patrick';
$SETTINGS['db']['password'] = '1qaz2wsx';

// the location of new consent forms which need to be processed
$SETTINGS['path']['CONSENT_FORM'] = $SETTINGS['path']['APPLICATION'].'/doc/form/consent';

// the location of new contact forms which need to be processed
$SETTINGS['path']['CONTACT_FORM'] = $SETTINGS['path']['APPLICATION'].'/doc/form/contact';

// the location of new proxy forms which need to be processed
$SETTINGS['path']['PROXY_FORM'] = $SETTINGS['path']['APPLICATION'].'/doc/form/proxy';

// the location to store processed consent forms
$SETTINGS['path']['CONSENT_FORM_DATA'] = $SETTINGS['path']['APPLICATION'].'/doc/data/consent';

// the location to store processed contact forms
$SETTINGS['path']['CONTACT_FORM_DATA'] = $SETTINGS['path']['APPLICATION'].'/doc/data/contact';

// the location to store processed proxy forms
$SETTINGS['path']['PROXY_FORM_DATA'] = $SETTINGS['path']['APPLICATION'].'/doc/data/proxy';
