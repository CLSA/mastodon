define( function() {
  'use strict';

  try { var module = cenozoApp.module( 'proxy_form', true ); } catch( err ) { console.warn( err ); return; }

  cenozoApp.initFormModule( module, 'proxy' );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnProxyFormAdjudicate', [
    'CnProxyFormAdjudicateFactory',
    function( CnProxyFormAdjudicateFactory ) {
      return {
        // special general template found in application's general module directory
        templateUrl: module.getFileUrl( '../mastodon/adjudicate.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          $scope.model = CnProxyFormAdjudicateFactory.instance();

          $scope.model.onLoad(); // breadcrumbs are handled by the service
        }
      };
    }
  ] );

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
  cenozo.providers.factory( 'CnProxyFormAdjudicateFactory', [
    'CnBaseFormAdjudicateFactory',
    function( CnBaseFormAdjudicateFactory ) {
      var object = function( parentModel ) {
        this.formColumnList = [ {
          column: 'uid',
          title: 'UID'
        }, {
          column: 'proxy',
          title: 'Proxy'
        }, {
          column: 'already_identified',
          title: 'Already Identified'
        }, {
          column: 'proxy_first_name',
          title: 'Proxy First Name'
        }, {
          column: 'proxy_last_name',
          title: 'Proxy Last Name'
        }, {
          column: 'proxy_apartment_number',
          title: 'Proxy Apartment Number'
        }, {
          column: 'proxy_street_number',
          title: 'Proxy Street Number'
        }, {
          column: 'proxy_street_name',
          title: 'Proxy Street Name'
        }, {
          column: 'proxy_box',
          title: 'Proxy Box'
        }, {
          column: 'proxy_rural_route',
          title: 'Proxy Rural Route'
        }, {
          column: 'proxy_address_other',
          title: 'Proxy Address Other'
        }, {
          column: 'proxy_city',
          title: 'Proxy City'
        }, {
          column: 'proxy_region',
          title: 'Proxy Region'
        }, {
          column: 'proxy_postcode',
          title: 'Proxy Postcode'
        }, {
          column: 'proxy_address_note',
          title: 'Proxy Address Note'
        }, {
          column: 'proxy_phone',
          title: 'Proxy Phone'
        }, {
          column: 'proxy_phone_note',
          title: 'Proxy Phone Note'
        }, {
          column: 'proxy_note',
          title: 'Proxy Note'
        }, {
          column: 'informant',
          title: 'Informant'
        }, {
          column: 'same_as_proxy',
          title: 'Same As Proxy'
        }, {
          column: 'informant_first_name',
          title: 'Informant First Name'
        }, {
          column: 'informant_last_name',
          title: 'Informant Last Name'
        }, {
          column: 'informant_apartment_number',
          title: 'Informant Apartment Number'
        }, {
          column: 'informant_street_number',
          title: 'Informant Street Number'
        }, {
          column: 'informant_street_name',
          title: 'Informant Street Name'
        }, {
          column: 'informant_box',
          title: 'Informant Box'
        }, {
          column: 'informant_rural_route',
          title: 'Informant Rural Route'
        }, {
          column: 'informant_address_other',
          title: 'Informant Address Other'
        }, {
          column: 'informant_city',
          title: 'Informant City'
        }, {
          column: 'informant_region',
          title: 'Informant Region'
        }, {
          column: 'informant_postcode',
          title: 'Informant Postcode'
        }, {
          column: 'informant_address_note',
          title: 'Informant Address Note'
        }, {
          column: 'informant_phone',
          title: 'Informant Phone'
        }, {
          column: 'informant_phone_note',
          title: 'Informant Phone Note'
        }, {
          column: 'informant_note',
          title: 'Informant Note'
        }, {
          column: 'informant_continue',
          title: 'Informant Continue'
        }, {
          column: 'health_card',
          title: 'Health Card'
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
  cenozo.providers.factory( 'CnProxyFormListFactory', [
    'CnBaseListFactory',
    function( CnBaseListFactory ) {
      var object = function( parentModel ) { CnBaseListFactory.construct( this, parentModel ); };
      return { instance: function( parentModel ) { return new object( parentModel ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnProxyFormViewFactory', [
    'CnBaseFormViewFactory',
    function( CnBaseFormViewFactory ) {
      var object = function( parentModel, root ) { CnBaseFormViewFactory.construct( this, parentModel, root ); };
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
