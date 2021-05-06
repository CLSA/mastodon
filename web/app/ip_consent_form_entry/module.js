define( function() {
  'use strict';

  try { var module = cenozoApp.module( 'ip_consent_form_entry', true ); }
  catch( err ) { console.warn( err ); return; }

  cenozoApp.initFormEntryModule( module, 'ip_consent' );

  // give these forms a special name
  angular.extend( module.name, {
    singular: 'information provider entry form',
    plural: 'information provider entry forms',
    possessive: 'information provider entry form\'s'
  } );

  module.addInputGroup( 'Details', {
    accept: {
      title: 'Consent to Act as Decision Maker',
      type: 'boolean'
    },
    alternate_id: {
      title: 'Alternate',
      type: 'lookup-typeahead',
      typeahead: {
        table: 'alternate',
        select: 'CONCAT( alternate.first_name, " ", alternate.last_name, " (", IF( ' +
          'proxy AND informant, ' +
          '"information provider and information provider", ' +
          'IF( proxy, "information provider", "information provider" ) ' +
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
  cenozo.providers.directive( 'cnIpConsentFormEntryList', [
    'CnIpConsentFormEntryModelFactory',
    function( CnIpConsentFormEntryModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'list.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnIpConsentFormEntryModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnIpConsentFormEntryView', [
    'CnIpConsentFormEntryModelFactory',
    function( CnIpConsentFormEntryModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'view.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnIpConsentFormEntryModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnIpConsentFormEntryListFactory', [
    'CnBaseFormEntryListFactory', 'CnModalMessageFactory',
    function( CnBaseFormEntryListFactory, CnModalMessageFactory ) {
      var object = function( parentModel ) { CnBaseFormEntryListFactory.construct( this, parentModel ); };
      return { instance: function( parentModel ) { return new object( parentModel ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnIpConsentFormEntryViewFactory', [
    'CnBaseFormEntryViewFactory', 'CnModalMessageFactory', 'CnModalConfirmFactory', 'CnHttpFactory',
    function( CnBaseFormEntryViewFactory, CnModalMessageFactory, CnModalConfirmFactory, CnHttpFactory ) {
      var object = function( parentModel, root ) { CnBaseFormEntryViewFactory.construct( this, parentModel, root ); };
      return { instance: function( parentModel, root ) { return new object( parentModel, root ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnIpConsentFormEntryModelFactory', [
    'CnBaseFormEntryModelFactory', 'CnIpConsentFormEntryListFactory', 'CnIpConsentFormEntryViewFactory',
    function( CnBaseFormEntryModelFactory, CnIpConsentFormEntryListFactory, CnIpConsentFormEntryViewFactory ) {
      var object = function( root ) {
        var self = this;
        CnBaseFormEntryModelFactory.construct( this, module );
        this.listModel = CnIpConsentFormEntryListFactory.instance( this );
        this.viewModel = CnIpConsentFormEntryViewFactory.instance( this, root );

        this.getTypeaheadData = function( input, viewValue ) {
          var data = self.$$getTypeaheadData( input, viewValue );

          // only include the selected participant's proxies and informants
          if( 'alternate' == input.typeahead.table ) {
            data.modifier.where.push( {
              column: 'alternate.informant',
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
