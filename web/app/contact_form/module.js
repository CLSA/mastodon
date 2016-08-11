define( function() {
  'use strict';

  try { var module = cenozoApp.module( 'contact_form', true ); } catch( err ) { console.warn( err ); return; }
  angular.extend( module, {
    identifier: {},
    name: {
      singular: 'contact form',
      plural: 'contact forms',
      possessive: 'contact form\'s',
      pluralPossessive: 'contact forms\''
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
        column: 'contact_form_total.entry_total',
        title: 'Entries',
        type: 'number'
      },
      submitted_total: {
        column: 'contact_form_total.submitted_total',
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

  module.addInputGroup( '', {
    id: {
      title: 'ID',
      type: 'string',
      constant: true
    },
    status: {
      title: 'Status',
      type: 'string',
      constant: true,
      help: 'Set to "completed" when done, ' +
            '"invalid" when marked invalid, ' +
            '"adjudication" when two entries have been submitted but do not match, ' +
            '"started" when there are less than two entries submitted and ' +
            '"new" when no entries have been submitted.'
    },
    completed: {
      title: 'Complete',
      type: 'boolean',
      constant: true
    },
    invalid: {
      title: 'Invalid',
      type: 'boolean'
    },
    date: {
      title: 'Date',
      type: 'date'
    },
    adjudicate: {
      type: 'hidden'
    }
  } );

  module.addExtraOperation( 'view', {
    title: 'Download',
    operation: function( $state, model ) { model.viewModel.downloadFile(); }
  } );

  if( angular.isDefined( module.actions.adjudicate ) ) {
    module.addExtraOperation( 'view', {
      title: 'Adjudicate',
      operation: function( $state, model ) { $state.go( 'contact_form.adjudicate', $state.params ); },
      isDisabled: function( $state, model ) { return !model.viewModel.record.adjudicate; }
    } );
  }

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnContactFormAdjudicate', [
    'CnContactFormAdjudicateFactory',
    function( CnContactFormAdjudicateFactory ) {
      return {
        // special general template found in application's general module directory
        templateUrl: module.getFileUrl( '../mastodon/adjudicate.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          $scope.model = CnContactFormAdjudicateFactory.instance();

          $scope.model.onLoad(); // breadcrumbs are handled by the service
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnContactFormList', [
    'CnContactFormModelFactory',
    function( CnContactFormModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'list.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnContactFormModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnContactFormView', [
    'CnContactFormModelFactory',
    function( CnContactFormModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'view.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnContactFormModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnContactFormAdjudicateFactory', [
    'CnBaseFormAdjudicateFactory',
    function( CnBaseFormAdjudicateFactory ) {
      var object = function( parentModel ) {
        this.formColumnList = [ {
          column: 'uid',
          title: 'UID'
        }, {
          column: 'option_1',
          title: 'Option #1'
        }, {
          column: 'option_2',
          title: 'Option #2'
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
  cenozo.providers.factory( 'CnContactFormListFactory', [
    'CnBaseListFactory',
    function( CnBaseListFactory ) {
      var object = function( parentModel ) { CnBaseListFactory.construct( this, parentModel ); };
      return { instance: function( parentModel ) { return new object( parentModel ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnContactFormViewFactory', [
    'CnBaseViewFactory', 'CnHttpFactory',
    function( CnBaseViewFactory, CnHttpFactory ) {
      var object = function( parentModel, root ) {
        CnBaseViewFactory.construct( this, parentModel, root );

        // download the form's file
        this.downloadFile = function() {
          return CnHttpFactory.instance( {
            path: 'consent_form/' + this.record.getIdentifier(),
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
      };
      return { instance: function( parentModel, root ) { return new object( parentModel, root ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnContactFormModelFactory', [
    'CnBaseModelFactory', 'CnContactFormListFactory', 'CnContactFormViewFactory',
    function( CnBaseModelFactory, CnContactFormListFactory, CnContactFormViewFactory ) {
      var object = function( root ) {
        var self = this;
        CnBaseModelFactory.construct( this, module );
        this.listModel = CnContactFormListFactory.instance( this );
        this.viewModel = CnContactFormViewFactory.instance( this, root );
      };

      return {
        root: new object( true ),
        instance: function() { return new object( false ); }
      };
    }
  ] );

} );
