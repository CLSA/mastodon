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

          $scope.model.onLoad(); // breadcrumbs are handled by the service
        }
      }
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnParticipantReleaseFactory', [
    'CnSession', 'CnHttpFactory', 'CnModalMessageFactory', '$state', '$q',
    function( CnSession, CnHttpFactory, CnModalMessageFactory, $state, $q ) {
      var object = function() {
        var self = this;
        this.participant = angular.isDefined( $state.params.identifier ) && $state.params.identifier;
        if( this.participant ) {
          // set up the breadcrumb trail
          CnHttpFactory.instance( {
            path: 'participant/' + this.participant,
            data: { select: { column: [ 'uid' ] } }
          } ).get().then( function( response ) {
            CnSession.setBreadcrumbTrail( [ {
              title: 'Participant',
              go: function() { $state.go( 'participant.list' ); }
            }, {
              title: response.data.uid,
              go: function() { $state.go( 'participant.view', { identifier: self.participant } ); }
            }, {
              title: 'Release'
            } ] );
          } );

          this.releaseParticipant = function( application ) {
            // TODO: implement
          };

          this.setPreferredSite = function( application ) {
            var site = application.siteList.findByProperty( 'id', application.preferred_site_id );

            // get the new site
            CnHttpFactory.instance( {
              path: 'participant/' + this.participant,
              data: {
                application_id: application.id,
                preferred_site_id: angular.isDefined( site.id ) ? site.id : null
              },
              onError: function( response ) {
                CnModalMessageFactory.instance( {
                  title: 'Unable To Set Preferred Site',
                  message: 'There was a problem while trying to set the participant\'s preferred site for ' +
                           application.title + ' to ' + ( angular.isDefined( site.id ) ? site.name : 'no site' ),
                  error: true
                } ).show();
              }
            } ).patch();
          };
        }

        this.reset = function() {
          this.isLoading = false;
          this.applicationList = [];
        };

        this.reset();

        this.onLoad = function() {
          self.reset();
          self.isLoading = true;
          var promise = null;
          if( this.participant ) {
            // get the application list with respect to this participant
            promise = CnHttpFactory.instance( {
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
          } else {
            promise = $q.all().finally( function() { self.isLoading = false; } );
          }

          return promise;
        };
      };

      return { instance: function() { return new object( false ); } };
    }
  ] );
      
} );
