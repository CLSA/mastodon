define( function() {
  'use strict';

  try { var module = cenozoApp.module( 'hin_form_entry', true ); }
  catch( err ) { console.warn( err ); return; }

  cenozoApp.initFormEntryModule( module, 'hin' );

  module.addInputGroup( 'Details', {
    accept: {
      title: 'Accept',
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
  }, true );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnHinFormEntryList', [
    'CnHinFormEntryModelFactory',
    function( CnHinFormEntryModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'list.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnHinFormEntryModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnHinFormEntryView', [
    'CnHinFormEntryModelFactory',
    function( CnHinFormEntryModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'view.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnHinFormEntryModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnHinFormEntryListFactory', [
    'CnBaseFormEntryListFactory', 'CnModalMessageFactory',
    function( CnBaseFormEntryListFactory, CnModalMessageFactory ) {
      var object = function( parentModel ) { CnBaseFormEntryListFactory.construct( this, parentModel ); };
      return { instance: function( parentModel ) { return new object( parentModel ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnHinFormEntryViewFactory', [
    'CnBaseFormEntryViewFactory', 'CnModalMessageFactory', 'CnModalConfirmFactory',
    function( CnBaseFormEntryViewFactory, CnModalMessageFactory, CnModalConfirmFactory ) {
      var object = function( parentModel, root ) { CnBaseFormEntryViewFactory.construct( this, parentModel, root ); };
      return { instance: function( parentModel, root ) { return new object( parentModel, root ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnHinFormEntryModelFactory', [
    'CnBaseFormEntryModelFactory', 'CnHinFormEntryListFactory', 'CnHinFormEntryViewFactory',
    function( CnBaseFormEntryModelFactory, CnHinFormEntryListFactory, CnHinFormEntryViewFactory ) {
      var object = function( root ) {
        CnBaseFormEntryModelFactory.construct( this, module );
        this.listModel = CnHinFormEntryListFactory.instance( this );
        this.viewModel = CnHinFormEntryViewFactory.instance( this, root );
      };

      return {
        root: new object( true ),
        instance: function() { return new object( false ); }
      };
    }
  ] );

} );
