define(function () {
  "use strict";

  try {
    var module = cenozoApp.module("contact_form", true);
  } catch (err) {
    console.warn(err);
    return;
  }

  cenozoApp.initFormModule(module, "contact");

  /* ############################################################################################## */
  cenozo.providers.directive("cnContactFormAdjudicate", [
    "CnContactFormAdjudicateFactory",
    function (CnContactFormAdjudicateFactory) {
      return {
        // special general template found in application's general module directory
        templateUrl: module.getFileUrl("../mastodon/adjudicate.tpl.html"),
        restrict: "E",
        scope: { model: "=?" },
        controller: function ($scope) {
          $scope.model = CnContactFormAdjudicateFactory.instance();

          $scope.model.onLoad(); // breadcrumbs are handled by the service
        },
      };
    },
  ]);

  /* ############################################################################################## */
  cenozo.providers.directive("cnContactFormList", [
    "CnContactFormModelFactory",
    function (CnContactFormModelFactory) {
      return {
        templateUrl: module.getFileUrl("list.tpl.html"),
        restrict: "E",
        scope: { model: "=?" },
        controller: function ($scope) {
          if (angular.isUndefined($scope.model))
            $scope.model = CnContactFormModelFactory.root;
        },
      };
    },
  ]);

  /* ############################################################################################## */
  cenozo.providers.directive("cnContactFormView", [
    "CnContactFormModelFactory",
    function (CnContactFormModelFactory) {
      return {
        templateUrl: module.getFileUrl("view.tpl.html"),
        restrict: "E",
        scope: { model: "=?" },
        controller: function ($scope) {
          if (angular.isUndefined($scope.model))
            $scope.model = CnContactFormModelFactory.root;
        },
      };
    },
  ]);

  /* ############################################################################################## */
  cenozo.providers.factory("CnContactFormAdjudicateFactory", [
    "CnBaseFormAdjudicateFactory", "CnContactFormModelFactory",
    function (CnBaseFormAdjudicateFactory, CnContactFormModelFactory) {
      var object = function (parentModel) {
        this.formColumnList = [
          {
            column: "uid",
            title: "UID",
          },
          {
            column: "participate",
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
        this.parentModel = CnContactFormModelFactory.root;
      };
      return {
        instance: function (parentModel) {
          return new object(parentModel);
        },
      };
    },
  ]);

  /* ############################################################################################## */
  cenozo.providers.factory("CnContactFormListFactory", [
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
  cenozo.providers.factory("CnContactFormViewFactory", [
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
  cenozo.providers.factory("CnContactFormModelFactory", [
    "CnBaseFormModelFactory",
    "CnContactFormListFactory",
    "CnContactFormViewFactory",
    function (
      CnBaseFormModelFactory,
      CnContactFormListFactory,
      CnContactFormViewFactory
    ) {
      var object = function (root) {
        var self = this;
        CnBaseFormModelFactory.construct(this, module);
        this.listModel = CnContactFormListFactory.instance(this);
        this.viewModel = CnContactFormViewFactory.instance(this, root);
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
