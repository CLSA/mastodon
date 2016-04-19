// extend the framework's module
define( [ cenozoApp.module( 'participant' ).getFileUrl( 'module.js' ) ], function() {
  'use strict';

  var module = cenozoApp.module( 'participant' );
  delete module.columnList.site;
  delete module.inputGroupList['Site & Contact Details'].default_site;
  delete module.inputGroupList['Site & Contact Details'].preferred_site_id;

} );
