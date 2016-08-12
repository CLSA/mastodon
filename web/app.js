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
cenozoApp.initFormModule = function( module, type ) {
  var columnList = {};
  columnList[type + '_form_id'] = {
    column: type + '_form.id',
    title: 'ID'
  };
  angular.extend( columnList, {
    user: {
      column: 'user.name',
      title: 'User'
    },
    submitted: {
      title: 'Submitted',
      type: 'boolean'
    },
    validated: {
      title: 'Validated',
      type: 'boolean'
    },
    date: {
      column: type + '_form.date',
      title: 'Date Added',
      type: 'date'
    }
  } );
  angular.extend( module, {
    identifier: {
      parent: {
        subject: type + '_form',
        column: type + '_form.id'
      }
    },
    name: {
      singular: type + ' form entry',
      plural: type + ' form entries',
      possessive: type + ' form entry\'s',
      pluralPossessive: type + ' form entries\''
    },
    columnList: columnList,
    defaultOrder: {
      column: 'user.name',
      reverse: false
    }
  } );

  var inputGroup = {
    submitted: { type: 'hidden' },
    completed: { column: type + '_form.completed', type: 'hidden' },
    user_id: {
      title: 'User',
      type: 'lookup-typeahead',
      typeahead: {
        table: 'user',
        select: 'CONCAT( first_name, " ", last_name, " (", name, ")" )',
        where: [ 'first_name', 'last_name', 'name' ]
      }
    }
  };

  if( 'contact' != type ) {
    inputGroup.uid = {
      title: 'UID',
      type: 'string',
      regex: '^[A-Z][0-9]{6}$',
      help: 'Must be in "A000000" format (a letter followed by 6 numbers)'
    };
  }

  module.addInputGroup( '', inputGroup );

  module.addExtraOperation( 'view', {
    title: 'Download',
    operation: function( $state, model ) { model.viewModel.downloadFile(); }
  } );

  // typist operations
  module.addExtraOperation( 'list', {
    title: 'Start New Entry',
    operation: function( $state, model ) { model.listModel.startNewEntry(); },
    isIncluded: function( $state, model ) { return model.isTypist; }
  } );
  module.addExtraOperation( 'view', {
    title: 'Submit Entry',
    operation: function( $state, model ) { model.viewModel.typistSubmitEntry(); },
    isIncluded: function( $state, model ) { return model.isTypist; }
  } );

  // administrator operations
  module.addExtraOperation( 'view', {
    title: 'Return to Typist',
    operation: function( $state, model ) {
      model.viewModel.deferEntry().then( function() { model.viewModel.onView(); } );
    },
    isDisabled: function( $state, model ) { return model.viewModel.record.completed; },
    isIncluded: function( $state, model ) { return !model.isTypist && true === model.viewModel.record.submitted; }
  } );
  module.addExtraOperation( 'view', {
    title: 'Force Submit',
    operation: function( $state, model ) {
      model.viewModel.submitEntry().then( function() { model.viewModel.onView(); } );
    },
    isDisabled: function( $state, model ) { return model.viewModel.record.completed; },
    isIncluded: function( $state, model ) { return !model.isTypist && false === model.viewModel.record.submitted; }
  } );
}

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
            data: { submitted: false }
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

/* ######################################################################################################## */
cenozo.factory( 'CnBaseFormEntryListFactory', [
  'CnBaseListFactory', 'CnSession', 'CnHttpFactory', 'CnModalMessageFactory', '$state',
  function( CnBaseListFactory, CnSession, CnHttpFactory, CnModalMessageFactory, $state ) {
    return {
      construct: function( object, parentModel ) {
        CnBaseListFactory.construct( object, parentModel );
        var formEntryName = parentModel.module.subject.snake;
        var formType = formEntryName.substring( 0, formEntryName.length - 10 );

        object.startNewEntry = function() {
          CnHttpFactory.instance( {
            path: formEntryName,
            data: { user_id: CnSession.user.id },
            onError: function( response ) {
              if( 404 == response.status ) {
                console.info( 'The "404 (Not Found)" error found above is normal and can be ignored.' );
                CnModalMessageFactory.instance( {
                  title: 'No Forms Available',
                  message: 'There are no new ' + formType + ' forms available for transcription at this time.'
                } ).show();
              } else { CnModalMessageFactory.httpError( response ); }
            }
          } ).post().then( function( response ) {
            $state.go( formEntryName + '.view', { identifier: response.data } );
          } );
        };
      }
    };
  }
] );

/* ######################################################################################################## */
cenozo.factory( 'CnBaseFormEntryViewFactory', [
  'CnBaseViewFactory', 'CnHttpFactory', 'CnModalMessageFactory', 'CnModalConfirmFactory', '$state',
  function( CnBaseViewFactory, CnHttpFactory, CnModalMessageFactory, CnModalConfirmFactory, $state ) {
    return {
      construct: function( object, parentModel, root ) {
        CnBaseViewFactory.construct( object, parentModel, root );
        var formEntryName = parentModel.module.subject.snake;
        var formName = formEntryName.substring( 0, formEntryName.length - 5 );

        object.onPatchError = function( response ) {
          // handle 306 errors (uid doesn't match existing participant)
          if( 306 == response.status ) {
            CnModalMessageFactory.instance( {
              title: 'Participant Not Found',
              message: 'There was no participant found for the UID "' + object.record.uid + '"',
              error: true
            } ).show().then( function() {
              object.record.uid = object.backupRecord.uid;
            } );
          } else object.$$onPatchError( response );
        };

        object.deferEntry = function() {
          return CnHttpFactory.instance( {
            path: formEntryName + '/' + object.record.id,
            data: { submitted: false }
          } ).patch().then( object.onLoad );
        };

        object.submitEntry = function() {
          return CnHttpFactory.instance( {
            path: formEntryName +'/' + object.record.id,
            data: { submitted: true }
          } ).patch();
        };

        object.typistSubmitEntry = function() {
          CnModalConfirmFactory.instance( {
            title: 'Submit Entry',
            message: 'Are you sure you wish to submit this form?  This should only be done after you have ' +
                     'entered all information on the form.'
          } ).show().then( function( response ) {
            if( response )
              object.submitEntry().then( function( response ) { $state.go( formEntryName + '.list' ); } );
          } );
        };

        // download the form's file
        object.downloadFile = function() {
          return CnHttpFactory.instance( {
            path: formName + '/' + object.record.getIdentifier(),
            data: { 'download': true },
            format: 'pdf'
          } ).get().then( function( response ) {
            saveAs(
              new Blob(
                [response.data],
                { type: response.headers( 'Content-Type' ).replace( /"(.*)"/, '$1' ) }
              ),
              response.headers( 'Content-Disposition' ).match( /filename=(.*);/ )[1]
            );
          } );
        };
      }
    };
  }
] );

/* ######################################################################################################## */
cenozo.factory( 'CnBaseFormEntryModelFactory', [
  'CnBaseModelFactory', 'CnSession',
  function( CnBaseModelFactory, CnSession ) {
    return {
      construct: function( object, module ) {
        CnBaseModelFactory.construct( object, module );
        object.isTypist = true;

        CnSession.promise.then( function() {
          object.isTypist = 'typist' == CnSession.role.name;

          if( object.isTypist ) {
            module.identifier = {};
            module.columnList.user.type = 'hidden';
            module.columnList.submitted.type = 'hidden';
            module.columnList.validated.type = 'hidden';
            var mainInputGroup = module.inputGroupList.findByProperty( 'title', '' );
            if( mainInputGroup ) {
              mainInputGroup.inputList.user_id.type = 'hidden';
              mainInputGroup.inputList.submitted.type = 'hidden';
            }
          }
        } );
      }
    };
  }
] );
