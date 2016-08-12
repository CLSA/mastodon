define( function() {
  'use strict';

  try { var module = cenozoApp.module( 'contact_form_entry', true ); }
  catch( err ) { console.warn( err ); return; }

  cenozoApp.initFormModule( module, 'contact' );

  module.addInputGroup( 'Details', {
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
  }, true );

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
    'CnBaseFormEntryListFactory', 'CnSession', 'CnHttpFactory', 'CnModalMessageFactory', '$state',
    function( CnBaseFormEntryListFactory, CnSession, CnHttpFactory, CnModalMessageFactory, $state ) {
      var object = function( parentModel ) {
        CnBaseFormEntryListFactory.construct( this, parentModel );
      };
      return { instance: function( parentModel ) { return new object( parentModel ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnContactFormEntryViewFactory', [
    'CnBaseFormEntryViewFactory', 'CnHttpFactory', 'CnModalMessageFactory', 'CnModalConfirmFactory', '$state',
    function( CnBaseFormEntryViewFactory, CnHttpFactory, CnModalMessageFactory, CnModalConfirmFactory, $state ) {
      var object = function( parentModel, root ) {
        CnBaseFormEntryViewFactory.construct( this, parentModel, root );
      };
      return { instance: function( parentModel, root ) { return new object( parentModel, root ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnContactFormEntryModelFactory', [
    'CnBaseFormEntryModelFactory',
    'CnContactFormEntryListFactory', 'CnContactFormEntryViewFactory', 'CnSession',
    function( CnBaseFormEntryModelFactory,
              CnContactFormEntryListFactory, CnContactFormEntryViewFactory, CnSession ) {
      var object = function( root ) {
        CnBaseFormEntryModelFactory.construct( this, module );
        this.listModel = CnContactFormEntryListFactory.instance( this );
        this.viewModel = CnContactFormEntryViewFactory.instance( this, root );
      };

      return {
        root: new object( true ),
        instance: function() { return new object( false ); }
      };
    }
  ] );

} );
