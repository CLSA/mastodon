define( function() {
  'use strict';

  try { var module = cenozoApp.module( 'consent_form', true ); } catch( err ) { console.warn( err ); return; }
  angular.extend( module, {
    identifier: {},
    name: {
      singular: 'consent form',
      plural: 'consent forms',
      possessive: 'consent form\'s',
      pluralPossessive: 'consent forms\''
    },
    columnList: {
      id: {
        title: 'ID'
      },
      complete: {
        title: 'Complete',
        type: 'boolean'
      },
      invalid: {
        title: 'Invalid',
        type: 'boolean'
      },
      date: {
        title: 'Date',
        type: 'date'
      }
    },
    defaultOrder: {
      column: 'date',
      reverse: true
    }
  } );

  module.addInputGroup( null, {
    id: {
      title: 'ID',
      type: 'string',
      format: 'integer'
    },
    complete: {
      title: 'Complete',
      type: 'boolean'
    },
    invalid: {
      title: 'Invalid',
      type: 'boolean'
    },
    date: {
      title: 'Date',
      type: 'date'
    }
  } );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnConsentFormList', [
    'CnConsentFormModelFactory',
    function( CnConsentFormModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'list.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnConsentFormModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnConsentFormTree', [
    'CnConsentFormTreeFactory', 'CnSession',
    function( CnConsentFormTreeFactory, CnSession ) {
      return {
        templateUrl: module.getFileUrl( 'tree.tpl.html' ),
        restrict: 'E',
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnConsentFormTreeFactory.instance();
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnConsentFormView', [
    'CnConsentFormModelFactory',
    function( CnConsentFormModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'view.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnConsentFormModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnConsentFormListFactory', [
    'CnBaseListFactory',
    function( CnBaseListFactory ) {
      var object = function( parentModel ) { CnBaseListFactory.construct( this, parentModel ); };
      return { instance: function( parentModel ) { return new object( parentModel ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnConsentFormViewFactory', [
    'CnBaseViewFactory',
    function( CnBaseViewFactory ) {
      var object = function( parentModel, root ) { CnBaseViewFactory.construct( this, parentModel, root ); };
      return { instance: function( parentModel, root ) { return new object( parentModel, root ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnConsentFormModelFactory', [
    'CnBaseModelFactory', 'CnConsentFormListFactory', 'CnConsentFormViewFactory',
    function( CnBaseModelFactory, CnConsentFormListFactory, CnConsentFormViewFactory ) {
      var object = function( root ) {
        var self = this;
        CnBaseModelFactory.construct( this, module );
        this.listModel = CnConsentFormListFactory.instance( this );
        this.viewModel = CnConsentFormViewFactory.instance( this, root );
      };

      return {
        root: new object( true ),
        instance: function() { return new object( false ); }
      };
    }
  ] );

} );
