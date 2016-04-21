// extend the framework's module
define( [ cenozoApp.module( 'application' ).getFileUrl( 'module.js' ) ], function() {
  'use strict';

  var module = cenozoApp.module( 'application' );
  module.addExtraOperation( 'list', {
    title: 'Release',
    operation: function( $state, model ) {
      if( 'participant' ==  model.getSubjectFromState() ) {
        $state.go( 'participant.release', $state.params );
      } else { // 'application' == model.getSubjectFromState()
        $state.go( 'participant.release' );
      }
    }
  } );

} );
