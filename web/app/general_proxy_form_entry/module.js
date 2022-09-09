define(function () {
  "use strict";

  try {
    var module = cenozoApp.module("general_proxy_form_entry", true);
  } catch (err) {
    console.warn(err);
    return;
  }

  cenozoApp.initFormEntryModule(module, "general_proxy");

  module.addInputGroup(
    "Additional Details",
    {
      continue_questionnaires: {
        title: "Continue Questionnaires",
        type: "boolean",
      },
      hin_future_access: {
        title: "Continue Health Card",
        type: "boolean",
      },
      continue_dcs_visits: {
        title: "Continue DCS Visits",
        type: "boolean",
      },
      signed: {
        title: "Signed",
        type: "boolean",
      },
      date: {
        title: "Date",
        type: "date",
      },
    },
    true
  );

  module.addInputGroup(
    "Proxy Decision Maker",
    {
      proxy_first_name: {
        title: "First Name",
        type: "string",
      },
      proxy_last_name: {
        title: "Last Name",
        type: "string",
      },
      proxy_address_international: {
        title: "International Address",
        type: "boolean",
      },
      proxy_apartment_number: {
        title: "Apartment Number",
        type: "string",
      },
      proxy_street_number: {
        title: "Street Number",
        type: "string",
      },
      proxy_street_name: {
        title: "Street Name",
        type: "string",
      },
      proxy_box: {
        title: "Box",
        type: "string",
        format: "integer",
        help: "Include numbers only.",
      },
      proxy_rural_route: {
        title: "Rural Route",
        type: "string",
        format: "integer",
        help: "Include numbers only.",
      },
      proxy_address_other: {
        title: "Address Other",
        type: "string",
      },
      proxy_city: {
        title: "City",
        type: "string",
      },
      proxy_region_id: {
        title: "Region",
        type: "enum",
        isExcluded: function ($state, model) {
          return !angular.isUndefined(model.viewModel.record.proxy_address_international) &&
            true === model.viewModel.record.proxy_address_international;
        },
      },
      proxy_international_region: {
        title: "International Region",
        type: "string",
        isExcluded: function ($state, model) {
          return angular.isUndefined(model.viewModel.record.proxy_address_international) ||
            true !== model.viewModel.record.proxy_address_international;
        },
      },
      proxy_international_country_id: {
        title: "International Country",
        type: "enum",
        isExcluded: function ($state, model) {
          return angular.isUndefined(model.viewModel.record.proxy_address_international) ||
            true !== model.viewModel.record.proxy_address_international;
        },
      },
      proxy_postcode: {
        title: "Postcode",
        type: "string",
        help: 'Must be in "A1A 1A1" format, zip codes in "01234" format (if not international).',
      },
      proxy_address_note: {
        title: "Address Note",
        type: "text",
      },
      proxy_phone_international: {
        title: "International Phone",
        type: "boolean",
      },
      proxy_phone: {
        title: "Phone",
        type: "string",
        help: "Must be a valid North American phone number in XXX-XXX-XXXX format (if not international).",
      },
      proxy_phone_note: {
        title: "Phone Note",
        type: "text",
      },
      proxy_note: {
        title: "Note",
        type: "text",
      },
      already_identified: {
        title: "Already Identified",
        type: "boolean",
      },
      same_as_proxy: {
        title: "Same As Proxy",
        type: "boolean",
      },
    },
    true
  );

  module.addInputGroup(
    "Proxy Information Provider",
    {
      informant_first_name: {
        title: "First Name",
        type: "string",
        help: "If informant is same as proxy then enter the first and last name only",
      },
      informant_last_name: {
        title: "Last Name",
        type: "string",
        help: "If informant is same as proxy then enter the first and last name only",
      },
      informant_address_international: {
        title: "International",
        type: "boolean",
      },
      informant_apartment_number: {
        title: "Apartment Number",
        type: "string",
      },
      informant_street_number: {
        title: "Street Number",
        type: "string",
      },
      informant_street_name: {
        title: "Street Name",
        type: "string",
      },
      informant_box: {
        title: "Box",
        type: "string",
        format: "integer",
      },
      informant_rural_route: {
        title: "Rural Route",
        type: "string",
        format: "integer",
      },
      informant_address_other: {
        title: "Address Other",
        type: "string",
      },
      informant_city: {
        title: "City",
        type: "string",
      },
      informant_region_id: {
        title: "Region",
        type: "enum",
        isExcluded: function ($state, model) {
          return !angular.isUndefined(model.viewModel.record.informant_address_international) &&
            true === model.viewModel.record.informant_address_international;
        },
      },
      informant_international_region: {
        title: "International Region",
        type: "string",
        isExcluded: function ($state, model) {
          return angular.isUndefined(model.viewModel.record.informant_address_international) ||
            true !== model.viewModel.record.informant_address_international;
        },
      },
      informant_international_country_id: {
        title: "International Country",
        type: "enum",
        isExcluded: function ($state, model) {
          return angular.isUndefined(model.viewModel.record.informant_address_international) ||
            true !== model.viewModel.record.informant_address_international;
        },
      },
      informant_postcode: {
        title: "Postcode",
        type: "string",
        help: 'Must be in "A1A 1A1" format, zip codes in "01234" format (if not international).',
      },
      informant_address_note: {
        title: "Address Note",
        type: "text",
      },
      informant_phone_international: {
        title: "International",
        type: "boolean",
      },
      informant_phone: {
        title: "Phone",
        type: "string",
        help: "Must be a valid North American phone number in XXX-XXX-XXXX format (if not international).",
      },
      informant_phone_note: {
        title: "Phone Note",
        type: "text",
      },
      informant_note: {
        title: "Note",
        type: "text",
      },
    },
    true
  );

  /* ############################################################################################## */
  cenozo.providers.directive("cnGeneralProxyFormEntryList", [
    "CnGeneralProxyFormEntryModelFactory",
    function (CnGeneralProxyFormEntryModelFactory) {
      return {
        templateUrl: module.getFileUrl("list.tpl.html"),
        restrict: "E",
        scope: { model: "=?" },
        controller: function ($scope) {
          if (angular.isUndefined($scope.model))
            $scope.model = CnGeneralProxyFormEntryModelFactory.root;
        },
      };
    },
  ]);

  /* ############################################################################################## */
  cenozo.providers.directive("cnGeneralProxyFormEntryView", [
    "CnGeneralProxyFormEntryModelFactory",
    function (CnGeneralProxyFormEntryModelFactory) {
      return {
        templateUrl: module.getFileUrl("view.tpl.html"),
        restrict: "E",
        scope: { model: "=?" },
        controller: function ($scope) {
          if (angular.isUndefined($scope.model))
            $scope.model = CnGeneralProxyFormEntryModelFactory.root;
        },
      };
    },
  ]);

  /* ############################################################################################## */
  cenozo.providers.factory("CnGeneralProxyFormEntryListFactory", [
    "CnBaseFormEntryListFactory",
    "CnModalMessageFactory",
    function (CnBaseFormEntryListFactory, CnModalMessageFactory) {
      var object = function (parentModel) {
        CnBaseFormEntryListFactory.construct(this, parentModel);
      };
      return {
        instance: function (parentModel) {
          return new object(parentModel);
        },
      };
    },
  ]);

  /* ############################################################################################## */
  cenozo.providers.factory("CnGeneralProxyFormEntryViewFactory", [
    "CnBaseFormEntryViewFactory",
    "CnModalMessageFactory",
    "CnModalConfirmFactory",
    function (
      CnBaseFormEntryViewFactory,
      CnModalMessageFactory,
      CnModalConfirmFactory
    ) {
      var object = function (parentModel, root) {
        CnBaseFormEntryViewFactory.construct(this, parentModel, root);
        this.onPatchError = function(response) {
          if( 400 == response.status && 'invalid format' == angular.fromJson( response.data ) ) {
            // use the config data to determine the error message
            let message = "";
            if( angular.isDefined(response.config.data.proxy_postcode) || 
                angular.isDefined(response.config.data.informant_postcode) ) {
              message = 'Postcodes must either be in "A1A 1A1" or "01234" format (if not international).';
            } else if( angular.isDefined(response.config.data.proxy_phone) ||
                       angular.isDefined(response.config.data.informant_phone) ) {
              message = 'Phone numbers must be a valid North American phone number in "XXX-XXX-XXXX" format (if not international).';
            } else {
              message = "An unknown error has occurred.";
            }

            // this will only happen when a postcode is invalid
            angular.extend( response, {
              status: 306,
              data: angular.toJson( message ),
            });
          }

          this.$$onPatchError(response);
        };
      };
      return {
        instance: function (parentModel, root) {
          return new object(parentModel, root);
        },
      };
    },
  ]);

  /* ############################################################################################## */
  cenozo.providers.factory("CnGeneralProxyFormEntryModelFactory", [
    "CnBaseFormEntryModelFactory",
    "CnGeneralProxyFormEntryListFactory",
    "CnGeneralProxyFormEntryViewFactory",
    "CnHttpFactory",
    function (
      CnBaseFormEntryModelFactory,
      CnGeneralProxyFormEntryListFactory,
      CnGeneralProxyFormEntryViewFactory,
      CnHttpFactory
    ) {
      var object = function (root) {
        CnBaseFormEntryModelFactory.construct(this, module);
        this.listModel = CnGeneralProxyFormEntryListFactory.instance(this);
        this.viewModel = CnGeneralProxyFormEntryViewFactory.instance(
          this,
          root
        );

        // extend getMetadata
        this.getMetadata = async function () {
          var self = this;
          await this.$$getMetadata();

          const [countryResponse, regionResponse] = await Promise.all([
            CnHttpFactory.instance({
              path: "country",
              data: {
                select: { column: [ "id", "name" ], },
                modifier: { order: "name", limit: 1000 },
              },
            }).query(),

            CnHttpFactory.instance({
              path: "region",
              data: {
                select: {
                  column: [
                    "id",
                    { table: "country", column: "name", alias: "country" },
                    {
                      column: 'CONCAT_WS( ", ", region.name, country.name )',
                      alias: "name",
                      table_prefix: false,
                    },
                  ],
                },
                modifier: { order: ["country.name", "region.name"], limit: 1000 },
              },
            }).query(),
          ]);

          this.metadata.columnList.proxy_international_country_id.enumList =
            countryResponse.data.reduce((list, item) => {
              list.push({
                value: item.id,
                name: item.name,
              });
              return list;
            }, []);
          this.metadata.columnList.informant_international_country_id.enumList = angular.copy(
            this.metadata.columnList.proxy_international_country_id.enumList
          );

          this.metadata.columnList.proxy_region_id.enumList =
            regionResponse.data.reduce((list, item) => {
              list.push({
                value: item.id,
                country: item.country,
                name: item.name,
              });
              return list;
            }, []);
          this.metadata.columnList.informant_region_id.enumList = angular.copy(
            this.metadata.columnList.proxy_region_id.enumList
          );
        };
      };

      return {
        root: new object(true),
        instance: function () {
          return new object(false);
        },
      };
    },
  ]);
});
