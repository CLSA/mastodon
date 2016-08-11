define( function() {
  'use strict';

  try { var module = cenozoApp.module( 'proxy_form_entry', true ); } catch( err ) { console.warn( err ); return; }
  angular.extend( module, {
    identifier: {
      parent: {
        subject: 'proxy_form',
        column: 'proxy_form.id'
      }
    },
    name: {
      singular: 'proxy form entry',
      plural: 'proxy form entries',
      possessive: 'proxy form entry\'s',
      pluralPossessive: 'proxy form entries\''
    },
    columnList: {
      proxy_form_id: {
        column: 'proxy_form_id',
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
        column: 'proxy_form.date',
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
    proxy_form_id: { type: 'hidden' },
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
    uid: {
      title: 'UID',
      type: 'string',
      regex: '^[A-Z][0-9]{6}$',
      help: 'Must be in "A000000" format (a letter followed by 6 numbers)'
    }
  } );

  module.addInputGroup( 'Proxy Decision Maker', {
    proxy: {
      title: "Use",
      type: 'boolean'
    },
    already_identified: {
      title: "Already Identified",
      type: 'boolean'
    },
    proxy_first_name: {
      title: "First Name",
      type: 'string'
    },
    proxy_last_name: {
      title: "Last Name",
      type: 'string'
    },
    proxy_apartment_number: {
      title: "Apartment Number",
      type: 'string'
    },
    proxy_street_number: {
      title: "Street Number",
      type: 'string'
    },
    proxy_street_name: {
      title: "Street Name",
      type: 'string'
    },
    proxy_box: {
      title: "Box",
      type: 'string'
    },
    proxy_rural_route: {
      title: "Rural Route",
      type: 'string'
    },
    proxy_address_other: {
      title: "Address Other",
      type: 'string'
    },
    proxy_city: {
      title: "City",
      type: 'string'
    },
    proxy_region_id: {
      title: "Region",
      type: 'enum'
    },
    proxy_postcode: {
      title: "Postcode",
      type: 'string'
    },
    proxy_address_note: {
      title: "Address Note",
      type: 'text'
    },
    proxy_phone: {
      title: "Phone",
      type: 'string'
    },
    proxy_phone_note: {
      title: "Phone Note",
      type: 'text'
    },
    proxy_note: {
      title: "Note",
      type: 'text'
    }
  }, true );

  module.addInputGroup( 'Proxy Information Provider', {
    informant: {
      title: "Use",
      type: 'boolean'
    },
    same_as_proxy: {
      title: "Same As Proxy",
      type: 'boolean'
    },
    informant_first_name: {
      title: "First Name",
      type: 'string'
    },
    informant_last_name: {
      title: "Last Name",
      type: 'string'
    },
    informant_apartment_number: {
      title: "Apartment Number",
      type: 'string'
    },
    informant_street_number: {
      title: "Street Number",
      type: 'string'
    },
    informant_street_name: {
      title: "Street Name",
      type: 'string'
    },
    informant_box: {
      title: "Box",
      type: 'string'
    },
    informant_rural_route: {
      title: "Rural Route",
      type: 'string'
    },
    informant_address_other: {
      title: "Address Other",
      type: 'string'
    },
    informant_city: {
      title: "City",
      type: 'string'
    },
    informant_region_id: {
      title: "Region",
      type: 'enum'
    },
    informant_postcode: {
      title: "Postcode",
      type: 'string'
    },
    informant_address_note: {
      title: "Address Note",
      type: 'text'
    },
    informant_phone: {
      title: "Phone",
      type: 'string'
    },
    informant_phone_note: {
      title: "Phone Note",
      type: 'text'
    },
    informant_note: {
      title: "Note",
      type: 'text'
    }
  }, true );

  module.addInputGroup( 'Additional Details', {
    informant_continue: {
      title: "Continue",
      type: 'boolean'
    },
    health_card: {
      title: "Health Card",
      type: 'boolean'
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

  module.addExtraOperation( 'view', {
    title: 'Download',
    operation: function( $state, model ) { model.viewModel.downloadFile(); }
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
  cenozo.providers.directive( 'cnProxyFormEntryList', [
    'CnProxyFormEntryModelFactory',
    function( CnProxyFormEntryModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'list.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnProxyFormEntryModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnProxyFormEntryTree', [
    'CnProxyFormEntryTreeFactory', 'CnSession',
    function( CnProxyFormEntryTreeFactory, CnSession ) {
      return {
        templateUrl: module.getFileUrl( 'tree.tpl.html' ),
        restrict: 'E',
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnProxyFormEntryTreeFactory.instance();
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.directive( 'cnProxyFormEntryView', [
    'CnProxyFormEntryModelFactory',
    function( CnProxyFormEntryModelFactory ) {
      return {
        templateUrl: module.getFileUrl( 'view.tpl.html' ),
        restrict: 'E',
        scope: { model: '=?' },
        controller: function( $scope ) {
          if( angular.isUndefined( $scope.model ) ) $scope.model = CnProxyFormEntryModelFactory.root;
        }
      };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnProxyFormEntryListFactory', [
    'CnBaseListFactory', 'CnSession', 'CnHttpFactory', 'CnModalMessageFactory', '$state',
    function( CnBaseListFactory, CnSession, CnHttpFactory, CnModalMessageFactory, $state ) {
      var object = function( parentModel ) {
        CnBaseListFactory.construct( this, parentModel );

        this.startNewEntry = function() {
          CnHttpFactory.instance( {
            path: 'proxy_form_entry',
            data: { user_id: CnSession.user.id },
            onError: function( response ) {
              if( 404 == response.status ) {
                console.info( 'The "404 (Not Found)" error found above is normal and can be ignored.' );
                CnModalMessageFactory.instance( {
                  title: 'No Forms Available',
                  message: 'There are no new proxy forms available for transcription at this time.'
                } ).show();
              } else { CnModalMessageFactory.httpError( response ); }
            }
          } ).post().then( function( response ) {
            $state.go( 'proxy_form_entry.view', { identifier: response.data } );
          } );
        };
      };
      return { instance: function( parentModel ) { return new object( parentModel ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnProxyFormEntryViewFactory', [
    'CnBaseViewFactory', 'CnHttpFactory', 'CnModalMessageFactory', 'CnModalConfirmFactory', '$state',
    function( CnBaseViewFactory, CnHttpFactory, CnModalMessageFactory, CnModalConfirmFactory, $state ) {
      var object = function( parentModel, root ) {
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
                path: 'proxy_form_entry/' + self.record.id,
                data: { deferred: false }
              } ).patch().then( function( response ) {
                $state.go( 'proxy_form_entry.list' );
              } );
            }
          } );
        };

        // download the form's file
        this.downloadFile = function() {
          return CnHttpFactory.instance( {
            path: 'proxy_form/' + this.record.proxy_form_id,
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
  cenozo.providers.factory( 'CnProxyFormEntryModelFactory', [
    'CnBaseModelFactory', 'CnProxyFormEntryListFactory', 'CnProxyFormEntryViewFactory',
    'CnSession', 'CnHttpFactory', '$q',
    function( CnBaseModelFactory, CnProxyFormEntryListFactory, CnProxyFormEntryViewFactory,
              CnSession, CnHttpFactory, $q ) {
      var object = function( root ) {
        var self = this;
        CnBaseModelFactory.construct( this, module );
        this.listModel = CnProxyFormEntryListFactory.instance( this );
        this.viewModel = CnProxyFormEntryViewFactory.instance( this, root );
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

        // extend getMetadata
        this.getMetadata = function() {
          return this.$$getMetadata().then( function() {
            return CnHttpFactory.instance( {
              path: 'region',
              data: {
                select: {
                  column: [
                    'id',
                    'country',
                    { column: 'CONCAT_WS( ", ", name, country )', alias: 'name', table_prefix: false }
                  ]
                },
                modifier: { order: ['country','name'], limit: 100 }
              }
            } ).query().then( function success( response ) {
              self.metadata.columnList.proxy_region_id.enumList = [];
              self.metadata.columnList.informant_region_id.enumList = [];
              response.data.forEach( function( item ) {
                self.metadata.columnList.proxy_region_id.enumList.push( {
                  value: item.id,
                  country: item.country,
                  name: item.name
                } );
                self.metadata.columnList.informant_region_id.enumList.push( {
                  value: item.id,
                  country: item.country,
                  name: item.name
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
