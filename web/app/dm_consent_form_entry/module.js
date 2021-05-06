define( function() {
  'use strict';

  try { var module = cenozoApp.module( 'dm_consent_form_entry', true ); }
  catch( err ) { console.warn( err ); return; }

  cenozoApp.initFormEntryModule( module, 'dm_consent' );

  // give these forms a special name
  angular.extend( module.name, {
    singular: 'decision maker entry form',
    plural: 'decision maker entry forms',
    possessive: 'decision maker entry form\'s'
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
          '"decision maker and information provider", ' +
          'IF( proxy, "decision maker", "information provider" ) ' +
        '), ")" )',
        where: [ 'alternate.first_name', 'alternate.last_name' ]
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
  cenozo.providers.directive( 'cnDmConsentFormEntryList', [
    'CnDmConsentFormEntryModelFactory',
    function( CnDmConsentFormEntryModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'list.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnDmConsentFormEntryModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnDmConsentFormEntryView', [
    'CnDmConsentFormEntryModelFactory',
    function( CnDmConsentFormEntryModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'view.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnDmConsentFormEntryModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnDmConsentFormEntryListFactory', [
    'CnBaseFormEntryListFactory', 'CnModalMessageFactory',
    function( CnBaseFormEntryListFactory, CnModalMessageFactory ) {
      var object = function( parentModel ) { CnBaseFormEntryListFactory.construct( this, parentModel ); };
      return { instance: function( parentModel ) { return new object( parentModel ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnDmConsentFormEntryViewFactory', [
    'CnBaseFormEntryViewFactory', 'CnModalMessageFactory', 'CnModalConfirmFactory', 'CnHttpFactory',
    function( CnBaseFormEntryViewFactory, CnModalMessageFactory, CnModalConfirmFactory, CnHttpFactory ) {
      var object = function( parentModel, root ) { CnBaseFormEntryViewFactory.construct( this, parentModel, root ); };
      return { instance: function( parentModel, root ) { return new object( parentModel, root ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnDmConsentFormEntryModelFactory', [
    'CnBaseFormEntryModelFactory', 'CnDmConsentFormEntryListFactory', 'CnDmConsentFormEntryViewFactory',
    function( CnBaseFormEntryModelFactory, CnDmConsentFormEntryListFactory, CnDmConsentFormEntryViewFactory ) {
      var object = function( root ) {
        var self = this;
        CnBaseFormEntryModelFactory.construct( this, module );
        this.listModel = CnDmConsentFormEntryListFactory.instance( this );
        this.viewModel = CnDmConsentFormEntryViewFactory.instance( this, root );

        this.getTypeaheadData = function( input, viewValue ) {
          var data = self.$$getTypeaheadData( input, viewValue );

          // only include the selected participant's proxies and informants
          if( 'alternate' == input.typeahead.table ) {
            data.modifier.where.push( {
              column: 'alternate.proxy',
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
