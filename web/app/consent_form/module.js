define(function () {
  "use strict";

  try {
    var module = cenozoApp.module("consent_form", true);
  } catch (err) {
    console.warn(err);
    return;
  }

  cenozoApp.initFormModule(module, "consent");

  /* ############################################################################################## */
  cenozo.providers.directive("cnConsentFormAdjudicate", [
    "CnConsentFormAdjudicateFactory",
    function (CnConsentFormAdjudicateFactory) {
      return {
        // special general template found in application's general module directory
        templateUrl: module.getFileUrl("../mastodon/adjudicate.tpl.html"),
        restrict: "E",
        scope: { model: "=?" },
        controller: function ($scope) {
          $scope.model = CnConsentFormAdjudicateFactory.instance();

          $scope.model.onLoad(); // breadcrumbs are handled by the service
        },
      };
    },
  ]);

  /* ############################################################################################## */
  cenozo.providers.directive("cnConsentFormList", [
    "CnConsentFormModelFactory",
    function (CnConsentFormModelFactory) {
      return {
        templateUrl: module.getFileUrl("list.tpl.html"),
        restrict: "E",
        scope: { model: "=?" },
        controller: function ($scope) {
          if (angular.isUndefined($scope.model))
            $scope.model = CnConsentFormModelFactory.root;
        },
      };
    },
  ]);

  /* ############################################################################################## */
  cenozo.providers.directive("cnConsentFormView", [
    "CnConsentFormModelFactory",
    function (CnConsentFormModelFactory) {
      return {
        templateUrl: module.getFileUrl("view.tpl.html"),
        restrict: "E",
        scope: { model: "=?" },
        controller: function ($scope) {
          if (angular.isUndefined($scope.model))
            $scope.model = CnConsentFormModelFactory.root;
        },
      };
    },
  ]);

  /* ############################################################################################## */
  cenozo.providers.factory("CnConsentFormAdjudicateFactory", [
    "CnBaseFormAdjudicateFactory", "CnConsentFormModelFactory",
    function (CnBaseFormAdjudicateFactory, CnConsentFormModelFactory) {
      var object = function (parentModel) {
        this.formColumnList = [
          {
            column: "uid",
            title: "UID",
          },
          {
            column: "participation",
            title: "Consent to Participate",
          },
          {
            column: "blood_urine",
            title: "Consent to Blood/Urine",
          },
          {
            column: "hin_access",
            title: "Consent to HIN Access",
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
        this.parentModel = CnConsentFormModelFactory.root;
      };
      return {
        instance: function (parentModel) {
          return new object(parentModel);
        },
      };
    },
  ]);

  /* ############################################################################################## */
  cenozo.providers.factory("CnConsentFormListFactory", [
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
  cenozo.providers.factory("CnConsentFormViewFactory", [
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
  cenozo.providers.factory("CnConsentFormModelFactory", [
    "CnBaseFormModelFactory",
    "CnConsentFormListFactory",
    "CnConsentFormViewFactory",
    function (
      CnBaseFormModelFactory,
      CnConsentFormListFactory,
      CnConsentFormViewFactory
    ) {
      var object = function (root) {
        var self = this;
        CnBaseFormModelFactory.construct(this, module);
        this.listModel = CnConsentFormListFactory.instance(this);
        this.viewModel = CnConsentFormViewFactory.instance(this, root);
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
