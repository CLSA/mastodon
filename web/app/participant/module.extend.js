// extend the framework's module
define( [ cenozoApp.module( 'participant' ).getFileUrl( 'module.js' ) ], function() {
  'use strict';

  var module = cenozoApp.module( 'participant' );
  delete module.columnList.site;
  var index = module.inputGroupList.findIndexByProperty( 'title', 'Site & Contact Details' );
  if( null != index ) {
    var inputGroup = module.inputGroupList[index];
    inputGroup.title = 'Contact Details';
    delete inputGroup.inputList.default_site;
    delete inputGroup.inputList.preferred_site_id;
  }

  module.addExtraOperation( 'view', {
    title: 'Download Opal Forms',
    isIncluded: function( $state, model ) {
      return angular.isDefined( model.viewModel.downloadOpalForms );
    },
    operation: function( $state, model ) {
      return model.viewModel.downloadOpalForms();
    }
  } );

  // extend the view factory
  cenozo.providers.decorator( 'CnParticipantViewFactory', [
    '$delegate', 'CnSession', 'CnHttpFactory', 'CnModalMessageFactory',
    function( $delegate, CnSession, CnHttpFactory, CnModalMessageFactory ) { 
      var instance = $delegate.instance;
      $delegate.instance = function( parentModel, root ) { 
        var object = instance( parentModel, root );
        if( 3 <= CnSession.role.tier ) {
          object.downloadOpalForms = function() {
            var modal = CnModalMessageFactory.instance( {
              title: 'Please Wait',
              message: 'Please wait while the participant\'s data is retrieved from Opal.',
              block: true
            } );
            modal.show();

            return CnHttpFactory.instance( {
              path: 'participant/' + object.record.getIdentifier() + '?opal_forms=1',
              format: 'zip'
            } ).file().finally( function() { modal.close(); } );
          };
        }
        return object;
      };  
      return $delegate;
    }   
  ] );

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
        this.participant = null;

        // set up the breadcrumb trail
        this.promise = CnHttpFactory.instance( {
          path: 'participant/' + $state.params.identifier,
          data: { select: { column: [ 'uid' ] } }
        } ).get().then( function( response ) {
          self.participant = response.data;
          self.participant.identifier = $state.params.identifier;
          CnSession.setBreadcrumbTrail( [ {
            title: 'Participants',
            go: function() { $state.go( 'participant.list' ); }
          }, {
            title: response.data.uid,
            go: function() { $state.go( 'participant.view', { identifier: $state.params.identifier } ); }
          }, {
            title: 'Release'
          } ] );
        } );

        this.viewParticipant = function() {
          $state.go( 'participant.view', { identifier: $state.params.identifier } );
        };

        this.releaseParticipant = function( application ) {
          self.promise.then( function() {
            CnHttpFactory.instance( {
              path: 'application/' + application.id + '/participant',
              data: self.participant.id
            } ).post().then( function() {
              application.datetime = moment().format();
            } );
          } );
        };

        this.setPreferredSite = function( application ) {
          self.promise.then( function() {
            // get the new site
            var site = application.siteList.findByProperty( 'id', application.preferred_site_id );

            CnHttpFactory.instance( {
              path: 'participant/' + $state.params.identifier,
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
          } );
        };

        this.reset = function() {
          self.isLoading = false;
          self.applicationList = [];
        };
        this.reset();

        this.onLoad = function() {
          return self.promise.then( function() {
            self.reset();
            self.isLoading = true;

            // get the application list with respect to this participant
            return CnHttpFactory.instance( {
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
                promiseList.push(
                  CnHttpFactory.instance( {
                    path: 'application/' + application.id + '/site',
                    data: { select: { column: [ 'name' ] } }
                  } ).get().then( function( response ) {
                    application.siteList = response.data;
                    application.siteList.unshift( { id: undefined, name: '(none)' } );
                  } )
                );
              } );
              return $q.all( promiseList );
            } ).finally( function() { self.isLoading = false; } );
          } );
        };
      };

      return { instance: function() { return new object( false ); } };
    }
  ] );

} );
