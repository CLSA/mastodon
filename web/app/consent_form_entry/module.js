define( function() {
  'use strict';

  try { var module = cenozoApp.module( 'consent_form_entry', true ); } catch( err ) { console.warn( err ); return; }
  angular.extend( module, {
    identifier: {
      parent: {
        subject: 'consent_form',
        column: 'consent_form.id'
      }
    },
    name: {
      singular: 'consent form entry',
      plural: 'consent form entries',
      possessive: 'consent form entry\'s',
      pluralPossessive: 'consent form entries\''
    },
    columnList: {
      consent_form_id: {
        column: 'consent_form_id',
        title: 'ID'
      },
      user: {
        column: 'user.name',
        title: 'User'
      },
      deferred: {
        title: 'Deferred',
        type: 'boolean'
      },
      validated: {
        title: 'Validated',
        type: 'boolean'
      },
      date: {
        column: 'consent_form.date',
        title: 'Date Added',
        type: 'date'
      }
    },
    defaultOrder: {
      column: 'user.name',
      reverse: false
    }
  } );

  module.addInputGroup( null, {
    user_id: {
      title: 'User',
      type: 'lookup-typeahead',
      typeahead: {
        table: 'user',
        select: 'CONCAT( first_name, " ", last_name, " (", name, ")" )',
        where: [ 'first_name', 'last_name', 'name' ]
      }
    },
    deferred: {
      title: 'Deferred',
      type: 'boolean'
    },
    uid: {
      title: 'UID',
      type: 'string',
      regex: '^[A-Z][0-9]{6}$',
      help: 'Must be in "A000000" format (a letter followed by 6 numbers)'
    },
    option_1: {
      title: 'Option #1',
      type: 'boolean'
    },
    option_2: {
      title: 'Option #2',
      type: 'boolean'
    },
    signed: {
      title: 'Signed',
      type: 'boolean'
    },
    date: {
      title: 'Date',
      type: 'date'
    }
  } );

  if( angular.isDefined( module.actions.start ) ) {
    module.addExtraOperation( 'list', {
      title: 'Start New Entry',
      operation: function( $state, model ) { model.listModel.startNewEntry(); }
    } );
  }

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnConsentFormEntryList', [
    'CnConsentFormEntryModelFactory',
    function( CnConsentFormEntryModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'list.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnConsentFormEntryModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnConsentFormEntryTree', [
    'CnConsentFormEntryTreeFactory', 'CnSession',
    function( CnConsentFormEntryTreeFactory, CnSession ) {
      return {
        templateUrl: module.getFileUrl( 'tree.tpl.html' ),
        restrict: 'E',
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnConsentFormEntryTreeFactory.instance();
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnConsentFormEntryView', [
    'CnConsentFormEntryModelFactory',
    function( CnConsentFormEntryModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'view.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnConsentFormEntryModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnConsentFormEntryListFactory', [
    'CnBaseListFactory', 'CnSession', 'CnHttpFactory', 'CnModalMessageFactory', '$state',
    function( CnBaseListFactory, CnSession, CnHttpFactory, CnModalMessageFactory, $state ) {
      var object = function( parentModel ) {
        CnBaseListFactory.construct( this, parentModel );

        this.startNewEntry = function() {
          CnHttpFactory.instance( {
            path: 'consent_form_entry',
            data: { user_id: CnSession.user.id },
            onError: function( response ) {
              if( XXX == response.status ) {
                CnModalMessageFactory.instance( {
                  title: 'No Forms Available',
                  message: 'There are no new consent forms available for transcription at this time.'
                } ).show();
              } else { CnModalMessageFactory.httpError( response ); }
            }
          } ).post().then( function( response ) {
            $state.go( 'consent_form_entry.view', { identifier: response } );
          } );
        };
      };
      return { instance: function( parentModel ) { return new object( parentModel ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnConsentFormEntryViewFactory', [
    'CnBaseViewFactory',
    function( CnBaseViewFactory ) {
      var object = function( parentModel, root ) { CnBaseViewFactory.construct( this, parentModel, root ); };
      return { instance: function( parentModel, root ) { return new object( parentModel, root ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnConsentFormEntryModelFactory', [
    'CnBaseModelFactory', 'CnConsentFormEntryListFactory', 'CnConsentFormEntryViewFactory', 'CnSession',
    function( CnBaseModelFactory, CnConsentFormEntryListFactory, CnConsentFormEntryViewFactory, CnSession ) {
      var object = function( root ) {
        var self = this;
        CnBaseModelFactory.construct( this, module );
        this.listModel = CnConsentFormEntryListFactory.instance( this );
        this.viewModel = CnConsentFormEntryViewFactory.instance( this, root );

        CnSession.promise.then( function() {
          if( 'typist' == CnSession.role.name ) {
            module.columnList.user.type = 'hidden';
            module.columnList.deferred.type = 'hidden';
            module.columnList.validated.type = 'hidden';
          }
        } );
      };

      return {
        root: new object( true ),
        instance: function() { return new object( false ); }
      };
    }
  ] );

} );
