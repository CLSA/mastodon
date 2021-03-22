'use strict';

var cenozo = angular.module( 'cenozo' );

/* ######################################################################################################## */
cenozo.controller( 'HeaderCtrl', [
  '$scope', 'CnBaseHeader',
  function( $scope, CnBaseHeader ) {
    // copy all properties from the base header
    CnBaseHeader.construct( $scope );
  }
] );

/* ######################################################################################################## */
cenozoApp.initFormModule = function( module, type ) {
  angular.extend( module, {
    identifier: {},
    name: {
      singular: type.replace( '_', ' ' ) + ' form',
      plural: type.replace( '_', ' ' ) + ' forms',
      possessive: type.replace( '_', ' ' ) + ' form\'s'
    },
    columnList: {
      id: {
        title: 'ID'
      },
      status: {
        title: 'Status',
        type: 'string',
        help: 'One of "completed", "invalid", "adjudication", "started" or "new".'
      },
      entry_total: {
        column: type + '_form_total.entry_total',
        title: 'Entries',
        type: 'number'
      },
      submitted_total: {
        column: type + '_form_total.submitted_total',
        title: 'Submitted Entries',
        type: 'number'
      },
      date: {
        title: 'Date',
        type: 'date'
      }
    },
    defaultOrder: {
      column: 'date',
      reverse: true
    }
  } );

  if( 'contact' != type ) {
    cenozo.insertPropertyAfter( module.columnList, 'id', 'cohort', {
      column: type + '_form_total.cohort',
      title: 'Cohort',
      type: 'string',
      help: 'A list of all cohorts entered by typists for this form (separated by a comma).'
    } );
    cenozo.insertPropertyAfter( module.columnList, 'cohort', 'uid', {
      column: type + '_form_total.uid',
      title: 'UID',
      type: 'string',
      help: 'A list of all UIDs entered by typists for this form (separated by a comma).'
    } );
  }

  module.addInputGroup( '', {
    id: {
      title: 'ID',
      type: 'string',
      isConstant: true
    },
    status: {
      title: 'Status',
      type: 'string',
      isConstant: true,
      help: 'Set to "completed" when done, ' +
            '"invalid" when marked invalid, ' +
            '"adjudication" when two entries have been submitted but do not match, ' +
            '"started" when there are less than two entries submitted and ' +
            '"new" when no entries have been submitted.'
    },
    completed: {
      title: 'Complete',
      type: 'boolean',
      isConstant: true
    },
    invalid: {
      title: 'Invalid',
      type: 'boolean'
    },
    date: {
      title: 'Date',
      type: 'date'
    },
    adjudicate: { type: 'hidden' },
    form_id: { type: 'hidden' }
  } );

  module.addExtraOperation( 'view', {
    title: 'Download',
    isDisabled: function( $state, model ) { return angular.isUndefined( model.viewModel.downloadFile ); },
    operation: function( $state, model ) { model.viewModel.downloadFile(); }
  } );

  if( angular.isDefined( module.actions.adjudicate ) ) {
    module.addExtraOperation( 'view', {
      title: 'Adjudicate',
      operation: function( $state, model ) { $state.go( type + '_form.adjudicate', $state.params ); },
      isDisabled: function( $state, model ) { return !model.viewModel.record.adjudicate; }
    } );
  }

  module.addExtraOperation( 'view', {
    title: 'View Imported Form',
    operation: function( $state, model ) { return $state.go( 'form.view', { identifier: model.viewModel.record.form_id } ); },
    isIncluded: function( $state, model ) { return model.viewModel.record.form_id; }
  } );
};

/* ######################################################################################################## */
cenozoApp.initFormEntryModule = function( module, type ) {
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
      singular: type.replace( '_', ' ' ) + ' form entry',
      plural: type.replace( '_', ' ' ) + ' form entries',
      possessive: type.replace( '_', ' ' ) + ' form entry\'s'
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

  inputGroup[type + '_form_id'] = { type: 'hidden' };

  if( 'contact' != type ) {
    inputGroup.participant_id = {
      title: 'Participant (UID)',
      type: 'lookup-typeahead',
      typeahead: {
        table: 'participant',
        select: 'CONCAT( participant.first_name, " ", participant.last_name, " (", uid, ")" )',
        where: [ 'participant.first_name', 'participant.last_name', 'uid' ]
      }
    };
  }

  module.addInputGroup( '', inputGroup );

  module.addExtraOperation( 'view', {
    title: 'Download',
    isDisabled: function( $state, model ) { return angular.isUndefined( model.viewModel.downloadFile ); },
    operation: function( $state, model ) { model.viewModel.downloadFile(); }
  } );

  // typist operations
  module.addExtraOperation( 'list', {
    title: 'Start New Entry',
    operation: function( $state, model ) { model.listModel.startNewEntry(); },
    isIncluded: function( $state, model ) { return model.isRole( 'typist' ); }
  } );
  module.addExtraOperation( 'view', {
    title: 'Submit Entry',
    operation: function( $state, model ) { model.viewModel.typistSubmitEntry(); },
    isIncluded: function( $state, model ) { return model.isRole( 'typist' ); }
  } );

  // administrator operations
  module.addExtraOperation( 'view', {
    title: 'Return to Typist',
    operation: function( $state, model ) {
      model.viewModel.deferEntry().then( function() { model.viewModel.onView(); } );
    },
    isDisabled: function( $state, model ) { return model.viewModel.record.completed; },
    isIncluded: function( $state, model ) { return !model.isRole( 'typist' ) && true === model.viewModel.record.submitted; }
  } );
  module.addExtraOperation( 'view', {
    title: 'Force Submit',
    operation: function( $state, model ) {
      model.viewModel.submitEntry().then( function() { model.viewModel.onView(); } );
    },
    isDisabled: function( $state, model ) { return model.viewModel.record.completed; },
    isIncluded: function( $state, model ) { return !model.isRole( 'typist' ) && false === model.viewModel.record.submitted; }
  } );
}

/* ######################################################################################################## */
cenozo.factory( 'CnBaseFormViewFactory', [
  'CnBaseViewFactory', 'CnHttpFactory',
  function( CnBaseViewFactory, CnHttpFactory ) {
    return {
      construct: function( object, parentModel, root ) {
        CnBaseViewFactory.construct( object, parentModel, root );

        object.afterView( function() {
          if( angular.isUndefined( object.downloadFile ) ) {
            object.downloadFile = function() {
              return CnHttpFactory.instance( {
                path: parentModel.module.subject.snake + '/' + object.record.getIdentifier()
              } ).file();
            };
          }
        } );
      }
    };
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

              if( angular.isUndefined( object.downloadFile ) ) {
                object.downloadFile = function() {
                  return CnHttpFactory.instance( { path: formName + '/' + object.form.id } ).file();
                };
              };

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
                    var entry1 = compareEntry[column];
                    if( angular.isString( entry1 ) ) entry1 = entry1.toUpperCase();
                    var entry2 = entry[column];
                    if( angular.isString( entry2 ) ) entry2 = entry2.toUpperCase();

                    if( object.formColumnList.findByProperty( 'column', column ) && entry1 !== entry2 )
                      list.push( column );
                  }
                }
                return list;
              }, [] );
            } )
          ] ).finally( function() { object.isLoading = false; } );
        };

        object.view = function( entryId ) { $state.go( formEntryName + '.view', { identifier: entryId } ); };

        object.validate = function( entryId ) {
          var data = { completed: true };
          CnHttpFactory.instance( {
            path: formName + '/' + object.form.id,
            data: { adjudicate: entryId }
          } ).patch().then( function() {
            //object.form[validatedEntryColumn] = entryId;
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
cenozo.factory( 'CnBaseFormModelFactory', [
  'CnBaseModelFactory',
  function( CnBaseModelFactory ) {
    return {
      construct: function( object, module ) {
        CnBaseModelFactory.construct( object, module );

        // make sure not to allow editing of completed forms
        object.getEditEnabled = function() {
          return object.$$getEditEnabled() && !object.viewModel.record.completed;
        };
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
        var formType = formEntryName.substring( 0, formEntryName.length - 11 ).camelToSnake().replace( /_/g, ' ' );

        object.startNewEntry = function() {
          CnHttpFactory.instance( {
            path: formEntryName,
            data: { user_id: CnSession.user.id },
            onError: function( response ) {
              if( 404 == response.status ) {
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
        var formName = formEntryName.replace( '_entry', '' );

        object.afterView( function() {
          if( angular.isUndefined( object.downloadFile ) ) {
            object.downloadFile = function() {
              return CnHttpFactory.instance( { path: formName + '/' + object.record[formName + '_id'] } ).file();
            };
          }
        } );

        object.onPatchError = function( response ) {
          // handle 306 errors (uid doesn't match existing participant)
          if( 306 == response.status ) {
            CnModalMessageFactory.instance( {
              title: 'Participant Not Found',
              message: JSON.parse( response.data ),
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
            data: { submitted: true },
            onError: function( response ) {
              if( 400 == response.status && angular.isObject( response.data ) ) {
                CnModalMessageFactory.instance( {
                  title: 'Error Found in Form',
                  message: 'There were errors found in the form which have been highlighted in red. ' +
                           'You must correct these errors before the form can be submitted.'
                } ).show();

                cenozo.forEachFormElement( 'form', function( element ) { element.$error = {}; } );
                for( var name in response.data ) {
                  var element = cenozo.getFormElement( name );
                  if( element ) {
                    if( 'Cannot be blank.' == response.data[name] ) element.$error.required = true;
                    else element.$error.custom = response.data[name];
                  }
                }
                cenozo.forEachFormElement( 'form', function( element ) {
                  cenozo.updateFormElement( element, true );
                } );
              } else if( 409 == response.status ) {
                // highlight the duplicate rows
                response.data.forEach( function( item ) {
                  // convert participant_id to uid
                  if( 'participant_id' == item ) item = 'uid';
                  var element = cenozo.getFormElement( item );
                  if( element ) {
                    element.$error.conflict = true;
                    cenozo.updateFormElement( element, true );
                  }
                } );
              } else CnModalMessageFactory.httpError( response );
            }
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

        // make sure not to allow editing of completed forms
        object.getEditEnabled = function() {
          return object.$$getEditEnabled() && !object.viewModel.record.completed;
        };

        CnSession.promise.then( function() {
          if( object.isRole( 'typist' ) ) {
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
