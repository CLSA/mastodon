define(function () {
  "use strict";

  try {
    var module = cenozoApp.module("consent_form_entry", true);
  } catch (err) {
    console.warn(err);
    return;
  }

  cenozoApp.initFormEntryModule(module, "consent");

  module.addInputGroup(
    "Details",
    {
      participation: {
        title: "Consent to Participate",
        type: "boolean",
      },
      blood_urine: {
        title: "Consent to Blood/Urine",
        type: "boolean",
      },
      hin_access: {
        title: "Consent to HIN Access",
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
  cenozo.providers.directive("cnConsentFormEntryList", [
    "CnConsentFormEntryModelFactory",
    function (CnConsentFormEntryModelFactory) {
      return {
        templateUrl: module.getFileUrl("list.tpl.html"),
        restrict: "E",
        scope: { model: "=?" },
        controller: function ($scope) {
          if (angular.isUndefined($scope.model))
            $scope.model = CnConsentFormEntryModelFactory.root;
        },
      };
    },
  ]);

  /* ############################################################################################## */
  cenozo.providers.directive("cnConsentFormEntryView", [
    "CnConsentFormEntryModelFactory",
    function (CnConsentFormEntryModelFactory) {
      return {
        templateUrl: module.getFileUrl("view.tpl.html"),
        restrict: "E",
        scope: { model: "=?" },
        controller: function ($scope) {
          if (angular.isUndefined($scope.model))
            $scope.model = CnConsentFormEntryModelFactory.root;
        },
      };
    },
  ]);

  /* ############################################################################################## */
  cenozo.providers.factory("CnConsentFormEntryListFactory", [
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
  cenozo.providers.factory("CnConsentFormEntryViewFactory", [
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
  cenozo.providers.factory("CnConsentFormEntryModelFactory", [
    "CnBaseFormEntryModelFactory",
    "CnConsentFormEntryListFactory",
    "CnConsentFormEntryViewFactory",
    function (
      CnBaseFormEntryModelFactory,
      CnConsentFormEntryListFactory,
      CnConsentFormEntryViewFactory
    ) {
      var object = function (root) {
        CnBaseFormEntryModelFactory.construct(this, module);
        this.listModel = CnConsentFormEntryListFactory.instance(this);
        this.viewModel = CnConsentFormEntryViewFactory.instance(this, root);
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
