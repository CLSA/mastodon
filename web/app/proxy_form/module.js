define( function() {
  'use strict';

  try { var module = cenozoApp.module( 'proxy_form', true ); } catch( err ) { console.warn( err ); return; }
  angular.extend( module, {
    identifier: {},
    name: {
      singular: 'proxy form',
      plural: 'proxy forms',
      possessive: 'proxy form\'s',
      pluralPossessive: 'proxy forms\''
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
  cenozo.providers.directive( 'cnProxyFormList', [
    'CnProxyFormModelFactory',
    function( CnProxyFormModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'list.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnProxyFormModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnProxyFormView', [
    'CnProxyFormModelFactory',
    function( CnProxyFormModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'view.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnProxyFormModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnProxyFormListFactory', [
    'CnBaseListFactory',
    function( CnBaseListFactory ) {
      var object = function( parentModel ) { CnBaseListFactory.construct( this, parentModel ); };
      return { instance: function( parentModel ) { return new object( parentModel ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnProxyFormViewFactory', [
    'CnBaseViewFactory',
    function( CnBaseViewFactory ) {
      var object = function( parentModel, root ) { CnBaseViewFactory.construct( this, parentModel, root ); };
      return { instance: function( parentModel, root ) { return new object( parentModel, root ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnProxyFormModelFactory', [
    'CnBaseModelFactory', 'CnProxyFormListFactory', 'CnProxyFormViewFactory',
    function( CnBaseModelFactory, CnProxyFormListFactory, CnProxyFormViewFactory ) {
      var object = function( root ) {
        var self = this;
        CnBaseModelFactory.construct( this, module );
        this.listModel = CnProxyFormListFactory.instance( this );
        this.viewModel = CnProxyFormViewFactory.instance( this, root );
      };

      return {
        root: new object( true ),
        instance: function() { return new object( false ); }
      };
    }
  ] );

} );
