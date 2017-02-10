define( function() {
  'use strict';

  try { var module = cenozoApp.module( 'hin_form', true ); } catch( err ) { console.warn( err ); return; }

  cenozoApp.initFormModule( module, 'hin' );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnHinFormAdjudicate', [
    'CnHinFormAdjudicateFactory',
    function( CnHinFormAdjudicateFactory ) {
      return {
        // special general template found in application's general module directory
        templateUrl: module.getFileUrl( '../mastodon/adjudicate.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          $scope.model = CnHinFormAdjudicateFactory.instance();

          $scope.model.onLoad(); // breadcrumbs are handled by the service
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnHinFormList', [
    'CnHinFormModelFactory',
    function( CnHinFormModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'list.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnHinFormModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnHinFormView', [
    'CnHinFormModelFactory',
    function( CnHinFormModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'view.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnHinFormModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnHinFormAdjudicateFactory', [
    'CnBaseFormAdjudicateFactory',
    function( CnBaseFormAdjudicateFactory ) {
      var object = function( parentModel ) {
        this.formColumnList = [ {
          column: 'uid',
          title: 'UID'
        }, {
          column: 'accept',
          title: 'Accept'
        }, {
          column: 'signed',
          title: 'Signed'
        }, {
          column: 'date',
          title: 'Date'
        } ];
        CnBaseFormAdjudicateFactory.construct( this, module );
      };
      return { instance: function( parentModel ) { return new object( parentModel ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnHinFormListFactory', [
    'CnBaseListFactory',
    function( CnBaseListFactory ) {
      var object = function( parentModel ) { CnBaseListFactory.construct( this, parentModel ); };
      return { instance: function( parentModel ) { return new object( parentModel ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnHinFormViewFactory', [
    'CnBaseFormViewFactory',
    function( CnBaseFormViewFactory ) {
      var object = function( parentModel, root ) { CnBaseFormViewFactory.construct( this, parentModel, root ); };
      return { instance: function( parentModel, root ) { return new object( parentModel, root ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnHinFormModelFactory', [
    'CnBaseFormModelFactory', 'CnHinFormListFactory', 'CnHinFormViewFactory',
    function( CnBaseFormModelFactory, CnHinFormListFactory, CnHinFormViewFactory ) {
      var object = function( root ) {
        var self = this;
        CnBaseFormModelFactory.construct( this, module );
        this.listModel = CnHinFormListFactory.instance( this );
        this.viewModel = CnHinFormViewFactory.instance( this, root );
      };

      return {
        root: new object( true ),
        instance: function() { return new object( false ); }
      };
    }
  ] );

} );
