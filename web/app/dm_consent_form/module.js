define( function() {
  'use strict';

  try { var module = cenozoApp.module( 'dm_consent_form', true ); } catch( err ) { console.warn( err ); return; }

  cenozoApp.initFormModule( module, 'dm_consent' );

  // give these forms a special name
  angular.extend( module.name, {
    singular: 'decision maker form',
    plural: 'decision maker forms',
    possessive: 'decision maker form\'s'
  } );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnDmConsentFormAdjudicate', [
    'CnDmConsentFormAdjudicateFactory',
    function( CnDmConsentFormAdjudicateFactory ) {
      return {
        // special general template found in application's general module directory
        templateUrl: module.getFileUrl( '../mastodon/adjudicate.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          $scope.model = CnDmConsentFormAdjudicateFactory.instance();

          $scope.model.onLoad(); // breadcrumbs are handled by the service
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnDmConsentFormList', [
    'CnDmConsentFormModelFactory',
    function( CnDmConsentFormModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'list.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnDmConsentFormModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnDmConsentFormView', [
    'CnDmConsentFormModelFactory',
    function( CnDmConsentFormModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'view.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnDmConsentFormModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnDmConsentFormAdjudicateFactory', [
    'CnBaseFormAdjudicateFactory',
    function( CnBaseFormAdjudicateFactory ) {
      var object = function( parentModel ) {
        this.formColumnList = [ {
          column: 'uid',
          title: 'UID'
        }, {
          column: 'accept',
          title: 'Consent to Act as Decision Maker'
        }, {
          column: 'alternate_id',
          title: 'Alternate'
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
  cenozo.providers.factory( 'CnDmConsentFormListFactory', [
    'CnBaseListFactory',
    function( CnBaseListFactory ) {
      var object = function( parentModel ) { CnBaseListFactory.construct( this, parentModel ); };
      return { instance: function( parentModel ) { return new object( parentModel ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnDmConsentFormViewFactory', [
    'CnBaseFormViewFactory',
    function( CnBaseFormViewFactory ) {
      var object = function( parentModel, root ) { CnBaseFormViewFactory.construct( this, parentModel, root ); };
      return { instance: function( parentModel, root ) { return new object( parentModel, root ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnDmConsentFormModelFactory', [
    'CnBaseFormModelFactory', 'CnDmConsentFormListFactory', 'CnDmConsentFormViewFactory',
    function( CnBaseFormModelFactory, CnDmConsentFormListFactory, CnDmConsentFormViewFactory ) {
      var object = function( root ) {
        var self = this;
        CnBaseFormModelFactory.construct( this, module );
        this.listModel = CnDmConsentFormListFactory.instance( this );
        this.viewModel = CnDmConsentFormViewFactory.instance( this, root );
      };

      return {
        root: new object( true ),
        instance: function() { return new object( false ); }
      };
    }
  ] );

} );
