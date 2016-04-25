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
      title: 'Release Participants',
      operation: function( $state, model ) { $state.go( 'application.release', $state.params ); },
      isIncluded: function( $state, model ) { return model.viewModel.record.release_based; }
    } );
  }

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnApplicationRelease', [
    'CnApplicationReleaseFactory',
    function( CnApplicationReleaseFactory ) {
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
    'CnSession', 'CnHttpFactory', 'CnModalMessageFactory', '$state', '$q',
    function( CnSession, CnHttpFactory, CnModalMessageFactory, $state, $q ) {
      var object = function() {
        var self = this;
        this.application = null;

        this.reset = function() {
          self.confirmInProgress = false;
          self.confirmedCount = null;
          self.uidListString = '';
          self.uidList = [];
          self.cohortSiteList = null;
        };
        this.reset();

        // set up the breadcrumb trail
        CnHttpFactory.instance( {
          path: 'application/' + $state.params.identifier,
          data: { select: { column: [ 'title', 'release_based' ] } }
        } ).get().then( function( response ) {
          self.application = response.data;
          self.application.identifier = $state.params.identifier;
          
          // immediately send a 404 if this application is not release-based
          if( !self.application.release_based ) $state.go( 'error.404' ); 
          else {
            CnSession.setBreadcrumbTrail( [ {
              title: 'Application',
              go: function() { $state.go( 'application.list' ); }
            }, {
              title: response.data.title,
              go: function() { $state.go( 'application.view', { identifier: $state.params.identifier } ); }
            }, {
              title: 'Release'
            } ] );
          }
        } );

        this.uidListStringChanged = function() {
          this.confirmedCount = null;
        };

        this.confirm = function() {
          this.confirmInProgress = true;
          this.confirmedCount = null;

          // clean up the uid list
          this.uidList =
            this.uidListString.toUpperCase() // convert to uppercase
                        .replace( /[\s,;|\/]/g, ' ' ) // replace whitespace and separation chars with a space
                        .replace( /[^a-zA-Z0-9 ]/g, '' ) // remove anything that isn't a letter, number of space
                        .split( ' ' ) // delimite string by spaces and create array from result
                        .filter( function( uid ) { // match UIDs (eg: A123456)
                          return null != uid.match( /^[A-Z][0-9]{6}$/ );
                        } )
                        .filter( function( uid, index, array ) { // make array unique
                          return index <= array.indexOf( uid );
                        } )
                        .sort(); // sort the array

          // now confirm UID list with server
          if( 0 == this.uidList.length ) {
            this.uidListString = '';
            this.confirmInProgress = false;
          } else {
            CnHttpFactory.instance( {
              path: 'participant',
              data: {
                mode: 'unreleased_only',
                application_id: this.application.id,
                uid_list: this.uidList
              }
            } ).post().then( function( response ) {
              self.confirmedCount = response.data.uid_list.length;
              self.uidListString = response.data.uid_list.join( ' ' );
              self.cohortSiteList = response.data.site_list;
              self.confirmInProgress = false;
            } );
          }
        };

        this.release = function() {
          if( !this.confirmInProgress && 0 < this.confirmedCount ) {
            CnHttpFactory.instance( {
              path: 'participant',
              data: {
                mode: 'release',
                application_id: self.application.id,
                uid_list: this.uidList
              }
            } ).post().then( function( response ) {
              CnModalMessageFactory.instance( {
                title: 'Participants Released',
                message: 'You have successfully released ' + self.confirmedCount + ' participants to ' +
                         self.application.title
              } ).show().then( self.reset );
            } );
          }
        };
      };

      return { instance: function() { return new object( false ); } };
    }
  ] );
      
} );
