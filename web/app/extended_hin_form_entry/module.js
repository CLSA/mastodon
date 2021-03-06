define(function () {
  "use strict";

  try {
    var module = cenozoApp.module("extended_hin_form_entry", true);
  } catch (err) {
    console.warn(err);
    return;
  }

  cenozoApp.initFormEntryModule(module, "extended_hin");

  module.addInputGroup(
    "Details",
    {
      hin10_access: {
        title: "HIN 10 Year Access",
        type: "boolean",
      },
      cihi_access: {
        title: "CIHI Access",
        type: "boolean",
      },
      cihi10_access: {
        title: "CIHI 10 Year Access",
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
  cenozo.providers.directive("cnExtendedHinFormEntryList", [
    "CnExtendedHinFormEntryModelFactory",
    function (CnExtendedHinFormEntryModelFactory) {
      return {
        templateUrl: module.getFileUrl("list.tpl.html"),
        restrict: "E",
        scope: { model: "=?" },
        controller: function ($scope) {
          if (angular.isUndefined($scope.model))
            $scope.model = CnExtendedHinFormEntryModelFactory.root;
        },
      };
    },
  ]);

  /* ############################################################################################## */
  cenozo.providers.directive("cnExtendedHinFormEntryView", [
    "CnExtendedHinFormEntryModelFactory",
    function (CnExtendedHinFormEntryModelFactory) {
      return {
        templateUrl: module.getFileUrl("view.tpl.html"),
        restrict: "E",
        scope: { model: "=?" },
        controller: function ($scope) {
          if (angular.isUndefined($scope.model))
            $scope.model = CnExtendedHinFormEntryModelFactory.root;
        },
      };
    },
  ]);

  /* ############################################################################################## */
  cenozo.providers.factory("CnExtendedHinFormEntryListFactory", [
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
  cenozo.providers.factory("CnExtendedHinFormEntryViewFactory", [
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
  cenozo.providers.factory("CnExtendedHinFormEntryModelFactory", [
    "CnBaseFormEntryModelFactory",
    "CnExtendedHinFormEntryListFactory",
    "CnExtendedHinFormEntryViewFactory",
    function (
      CnBaseFormEntryModelFactory,
      CnExtendedHinFormEntryListFactory,
      CnExtendedHinFormEntryViewFactory
    ) {
      var object = function (root) {
        CnBaseFormEntryModelFactory.construct(this, module);
        this.listModel = CnExtendedHinFormEntryListFactory.instance(this);
        this.viewModel = CnExtendedHinFormEntryViewFactory.instance(this, root);
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
