define(function () {
  "use strict";

  try {
    var module = cenozoApp.module("proxy_form_entry", true);
  } catch (err) {
    console.warn(err);
    return;
  }

  cenozoApp.initFormEntryModule(module, "proxy");

  module.addInputGroup(
    "Proxy Decision Maker",
    {
      proxy: {
        title: "Use",
        type: "boolean",
      },
      already_identified: {
        title: "Already Identified",
        type: "boolean",
      },
      proxy_first_name: {
        title: "First Name",
        type: "string",
      },
      proxy_last_name: {
        title: "Last Name",
        type: "string",
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
      },
      proxy_postcode: {
        title: "Postcode",
        type: "string",
        regex: "^(([A-Z][0-9][A-Z] [0-9][A-Z][0-9])|([0-9]{5}))$",
        help: 'Must be in "A1A 1A1" format, zip codes in "01234" format.',
      },
      proxy_address_note: {
        title: "Address Note",
        type: "text",
      },
      proxy_phone: {
        title: "Phone",
        type: "string",
        regex:
          "^[2-9](1[02-9]|[02-8]1|[02-8][02-9])-[2-9](1[02-9]|[02-9]1|[02-9]{2})-[0-9]{4}$",
        help: "Must be in NNN-NNN-NNNN format.",
      },
      proxy_phone_note: {
        title: "Phone Note",
        type: "text",
      },
      proxy_note: {
        title: "Note",
        type: "text",
      },
    },
    true
  );

  module.addInputGroup(
    "Proxy Information Provider",
    {
      informant: {
        title: "Use",
        type: "boolean",
      },
      same_as_proxy: {
        title: "Same As Proxy",
        type: "boolean",
      },
      informant_first_name: {
        title: "First Name",
        type: "string",
      },
      informant_last_name: {
        title: "Last Name",
        type: "string",
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
      },
      informant_postcode: {
        title: "Postcode",
        type: "string",
        regex: "^(([A-Z][0-9][A-Z] [0-9][A-Z][0-9])|([0-9]{5}))$",
        help: 'Must be in "A1A 1A1" format, zip codes in "01234" format.',
      },
      informant_address_note: {
        title: "Address Note",
        type: "text",
      },
      informant_phone: {
        title: "Phone",
        type: "string",
        regex:
          "^[2-9](1[02-9]|[02-8]1|[02-8][02-9])-[2-9](1[02-9]|[02-9]1|[02-9]{2})-[0-9]{4}$",
        help: "Must be in XXX-XXX-XXXX format.",
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

  module.addInputGroup(
    "Additional Details",
    {
      continue_questionnaires: {
        title: "Continue Questionnaires",
        type: "boolean",
      },
      continue_physical_tests: {
        title: "Continue Physical Tests",
        type: "boolean",
      },
      continue_draw_blood: {
        title: "Continue Blood and Urine",
        type: "boolean",
      },
      hin_future_access: {
        title: "Continue Health Card",
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

  /* ############################################################################################## */
  cenozo.providers.directive("cnProxyFormEntryList", [
    "CnProxyFormEntryModelFactory",
    function (CnProxyFormEntryModelFactory) {
      return {
        templateUrl: module.getFileUrl("list.tpl.html"),
        restrict: "E",
        scope: { model: "=?" },
        controller: function ($scope) {
          if (angular.isUndefined($scope.model))
            $scope.model = CnProxyFormEntryModelFactory.root;
        },
      };
    },
  ]);

  /* ############################################################################################## */
  cenozo.providers.directive("cnProxyFormEntryView", [
    "CnProxyFormEntryModelFactory",
    function (CnProxyFormEntryModelFactory) {
      return {
        templateUrl: module.getFileUrl("view.tpl.html"),
        restrict: "E",
        scope: { model: "=?" },
        controller: function ($scope) {
          if (angular.isUndefined($scope.model))
            $scope.model = CnProxyFormEntryModelFactory.root;
        },
      };
    },
  ]);

  /* ############################################################################################## */
  cenozo.providers.factory("CnProxyFormEntryListFactory", [
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
  cenozo.providers.factory("CnProxyFormEntryViewFactory", [
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
      };
      return {
        instance: function (parentModel, root) {
          return new object(parentModel, root);
        },
      };
    },
  ]);

  /* ############################################################################################## */
  cenozo.providers.factory("CnProxyFormEntryModelFactory", [
    "CnBaseFormEntryModelFactory",
    "CnProxyFormEntryListFactory",
    "CnProxyFormEntryViewFactory",
    "CnHttpFactory",
    function (
      CnBaseFormEntryModelFactory,
      CnProxyFormEntryListFactory,
      CnProxyFormEntryViewFactory,
      CnHttpFactory
    ) {
      var object = function (root) {
        CnBaseFormEntryModelFactory.construct(this, module);
        this.listModel = CnProxyFormEntryListFactory.instance(this);
        this.viewModel = CnProxyFormEntryViewFactory.instance(this, root);

        // extend getMetadata
        this.getMetadata = async function () {
          var self = this;
          await this.$$getMetadata();

          var response = await CnHttpFactory.instance({
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
          }).query();

          this.metadata.columnList.proxy_region_id.enumList = [];
          this.metadata.columnList.informant_region_id.enumList = [];
          response.data.forEach(function (item) {
            self.metadata.columnList.proxy_region_id.enumList.push({
              value: item.id,
              country: item.country,
              name: item.name,
            });
            self.metadata.columnList.informant_region_id.enumList.push({
              value: item.id,
              country: item.country,
              name: item.name,
            });
          });
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
