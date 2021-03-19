define( function() {
  'use strict';

  try { var module = cenozoApp.module( 'proxy_consent_form_entry', true ); }
  catch( err ) { console.warn( err ); return; }

  cenozoApp.initFormEntryModule( module, 'proxy_consent' );

  module.addInputGroup( 'Details', {
    accept: {
      title: 'Consent to Act as Proxy',
      type: 'boolean'
    },
    type: {
      title: 'Type of Proxy',
      type: 'enum'
    },
    alternate_id: {
      title: 'Proxy',
      type: 'enum'
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
  cenozo.providers.directive( 'cnProxyConsentFormEntryList', [
    'CnProxyConsentFormEntryModelFactory',
    function( CnProxyConsentFormEntryModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'list.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnProxyConsentFormEntryModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnProxyConsentFormEntryView', [
    'CnProxyConsentFormEntryModelFactory',
    function( CnProxyConsentFormEntryModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'view.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnProxyConsentFormEntryModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnProxyConsentFormEntryListFactory', [
    'CnBaseFormEntryListFactory', 'CnModalMessageFactory',
    function( CnBaseFormEntryListFactory, CnModalMessageFactory ) {
      var object = function( parentModel ) { CnBaseFormEntryListFactory.construct( this, parentModel ); };
      return { instance: function( parentModel ) { return new object( parentModel ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnProxyConsentFormEntryViewFactory', [
    'CnBaseFormEntryViewFactory', 'CnModalMessageFactory', 'CnModalConfirmFactory',
    function( CnBaseFormEntryViewFactory, CnModalMessageFactory, CnModalConfirmFactory ) {
      var object = function( parentModel, root ) { CnBaseFormEntryViewFactory.construct( this, parentModel, root ); };
      return { instance: function( parentModel, root ) { return new object( parentModel, root ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnProxyConsentFormEntryModelFactory', [
    'CnBaseFormEntryModelFactory', 'CnProxyConsentFormEntryListFactory', 'CnProxyConsentFormEntryViewFactory', 'CnHttpFactory',
    function( CnBaseFormEntryModelFactory, CnProxyConsentFormEntryListFactory, CnProxyConsentFormEntryViewFactory, CnHttpFactory ) {
      var object = function( root ) {
        var self = this;
        CnBaseFormEntryModelFactory.construct( this, module );
        this.listModel = CnProxyConsentFormEntryListFactory.instance( this );
        this.viewModel = CnProxyConsentFormEntryViewFactory.instance( this, root );

        // extend getMetadata
        this.getMetadata = function() {
          return this.$$getMetadata().then( function() {
            return CnHttpFactory.instance( {
              path: 'alternate',
              data: {
                select: { column: [ 'first_name', 'last_name', 'informant', 'proxy' ] },
                modifier: { where: [ { column: 'informant OR proxy', operator: '=', value: true } ] }
              }
            } ).query(). then( function( response ) {
              self.metadata.columnList.alternate_id.enumList = [];
              response.data.forEach( function( item ) {
                self.metadata.columnList.alternate_id.enumList.push( {
                  value: item.id,
                  name: item.first_name + ' ' + item.last_name
                } );
              } );
            } )
          } );
        };
      };

      return {
        root: new object( true ),
        instance: function() { return new object( false ); }
      };
    }
  ] );

} );
