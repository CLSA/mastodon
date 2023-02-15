define(function () {
  "use strict";

  try {
    var module = cenozoApp.module("general_proxy_form", true);
  } catch (err) {
    console.warn(err);
    return;
  }

  cenozoApp.initFormModule(module, "general_proxy");

  /* ############################################################################################## */
  cenozo.providers.directive("cnGeneralProxyFormAdjudicate", [
    "CnGeneralProxyFormAdjudicateFactory",
    function (CnGeneralProxyFormAdjudicateFactory) {
      return {
        // special general template found in application's general module directory
        templateUrl: module.getFileUrl("../mastodon/adjudicate.tpl.html"),
        restrict: "E",
        scope: { model: "=?" },
        controller: function ($scope) {
          $scope.model = CnGeneralProxyFormAdjudicateFactory.instance();

          $scope.model.onLoad(); // breadcrumbs are handled by the service
        },
      };
    },
  ]);

  /* ############################################################################################## */
  cenozo.providers.directive("cnGeneralProxyFormList", [
    "CnGeneralProxyFormModelFactory",
    function (CnGeneralProxyFormModelFactory) {
      return {
        templateUrl: module.getFileUrl("list.tpl.html"),
        restrict: "E",
        scope: { model: "=?" },
        controller: function ($scope) {
          if (angular.isUndefined($scope.model))
            $scope.model = CnGeneralProxyFormModelFactory.root;
        },
      };
    },
  ]);

  /* ############################################################################################## */
  cenozo.providers.directive("cnGeneralProxyFormView", [
    "CnGeneralProxyFormModelFactory",
    function (CnGeneralProxyFormModelFactory) {
      return {
        templateUrl: module.getFileUrl("view.tpl.html"),
        restrict: "E",
        scope: { model: "=?" },
        controller: function ($scope) {
          if (angular.isUndefined($scope.model))
            $scope.model = CnGeneralProxyFormModelFactory.root;
        },
      };
    },
  ]);

  /* ############################################################################################## */
  cenozo.providers.factory("CnGeneralProxyFormAdjudicateFactory", [
    "CnBaseFormAdjudicateFactory", "CnGeneralProxyFormModelFactory",
    function (CnBaseFormAdjudicateFactory, CnGeneralProxyFormModelFactory) {
      var object = function (parentModel) {
        this.formColumnList = [
          {
            column: "uid",
            title: "UID",
          },
          {
            column: "continue_questionnaires",
            title: "Continue Questionnaires",
          },
          {
            column: "hin_future_access",
            title: "Continue Health Card",
          },
          {
            column: "continue_dcs_visits",
            title: "Continue DCS Visits",
          },
          {
            column: "signed",
            title: "Signed",
          },
          {
            column: "date",
            title: "Date",
          },
          {
            column: "proxy_first_name",
            title: "Proxy First Name",
          },
          {
            column: "proxy_last_name",
            title: "Proxy Last Name",
          },
          {
            column: "proxy_address_international",
            title: "Proxy Address International",
          },
          {
            column: "proxy_apartment_number",
            title: "Proxy Apartment Number",
          },
          {
            column: "proxy_street_number",
            title: "Proxy Street Number",
          },
          {
            column: "proxy_street_name",
            title: "Proxy Street Name",
          },
          {
            column: "proxy_box",
            title: "Proxy Box",
          },
          {
            column: "proxy_rural_route",
            title: "Proxy Rural Route",
          },
          {
            column: "proxy_address_other",
            title: "Proxy Address Other",
          },
          {
            column: "proxy_city",
            title: "Proxy City",
          },
          {
            column: "proxy_region",
            title: "Proxy Region",
          },
          {
            column: "proxy_international_region",
            title: "Proxy International Region",
          },
          {
            column: "proxy_international_country",
            title: "Proxy International Country",
          },
          {
            column: "proxy_postcode",
            title: "Proxy Postcode",
          },
          {
            column: "proxy_address_note",
            title: "Proxy Address Note",
          },
          {
            column: "proxy_phone_international",
            title: "Proxy Phone International",
          },
          {
            column: "proxy_phone",
            title: "Proxy Phone",
          },
          {
            column: "proxy_phone_note",
            title: "Proxy Phone Note",
          },
          {
            column: "proxy_note",
            title: "Proxy Note",
          },
          {
            column: "already_identified",
            title: "Already Identified",
          },
          {
            column: "same_as_proxy",
            title: "Same As Proxy",
          },
          {
            column: "informant_first_name",
            title: "Informant First Name",
          },
          {
            column: "informant_last_name",
            title: "Informant Last Name",
          },
          {
            column: "informant_address_international",
            title: "Informant Address International",
          },
          {
            column: "informant_apartment_number",
            title: "Informant Apartment Number",
          },
          {
            column: "informant_street_number",
            title: "Informant Street Number",
          },
          {
            column: "informant_street_name",
            title: "Informant Street Name",
          },
          {
            column: "informant_box",
            title: "Informant Box",
          },
          {
            column: "informant_rural_route",
            title: "Informant Rural Route",
          },
          {
            column: "informant_address_other",
            title: "Informant Address Other",
          },
          {
            column: "informant_city",
            title: "Informant City",
          },
          {
            column: "informant_region",
            title: "Informant Region",
          },
          {
            column: "informant_international_region",
            title: "Proxy International Region",
          },
          {
            column: "informant_international_country",
            title: "Proxy International Country",
          },
          {
            column: "informant_postcode",
            title: "Informant Postcode",
          },
          {
            column: "informant_address_note",
            title: "Informant Address Note",
          },
          {
            column: "informant_phone_international",
            title: "Informant Phone International",
          },
          {
            column: "informant_phone",
            title: "Informant Phone",
          },
          {
            column: "informant_phone_note",
            title: "Informant Phone Note",
          },
          {
            column: "informant_note",
            title: "Informant Note",
          },
        ];
        CnBaseFormAdjudicateFactory.construct(this, module);
        this.parentModel = CnGeneralProxyFormModelFactory.root;
      };
      return {
        instance: function (parentModel) {
          return new object(parentModel);
        },
      };
    },
  ]);

  /* ############################################################################################## */
  cenozo.providers.factory("CnGeneralProxyFormListFactory", [
    "CnBaseListFactory",
    function (CnBaseListFactory) {
      var object = function (parentModel) {
        CnBaseListFactory.construct(this, parentModel);
      };
      return {
        instance: function (parentModel) {
          return new object(parentModel);
        },
      };
    },
  ]);

  /* ############################################################################################## */
  cenozo.providers.factory("CnGeneralProxyFormViewFactory", [
    "CnBaseFormViewFactory",
    function (CnBaseFormViewFactory) {
      var object = function (parentModel, root) {
        CnBaseFormViewFactory.construct(this, parentModel, root);
      };
      return {
        instance: function (parentModel, root) {
          return new object(parentModel, root);
        },
      };
    },
  ]);

  /* ############################################################################################## */
  cenozo.providers.factory("CnGeneralProxyFormModelFactory", [
    "CnBaseFormModelFactory",
    "CnGeneralProxyFormListFactory",
    "CnGeneralProxyFormViewFactory",
    function (
      CnBaseFormModelFactory,
      CnGeneralProxyFormListFactory,
      CnGeneralProxyFormViewFactory
    ) {
      var object = function (root) {
        var self = this;
        CnBaseFormModelFactory.construct(this, module);
        this.listModel = CnGeneralProxyFormListFactory.instance(this);
        this.viewModel = CnGeneralProxyFormViewFactory.instance(this, root);

        // proxy forms can be submitted (for beartooth integration) but this can't be done through the web UI
        this.getAddEnabled = function () {
          return false;
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
