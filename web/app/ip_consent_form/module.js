define(function () {
  "use strict";

  try {
    var module = cenozoApp.module("ip_consent_form", true);
  } catch (err) {
    console.warn(err);
    return;
  }

  cenozoApp.initFormModule(module, "ip_consent");

  // give these forms a special name
  angular.extend(module.name, {
    singular: "information provider form",
    plural: "information provider forms",
    possessive: "information provider form's",
  });

  /* ############################################################################################## */
  cenozo.providers.directive("cnIpConsentFormAdjudicate", [
    "CnIpConsentFormAdjudicateFactory",
    function (CnIpConsentFormAdjudicateFactory) {
      return {
        // special general template found in application's general module directory
        templateUrl: module.getFileUrl("../mastodon/adjudicate.tpl.html"),
        restrict: "E",
        scope: { model: "=?" },
        controller: function ($scope) {
          $scope.model = CnIpConsentFormAdjudicateFactory.instance();

          $scope.model.onLoad(); // breadcrumbs are handled by the service
        },
      };
    },
  ]);

  /* ############################################################################################## */
  cenozo.providers.directive("cnIpConsentFormList", [
    "CnIpConsentFormModelFactory",
    function (CnIpConsentFormModelFactory) {
      return {
        templateUrl: module.getFileUrl("list.tpl.html"),
        restrict: "E",
        scope: { model: "=?" },
        controller: function ($scope) {
          if (angular.isUndefined($scope.model))
            $scope.model = CnIpConsentFormModelFactory.root;
        },
      };
    },
  ]);

  /* ############################################################################################## */
  cenozo.providers.directive("cnIpConsentFormView", [
    "CnIpConsentFormModelFactory",
    function (CnIpConsentFormModelFactory) {
      return {
        templateUrl: module.getFileUrl("view.tpl.html"),
        restrict: "E",
        scope: { model: "=?" },
        controller: function ($scope) {
          if (angular.isUndefined($scope.model))
            $scope.model = CnIpConsentFormModelFactory.root;
        },
      };
    },
  ]);

  /* ############################################################################################## */
  cenozo.providers.factory("CnIpConsentFormAdjudicateFactory", [
    "CnBaseFormAdjudicateFactory", "CnIpConsentFormModelFactory",
    function (CnBaseFormAdjudicateFactory, CnIpConsentFormModelFactory) {
      var object = function (parentModel) {
        this.formColumnList = [
          {
            column: "uid",
            title: "UID",
          },
          {
            column: "accept",
            title: "Consent to Act as Information Provider",
          },
          {
            column: "alternate_full_name",
            title: "Information Provider",
          },
          {
            column: "signed",
            title: "Signed",
          },
          {
            column: "date",
            title: "Date",
          },
        ];
        CnBaseFormAdjudicateFactory.construct(this, module);
        this.parentModel = CnIpConsentFormModelFactory.root;
      };
      return {
        instance: function (parentModel) {
          return new object(parentModel);
        },
      };
    },
  ]);

  /* ############################################################################################## */
  cenozo.providers.factory("CnIpConsentFormListFactory", [
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
  cenozo.providers.factory("CnIpConsentFormViewFactory", [
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
  cenozo.providers.factory("CnIpConsentFormModelFactory", [
    "CnBaseFormModelFactory",
    "CnIpConsentFormListFactory",
    "CnIpConsentFormViewFactory",
    function (
      CnBaseFormModelFactory,
      CnIpConsentFormListFactory,
      CnIpConsentFormViewFactory
    ) {
      var object = function (root) {
        var self = this;
        CnBaseFormModelFactory.construct(this, module);
        this.listModel = CnIpConsentFormListFactory.instance(this);
        this.viewModel = CnIpConsentFormViewFactory.instance(this, root);
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
