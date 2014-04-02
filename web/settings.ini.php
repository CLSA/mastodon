<?php
/**
 * settings.ini.php
 * 
 * Defines initialization settings for mastodon.
 * DO NOT edit this file, to override these settings use settings.local.ini.php instead.
 * Any changes in the local ini file will override the settings found here.
 */

global $SETTINGS;

// tagged version
$SETTINGS['general']['application_name'] = 'mastodon';
$SETTINGS['general']['service_name'] = $SETTINGS['general']['application_name'];
$SETTINGS['general']['version'] = '1.2.7';

// always leave as false when running as production server
$SETTINGS['general']['development_mode'] = false;

// Defines the username and password used by mastodon when communicating as a machine
$SETTINGS['general']['machine_user'] = NULL;
$SETTINGS['general']['machine_password'] = NULL;

// the location of mastodon internal path
$SETTINGS['path']['APPLICATION'] = '/usr/local/lib/mastodon';

// the url to Sabretooth (set to NULL to disable Sabretooth support)
$SETTINGS['url']['SABRETOOTH'] = NULL;

// the url to Beartooth (set to NULL to disable Mastodon support)
$SETTINGS['url']['BEARTOOTH'] = NULL;

// the location of new consent forms which need to be processed
$SETTINGS['path']['CONSENT_FORM'] = $SETTINGS['path']['APPLICATION'].'/doc/form/consent';

// the location of new contact forms which need to be processed
$SETTINGS['path']['CONTACT_FORM'] = $SETTINGS['path']['APPLICATION'].'/doc/form/contact';

// the location of new proxy forms which need to be processed
$SETTINGS['path']['PROXY_FORM'] = $SETTINGS['path']['APPLICATION'].'/doc/form/proxy';
