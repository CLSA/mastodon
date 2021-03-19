define( function() {
  'use strict';

  try { var module = cenozoApp.module( 'proxy_consent_form', true ); } catch( err ) { console.warn( err ); return; }

  cenozoApp.initFormModule( module, 'proxy_consent' );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnProxyConsentFormAdjudicate', [
    'CnProxyConsentFormAdjudicateFactory',
    function( CnProxyConsentFormAdjudicateFactory ) {
      return {
        // special general template found in application's general module directory
        templateUrl: module.getFileUrl( '../mastodon/adjudicate.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          $scope.model = CnProxyConsentFormAdjudicateFactory.instance();

          $scope.model.onLoad(); // breadcrumbs are handled by the service
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnProxyConsentFormList', [
    'CnProxyConsentFormModelFactory',
    function( CnProxyConsentFormModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'list.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnProxyConsentFormModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnProxyConsentFormView', [
    'CnProxyConsentFormModelFactory',
    function( CnProxyConsentFormModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'view.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnProxyConsentFormModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnProxyConsentFormAdjudicateFactory', [
    'CnBaseFormAdjudicateFactory',
    function( CnBaseFormAdjudicateFactory ) {
      var object = function( parentModel ) {
        this.formColumnList = [ {
          column: 'uid',
          title: 'UID'
        }, {
          column: 'accept',
          title: 'Consent to Act as Proxy'
        }, {
          column: 'type',
          title: 'Type of Proxy'
        }, {
          column: 'alternate_id',
          title: 'Proxy'
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
  cenozo.providers.factory( 'CnProxyConsentFormListFactory', [
    'CnBaseListFactory',
    function( CnBaseListFactory ) {
      var object = function( parentModel ) { CnBaseListFactory.construct( this, parentModel ); };
      return { instance: function( parentModel ) { return new object( parentModel ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnProxyConsentFormViewFactory', [
    'CnBaseFormViewFactory',
    function( CnBaseFormViewFactory ) {
      var object = function( parentModel, root ) { CnBaseFormViewFactory.construct( this, parentModel, root ); };
      return { instance: function( parentModel, root ) { return new object( parentModel, root ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnProxyConsentFormModelFactory', [
    'CnBaseFormModelFactory', 'CnProxyConsentFormListFactory', 'CnProxyConsentFormViewFactory',
    function( CnBaseFormModelFactory, CnProxyConsentFormListFactory, CnProxyConsentFormViewFactory ) {
      var object = function( root ) {
        var self = this;
        CnBaseFormModelFactory.construct( this, module );
        this.listModel = CnProxyConsentFormListFactory.instance( this );
        this.viewModel = CnProxyConsentFormViewFactory.instance( this, root );
      };

      return {
        root: new object( true ),
        instance: function() { return new object( false ); }
      };
    }
  ] );

} );
