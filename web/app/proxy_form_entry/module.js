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
      user: {
        column: 'user.name',
        title: 'User'
      },
      deferred: {
        title: 'Deferred',
        type: 'boolean'
      }
    },
    defaultOrder: {
      column: 'user.name',
      reverse: false
    }
  } );

  module.addInputGroup( null, {
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
      format: '^[A-Z][0-9]{6}$',
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
  } );

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
  } );

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
  } );

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
    'CnBaseListFactory',
    function( CnBaseListFactory ) {
      var object = function( parentModel ) { CnBaseListFactory.construct( this, parentModel ); };
      return { instance: function( parentModel ) { return new object( parentModel ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnProxyFormEntryViewFactory', [
    'CnBaseViewFactory',
    function( CnBaseViewFactory ) {
      var object = function( parentModel, root ) { CnBaseViewFactory.construct( this, parentModel, root ); };
      return { instance: function( parentModel, root ) { return new object( parentModel, root ); } };
    }
  ] );

  /* ######################################################################################################## */
  cenozo.providers.factory( 'CnProxyFormEntryModelFactory', [
    'CnBaseModelFactory', 'CnProxyFormEntryListFactory', 'CnProxyFormEntryViewFactory',
    'CnHttpFactory', '$q',
    function( CnBaseModelFactory, CnProxyFormEntryListFactory, CnProxyFormEntryViewFactory,
              CnHttpFactory, $q ) {
      var object = function( root ) {
        var self = this;
        CnBaseModelFactory.construct( this, module );
        this.listModel = CnProxyFormEntryListFactory.instance( this );
        this.viewModel = CnProxyFormEntryViewFactory.instance( this, root );

        // extend getMetadata
        this.getMetadata = function() {
          return $q.all( [

            this.$$getMetadata(),

            CnHttpFactory.instance( {
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

          ] );
        };
      };

      return {
        root: new object( true ),
        instance: function() { return new object( false ); }
      };
    }
  ] );

} );
