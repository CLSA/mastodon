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
      type: 'lookup-typeahead',
      typeahead: {
        table: 'alternate',
        select: 'CONCAT( alternate.first_name, " ", alternate.last_name, " (", IF( ' +
          'proxy AND informant, ' +
          '"decision maker and information provider", ' +
          'IF( proxy, "decision maker", "information provider" ) ' +
        '), ")" )',
        where: [ 'alternate.first_name', 'alternate.last_name' ]
        /*
        modifier: {
          where: { column: '', operator: '=', value: 
        }
        */
      }
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
    'CnBaseFormEntryViewFactory', 'CnModalMessageFactory', 'CnModalConfirmFactory', 'CnHttpFactory',
    function( CnBaseFormEntryViewFactory, CnModalMessageFactory, CnModalConfirmFactory, CnHttpFactory ) {
      var object = function( parentModel, root ) { CnBaseFormEntryViewFactory.construct( this, parentModel, root ); };
      return { instance: function( parentModel, root ) { return new object( parentModel, root ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnProxyConsentFormEntryModelFactory', [
    'CnBaseFormEntryModelFactory', 'CnProxyConsentFormEntryListFactory', 'CnProxyConsentFormEntryViewFactory',
    function( CnBaseFormEntryModelFactory, CnProxyConsentFormEntryListFactory, CnProxyConsentFormEntryViewFactory ) {
      var object = function( root ) {
        var self = this;
        CnBaseFormEntryModelFactory.construct( this, module );
        this.listModel = CnProxyConsentFormEntryListFactory.instance( this );
        this.viewModel = CnProxyConsentFormEntryViewFactory.instance( this, root );

        this.getTypeaheadData = function( input, viewValue ) {
          var data = self.$$getTypeaheadData( input, viewValue );

          // only include the selected participant's proxies and infromants
          if( 'alternate' == input.typeahead.table ) {
            data.modifier.where.push( {
              column: '( alternate.proxy OR alternate.informant )',
              operator: '=',
              value: true
            } );
            data.modifier.where.push( {
              column: 'alternate.participant_id',
              operator: '=',
              value: self.viewModel.record.participant_id
            } );
          }

          return data;
        };
      };

      return {
        root: new object( true ),
        instance: function() { return new object( false ); }
      };
    }
  ] );

} );
