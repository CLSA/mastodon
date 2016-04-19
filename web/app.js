'use strict';

var cenozo = angular.module( 'cenozo' );

cenozo.controller( 'HeaderCtrl', [
  '$scope', '$state', 'CnBaseHeader', 'CnSession', 'CnHttpFactory', 'CnModalMessageFactory',
  function( $scope, $state, CnBaseHeader, CnSession, CnHttpFactory, CnModalMessageFactory ) {
    // copy all properties from the base header
    CnBaseHeader.construct( $scope );
  }
] );
