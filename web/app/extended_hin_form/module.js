define(function () {
  "use strict";

  try {
    var module = cenozoApp.module("extended_hin_form", true);
  } catch (err) {
    console.warn(err);
    return;
  }

  cenozoApp.initFormModule(module, "extended_hin");

  /* ############################################################################################## */
  cenozo.providers.directive("cnExtendedHinFormAdjudicate", [
    "CnExtendedHinFormAdjudicateFactory",
    function (CnExtendedHinFormAdjudicateFactory) {
      return {
        // special general template found in application's general module directory
        templateUrl: module.getFileUrl("../mastodon/adjudicate.tpl.html"),
        restrict: "E",
        scope: { model: "=?" },
        controller: function ($scope) {
          $scope.model = CnExtendedHinFormAdjudicateFactory.instance();

          $scope.model.onLoad(); // breadcrumbs are handled by the service
        },
      };
    },
  ]);

  /* ############################################################################################## */
  cenozo.providers.directive("cnExtendedHinFormList", [
    "CnExtendedHinFormModelFactory",
    function (CnExtendedHinFormModelFactory) {
      return {
        templateUrl: module.getFileUrl("list.tpl.html"),
        restrict: "E",
        scope: { model: "=?" },
        controller: function ($scope) {
          if (angular.isUndefined($scope.model))
            $scope.model = CnExtendedHinFormModelFactory.root;
        },
      };
    },
  ]);

  /* ############################################################################################## */
  cenozo.providers.directive("cnExtendedHinFormView", [
    "CnExtendedHinFormModelFactory",
    function (CnExtendedHinFormModelFactory) {
      return {
        templateUrl: module.getFileUrl("view.tpl.html"),
        restrict: "E",
        scope: { model: "=?" },
        controller: function ($scope) {
          if (angular.isUndefined($scope.model))
            $scope.model = CnExtendedHinFormModelFactory.root;
        },
      };
    },
  ]);

  /* ############################################################################################## */
  cenozo.providers.factory("CnExtendedHinFormAdjudicateFactory", [
    "CnBaseFormAdjudicateFactory", "CnExtendedHinFormModelFactory",
    function (CnBaseFormAdjudicateFactory, CnExtendedHinFormModelFactory) {
      var object = function (parentModel) {
        this.formColumnList = [
          {
            column: "uid",
            title: "UID",
          },
          {
            column: "hin10_access",
            title: "HIN 10 Year Access",
          },
          {
            column: "cihi_access",
            title: "CIHI Access",
          },
          {
            column: "cihi10_access",
            title: "CIHI 10 Year Access",
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
        this.parentModel = CnExtendedHinFormModelFactory.root;
      };
      return {
        instance: function (parentModel) {
          return new object(parentModel);
        },
      };
    },
  ]);

  /* ############################################################################################## */
  cenozo.providers.factory("CnExtendedHinFormListFactory", [
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
  cenozo.providers.factory("CnExtendedHinFormViewFactory", [
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
  cenozo.providers.factory("CnExtendedHinFormModelFactory", [
    "CnBaseFormModelFactory",
    "CnExtendedHinFormListFactory",
    "CnExtendedHinFormViewFactory",
    function (
      CnBaseFormModelFactory,
      CnExtendedHinFormListFactory,
      CnExtendedHinFormViewFactory
    ) {
      var object = function (root) {
        var self = this;
        CnBaseFormModelFactory.construct(this, module);
        this.listModel = CnExtendedHinFormListFactory.instance(this);
        this.viewModel = CnExtendedHinFormViewFactory.instance(this, root);
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
