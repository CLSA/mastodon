'use strict';

var cenozo = angular.module( 'cenozo' );

/* ######################################################################################################## */
cenozo.controller( 'HeaderCtrl', [
  '$scope', '$state', 'CnBaseHeader', 'CnSession', 'CnHttpFactory', 'CnModalMessageFactory',
  function( $scope, $state, CnBaseHeader, CnSession, CnHttpFactory, CnModalMessageFactory ) {
    // copy all properties from the base header
    CnBaseHeader.construct( $scope );
  }
] );

/* ######################################################################################################## */
cenozo.factory( 'CnBaseFormAdjudicateFactory', [
  'CnSession', 'CnHttpFactory', '$state', '$q',
  function( CnSession, CnHttpFactory, $state, $q ) {
    return {
      construct: function( object, module ) {
        var formName = module.subject.snake;
        var formEntryName = formName + '_entry';
        var validatedEntryColumn = 'validated_' + formEntryName + '_id';

        object.module = module;

        object.reset = function() {
          object.form = null;
          object.isLoading = false;
          object.formEntryList = [];
          object.conflictColumnList = [];
        };
        object.reset();

        object.onLoad = function() {
          object.reset();
          object.isLoading = true;
          return $q.all( [
            CnHttpFactory.instance( {
              path: formName + '/' + $state.params.identifier,
              data: { select: { column: [
                'completed',
                'invalid',
                'adjudicate',
                { column: validatedEntryColumn, alias: 'validated_form_id' }
              ] } }
            } ).get().then( function( response ) {
              object.form = response.data;
              object.form.identifier = $state.params.identifier;

              CnSession.setBreadcrumbTrail( [ {
                title: module.name.plural.ucWords(),
                go: function() { $state.go( '^.list' ); }
              }, {
                title: object.form.id,
                go: function() { $state.go( '^.view', { identifier: $state.params.identifier } ); }
              }, {
                title: 'Adjudicate'
              } ] );
            } ),

            CnHttpFactory.instance( {
              path: formName + '/' + $state.params.identifier + '/' + formEntryName
            } ).get().then( function( response ) {
              object.formEntryList = response.data;

              // go through each entry to determine which columns don't match
              var compareEntry = object.formEntryList[0];
              object.conflictColumnList = object.formEntryList.reduce( function( list, entry, index ) {
                // compare the first entry to all others
                if( 0 < index ) {
                  for( var column in entry ) {
                    // check if this column is in the form column list and doesn't match the first entry
                    if( object.formColumnList.findByProperty( 'column', column ) &&
                        compareEntry[column] !== entry[column] ) list.push( column );
                  }
                }
                return list;
              }, [] );
            } )
          ] ).finally( function() { object.isLoading = false; } );
        };

        object.defer = function( entryId ) {
          CnHttpFactory.instance( {
            path: formEntryName + '/' + entryId,
            data: { deferred: true }
          } ).patch().then( object.onLoad );
        };

        object.validate = function( entryId ) {
          var data = { completed: true };
          data[validatedEntryColumn] = entryId;
          CnHttpFactory.instance( {
            path: formName + '/' + object.form.id,
            data: data
          } ).patch().then( function() {
            object.form[validatedEntryColumn] = entryId;
            object.onLoad();
          } );
        };

        object.viewParent = function() {
          $state.go( '^.view', { identifier: $state.params.identifier } );
        }
      }
    };
  }
] );
