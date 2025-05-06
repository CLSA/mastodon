<?php
/**
 * Main web script which drives the application
 */

require_once '../settings.ini.php';
require_once '../settings.local.ini.php';
require_once $SETTINGS['path']['CENOZO'].'/src/bootstrap.class.php';
$bootstrap = new \cenozo\bootstrap();
$bootstrap->initialize( 'ui3' );
