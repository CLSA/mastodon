// extend the framework's module
define( [ cenozoApp.module( 'application' ).getFileUrl( 'module.js' ) ], function() {
  'use strict';

  var module = cenozoApp.module( 'application' );

  if( angular.isDefined( cenozoApp.module( 'participant' ).actions.release ) ) {
    module.addExtraOperation( 'list', {
      title: 'Manage Applications',
      isIncluded: function( $state, model ) { return 'participant' == model.getSubjectFromState(); },
      operation: function( $state, model ) { $state.go( 'participant.release', $state.params ); }
    } );
  }

  if( angular.isDefined( module.actions.release ) ) {
    module.addExtraOperation( 'view', {
      title: 'Manage Participants',
      operation: function( $state, model ) { $state.go( 'application.release', $state.params ); },
      isIncluded: function( $state, model ) { return model.viewModel.record.release_based; }
    } );
  }

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnApplicationRelease', [
    'CnApplicationReleaseFactory', '$timeout',
    function( CnApplicationReleaseFactory, $timeout ) {
      return {
        // look for the template in the application's path, not the framework
        templateUrl: cenozoApp.baseUrl + '/app/application/release.tpl.html?build=' + cenozoApp.build,
        restrict: 'E',
        controller: function( $scope ) {
          $scope.model = CnApplicationReleaseFactory.instance();
          // breadcrumbs are handled by the service
        }
      }
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnApplicationReleaseFactory', [
    'CnSession', 'CnHttpFactory', 'CnParticipantSelectionFactory', 'CnModalMessageFactory', '$state', '$q',
    function( CnSession, CnHttpFactory, CnParticipantSelectionFactory, CnModalMessageFactory, $state, $q ) {
      var object = function() {
        var self = this;
        this.application = null;
        this.preferredSiteId = null;
        this.applicationSiteList = [];
        this.participantSelection = CnParticipantSelectionFactory.instance();

        this.reset = function() {
          self.participantSelection.reset();
          self.cohortSiteList = null;
          self.preferredSiteId = null;
        };
        this.reset();

        // get the application details and set up the breadcrumb trail
        CnHttpFactory.instance( {
          path: 'application/' + $state.params.identifier,
          data: { select: { column: [ 'title', 'release_based' ] } }
        } ).get().then( function( response ) {
          self.application = response.data;
          self.application.identifier = $state.params.identifier;

          // Make modifications to the standard participant selection service
          // This is required because Mastodon extends the service by adding a site-list as well as identifier-list
          angular.extend( self.participantSelection, {
            data: { mode: 'unreleased_only', application_id: self.application.id },
            responseFn: function( model, response ) {
              model.confirmedCount = response.data.identifier_list.length;
              model.identifierListString = response.data.identifier_list.join( ' ' );
              model.confirmInProgress = false;
              self.cohortSiteList = response.data.site_list;
            }
          } );

          // immediately send a 404 if this application is not release-based
          if( !self.application.release_based ) $state.go( 'error.404' );
          else {
            CnSession.setBreadcrumbTrail( [ {
              title: 'Applications',
              go: function() { $state.go( 'application.list' ); }
            }, {
              title: response.data.title,
              go: function() { $state.go( 'application.view', { identifier: $state.params.identifier } ); }
            }, {
              title: 'Release'
            } ] );
          }
        } );

        // get the application's site list
        CnHttpFactory.instance( {
          path: 'application/' + $state.params.identifier + '/site',
          data: { select: { column: [ 'name' ] } }
        } ).get().then( function( response ) {
          self.applicationSiteList = response.data;
          response.data.unshift( { id: null, name: 'No Preferred Site' } );
        } );

        this.release = function() {
          if( !this.participantSelection.confirmInProgress && 0 < this.participantSelection.confirmedCount ) {
            CnHttpFactory.instance( {
              path: 'participant',
              data: {
                mode: 'release',
                application_id: self.application.id,
                site_id: this.preferredSiteId,
                identifier_id: self.participantSelection.identifierId,
                identifier_list: this.participantSelection.getIdentifierList()
              }
            } ).post().then( function( response ) {
              CnModalMessageFactory.instance( {
                title: 'Participants Released',
                message: 'You have successfully released ' + self.participantSelection.confirmedCount + ' participants to ' +
                         self.application.title
              } ).show().then( function() { self.reset(); } );
            } );
          }
        };
      };

      return { instance: function() { return new object( false ); } };
    }
  ] );

} );
