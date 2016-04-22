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
      operation: function( $state, model ) { $state.go( 'application.release', $state.params ); }
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
        this.confirmInProgress = false;
        this.confirmedCount = null;
        this.uidList = '';

        // set up the breadcrumb trail
        CnHttpFactory.instance( {
          path: 'application/' + $state.params.identifier,
          data: { select: { column: [ 'title' ] } }
        } ).get().then( function( response ) {
          self.application = response.data;
          self.application.identifier = $state.params.identifier;
          CnSession.setBreadcrumbTrail( [ {
            title: 'Application',
            go: function() { $state.go( 'application.list' ); }
          }, {
            title: response.data.title,
            go: function() { $state.go( 'application.view', { identifier: $state.params.identifier } ); }
          }, {
            title: 'Release'
          } ] );
        } );

        this.uidListChanged = function() {
          this.confirmedCount = null;
        };

        this.confirm = function() {
          this.confirmInProgress = true;
          this.confirmedCount = null;

          // clean up the uid list
          var fixedList =
            this.uidList.toUpperCase() // convert to uppercase
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
          if( 0 == fixedList.length ) {
            self.uidList = '';
            self.confirmInProgress = false;
          } else {
            CnHttpFactory.instance( {
              path: 'participant',
              data: { application_id: self.application.id, uid_list: fixedList }
            } ).post().then( function( response ) {
              self.confirmedCount = response.data.length;
              self.uidList = response.data.join( ' ' );
              self.confirmInProgress = false;
            } );
          }
        };
      };

      return { instance: function() { return new object( false ); } };
    }
  ] );
      
} );
