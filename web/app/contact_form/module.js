define( function() {
  'use strict';

  try { var module = cenozoApp.module( 'contact_form', true ); } catch( err ) { console.warn( err ); return; }
  angular.extend( module, {
    identifier: {},
    name: {
      singular: 'contact form',
      plural: 'contact forms',
      possessive: 'contact form\'s',
      pluralPossessive: 'contact forms\''
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
  cenozo.providers.directive( 'cnContactFormList', [
    'CnContactFormModelFactory',
    function( CnContactFormModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'list.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnContactFormModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnContactFormTree', [
    'CnContactFormTreeFactory', 'CnSession',
    function( CnContactFormTreeFactory, CnSession ) {
      return {
        templateUrl: module.getFileUrl( 'tree.tpl.html' ),
        restrict: 'E',
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnContactFormTreeFactory.instance();
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnContactFormView', [
    'CnContactFormModelFactory',
    function( CnContactFormModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'view.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnContactFormModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnContactFormListFactory', [
    'CnBaseListFactory',
    function( CnBaseListFactory ) {
      var object = function( parentModel ) { CnBaseListFactory.construct( this, parentModel ); };
      return { instance: function( parentModel ) { return new object( parentModel ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnContactFormViewFactory', [
    'CnBaseViewFactory',
    function( CnBaseViewFactory ) {
      var object = function( parentModel, root ) { CnBaseViewFactory.construct( this, parentModel, root ); };
      return { instance: function( parentModel, root ) { return new object( parentModel, root ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnContactFormModelFactory', [
    'CnBaseModelFactory', 'CnContactFormListFactory', 'CnContactFormViewFactory',
    function( CnBaseModelFactory, CnContactFormListFactory, CnContactFormViewFactory ) {
      var object = function( root ) {
        var self = this;
        CnBaseModelFactory.construct( this, module );
        this.listModel = CnContactFormListFactory.instance( this );
        this.viewModel = CnContactFormViewFactory.instance( this, root );
      };

      return {
        root: new object( true ),
        instance: function() { return new object( false ); }
      };
    }
  ] );

} );
