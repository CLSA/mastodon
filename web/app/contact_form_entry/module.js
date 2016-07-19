define( function() {
  'use strict';

  try { var module = cenozoApp.module( 'contact_form_entry', true ); } catch( err ) { console.warn( err ); return; }
  angular.extend( module, {
    identifier: {
      parent: {
        subject: 'contact_form',
        column: 'contact_form.id'
      }
    },
    name: {
      singular: 'contact form entry',
      plural: 'contact form entries',
      possessive: 'contact form entry\'s',
      pluralPossessive: 'contact form entries\''
    },
    columnList: {
      contact_form_id: {
        column: 'contact_form_id',
        title: 'ID'
      },
      user: {
        column: 'user.name',
        title: 'User'
      },
      deferred: {
        title: 'Deferred',
        type: 'boolean'
      },
      validated: {
        title: 'Validated',
        type: 'boolean'
      },
      date: {
        column: 'contact_form.date',
        title: 'Date Added',
        type: 'date'
      }
    },
    defaultOrder: {
      column: 'user.name',
      reverse: false
    }
  } );

  module.addInputGroup( '', {
    user_id: {
      title: 'User',
      type: 'lookup-typeahead',
      typeahead: {
        table: 'user',
        select: 'CONCAT( first_name, " ", last_name, " (", name, ")" )',
        where: [ 'first_name', 'last_name', 'name' ]
      }
    },
    deferred: {
      title: 'Deferred',
      type: 'boolean'
    },
    first_name: {
      title: 'First Name',
      type: 'string'
    },
    last_name: {
      title: 'Last Name',
      type: 'string'
    },
    apartment_number: {
      title: 'Apartment #',
      type: 'string'
    },
    street_number: {
      title: 'Street #',
      type: 'string'
    },
    street_name: {
      title: 'Street Name',
      type: 'string'
    },
    box: {
      title: 'Post Office Box',
      type: 'string'
    },
    rural_route: {
      title: 'Rural Route',
      type: 'string'
    },
    address_other: {
      title: 'Other',
      type: 'string'
    },
    city: {
      title: 'City',
      type: 'string'
    },
    region_id: {
      title: 'Region',
      type: 'enum'
    },
    postcode: {
      title: 'Postcode',
      type: 'string'
    },
    address_note: {
      title: 'Address Note',
      type: 'text'
    },
    home_phone: {
      title: 'Home Phone',
      type: 'string'
    },
    home_phone_note: {
      title: 'Home Phone Note',
      type: 'text'
    },
    mobile_phone: {
      title: 'Mobile Phone',
      type: 'string'
    },
    mobile_phone_note: {
      title: 'Mobile Phone Note',
      type: 'text'
    },
    phone_preference: {
      title: 'Phone Preference',
      type: 'string'
    },
    email: {
      title: 'Email',
      type: 'string'
    },
    gender: {
      title: 'Sex',
      type: 'string'
    },
    age_bracket: {
      title: 'Age Bracket',
      type: 'string'
    },
    monday: {
      title: 'Monday',
      type: 'boolean'
    },
    tuesday: {
      title: 'Tuesday',
      type: 'boolean'
    },
    wednesday: {
      title: 'Wednesday',
      type: 'boolean'
    },
    thursday: {
      title: 'Thursday',
      type: 'boolean'
    },
    friday: {
      title: 'Friday',
      type: 'boolean'
    },
    saturday: {
      title: 'Saturday',
      type: 'boolean'
    },
    time_9_10: {
      title: '9am to 10am',
      type: 'boolean'
    },
    time_10_11: {
      title: '10am to 11am',
      type: 'boolean'
    },
    time_11_12: {
      title: '11am to 12pm',
      type: 'boolean'
    },
    time_12_13: {
      title: '12pm to 1pm',
      type: 'boolean'
    },
    time_13_14: {
      title: '1pm to 2pm',
      type: 'boolean'
    },
    time_14_15: {
      title: '2pm to 3pm',
      type: 'boolean'
    },
    time_15_16: {
      title: '3pm to 4pm',
      type: 'boolean'
    },
    time_16_17: {
      title: '4pm to 5pm',
      type: 'boolean'
    },
    time_17_18: {
      title: '5pm to 6pm',
      type: 'boolean'
    },
    time_18_19: {
      title: '6pm to 7pm',
      type: 'boolean'
    },
    time_19_20: {
      title: '7pm to 8pm',
      type: 'boolean'
    },
    time_20_21: {
      title: '8pm to 9pm',
      type: 'boolean'
    },
    high_school: {
      title: 'High School',
      type: 'boolean'
    },
    post_secondary: {
      title: 'Post Secondary',
      type: 'boolean'
    },
    language_id: {
      title: 'Language',
      type: 'enum'
    },
    cohort_id: {
      title: 'Cohort',
      type: 'enum'
    },
    code: {
      title: 'Code',
      type: 'string'
    },
    signed: {
      title: 'Signed',
      type: 'boolean'
    },
    participant_date: {
      title: 'Participant Date',
      type: 'date'
    },
    stamped_date: {
      title: 'Stamped Date',
      type: 'date'
    },
    note: {
      title: 'Note',
      type: 'text'
    }
  } );

  if( angular.isDefined( module.actions.start ) ) {
    module.addExtraOperation( 'list', {
      title: 'Start New Entry',
      operation: function( $state, model ) { model.listModel.startNewEntry(); },
      isIncluded: function( $state, model ) { return model.isTypist; }
    } );
  }

  if( angular.isDefined( module.actions.start ) ) {
    module.addExtraOperation( 'view', {
      title: 'Submit Entry',
      operation: function( $state, model ) { model.viewModel.submitEntry(); },
      isIncluded: function( $state, model ) { return model.isTypist; }
    } );
  }

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnContactFormEntryList', [
    'CnContactFormEntryModelFactory',
    function( CnContactFormEntryModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'list.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnContactFormEntryModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnContactFormEntryTree', [
    'CnContactFormEntryTreeFactory', 'CnSession',
    function( CnContactFormEntryTreeFactory, CnSession ) {
      return {
        templateUrl: module.getFileUrl( 'tree.tpl.html' ),
        restrict: 'E',
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnContactFormEntryTreeFactory.instance();
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnContactFormEntryView', [
    'CnContactFormEntryModelFactory',
    function( CnContactFormEntryModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'view.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnContactFormEntryModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnContactFormEntryListFactory', [
    'CnBaseListFactory', 'CnSession', 'CnHttpFactory', 'CnModalMessageFactory', '$state',
    function( CnBaseListFactory, CnSession, CnHttpFactory, CnModalMessageFactory, $state ) {
      var object = function( parentModel ) {
        CnBaseListFactory.construct( this, parentModel );

        this.startNewEntry = function() {
          CnHttpFactory.instance( {
            path: 'contact_form_entry',
            data: { user_id: CnSession.user.id },
            onError: function( response ) {
              if( 404 == response.status ) {
                console.info( 'The "404 (Not Found)" error found above is normal and can be ignored.' );
                CnModalMessageFactory.instance( {
                  title: 'No Forms Available',
                  message: 'There are no new contact forms available for transcription at this time.'
                } ).show();
              } else { CnModalMessageFactory.httpError( response ); }
            }
          } ).post().then( function( response ) {
            $state.go( 'contact_form_entry.view', { identifier: response.data } );
          } );
        };
      };
      return { instance: function( parentModel ) { return new object( parentModel ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnContactFormEntryViewFactory', [
    'CnBaseViewFactory', 'CnHttpFactory', 'CnModalMessageFactory', 'CnModalConfirmFactory', '$state',
    function( CnBaseViewFactory, CnHttpFactory, CnModalMessageFactory, CnModalConfirmFactory, $state ) {
      var object = function( parentModel, root ) {
        var self = this;
        CnBaseViewFactory.construct( this, parentModel, root );

        this.onPatchError = function( response ) {
          // handle 306 errors (uid doesn't match existing participant)
          if( 306 == response.status ) {
            CnModalMessageFactory.instance( {
              title: 'Participant Not Found',
              message: 'There was no participant found for the UID "' + self.record.uid + '"',
              error: true
            } ).show().then( function() {
              self.record.uid = self.backupRecord.uid;
            } );
          } else self.$$onPatchError( response );
        };

        this.submitEntry = function() {
          CnModalConfirmFactory.instance( {
            title: 'Submit Entry',
            message: 'Are you sure you wish to submit this form?  This should only be done after you have ' +
                     'entered all information on the form.'
          } ).show().then( function( response ) {
            if( response ) {
              CnHttpFactory.instance( {
                path: 'contact_form_entry/' + self.record.id,
                data: { deferred: false }
              } ).patch().then( function( response ) {
                $state.go( 'contact_form_entry.list' );
              } );
            }
          } );
        };
      };
      return { instance: function( parentModel, root ) { return new object( parentModel, root ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnContactFormEntryModelFactory', [
    'CnBaseModelFactory', 'CnContactFormEntryListFactory', 'CnContactFormEntryViewFactory', 'CnSession',
    function( CnBaseModelFactory, CnContactFormEntryListFactory, CnContactFormEntryViewFactory, CnSession ) {
      var object = function( root ) {
        var self = this;
        CnBaseModelFactory.construct( this, module );
        this.listModel = CnContactFormEntryListFactory.instance( this );
        this.viewModel = CnContactFormEntryViewFactory.instance( this, root );
        this.isTypist = true;

        CnSession.promise.then( function() {
          self.isTypist = 'typist' == CnSession.role.name;

          if( self.isTypist ) {
            module.identifier = {};
            module.columnList.user.type = 'hidden';
            module.columnList.deferred.type = 'hidden';
            module.columnList.validated.type = 'hidden';
            var mainInputGroup = module.inputGroupList.findByProperty( 'title', '' );
            if( mainInputGroup ) {
              mainInputGroup.inputList.user_id.type = 'hidden';
              mainInputGroup.inputList.deferred.type = 'hidden';
            }
          }
        } );
      };

      return {
        root: new object( true ),
        instance: function() { return new object( false ); }
      };
    }
  ] );

} );
