// extend the framework's module
define( [ cenozoApp.module( 'participant' ).getFileUrl( 'module.js' ) ], function() {
  'use strict';

  var module = cenozoApp.module( 'participant' );
  delete module.columnList.site;
  delete module.inputGroupList['Site & Contact Details'].default_site;
  delete module.inputGroupList['Site & Contact Details'].preferred_site_id;

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnParticipantRelease', [
    'CnParticipantReleaseFactory',
    function( CnParticipantReleaseFactory ) {
      return {
        // look for the template in the application's path, not the framework
        templateUrl: cenozoApp.baseUrl + '/app/participant/release.tpl.html?build=' + cenozoApp.build,
        restrict: 'E', 
        controller: function( $scope ) {
          $scope.model = CnParticipantReleaseFactory.instance();
        }
      }
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnParticipantReleaseFactory', [
    'CnHttpFactory', '$state', '$q',
    function( CnHttpFactory, $state, $q ) {
      var object = function() {
        var self = this;
        this.module = module;
        this.isLoading = true;
        this.participant = angular.isDefined( $state.params.identifier ) && $state.params.identifier;
        this.applicationList = [];
        
        if( this.participant ) {
          // get the application list with respect to this participant
          CnHttpFactory.instance( {
            path: 'participant/' + $state.params.identifier + '/application',
            data: { select: { column: [
              'title', 'release_based', 'datetime', 'default_site_id', 'preferred_site_id'
            ] } }
          } ).get().then( function( response ) {
            self.applicationList = response.data;

            // get the site list for each application
            var promiseList = [];
            self.applicationList.forEach( function( application ) {
              if( null == application.preferred_site_id ) application.preferred_site_id = undefined;
              CnHttpFactory.instance( {
                path: 'application/' + application.id + '/site',
                data: { select: { column: [ 'name' ] } }
              } ).get().then( function( response ) {
                application.siteList = response.data;
                application.siteList.unshift( { id: undefined, name: '(none)' } );
              } );
            } );
            return $q.all( promiseList );
          } ).finally( function() { self.isLoading = false; } );
        }
      };

      return { instance: function() { return new object( false ); } };
    }
  ] );
      
} );
