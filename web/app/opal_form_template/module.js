define(function () {
  "use strict";

  try {
    var module = cenozoApp.module("opal_form_template", true);
  } catch (err) {
    console.warn(err);
    return;
  }
  angular.extend(module, {
    identifier: { column: "name" },
    name: {
      singular: "opal form template",
      plural: "opal form templates",
      possessive: "opal form template's",
    },
    columnList: {
      name: { title: "Name" },
      description: {
        title: "Description",
        align: "left",
      },
    },
    defaultOrder: {
      column: "name",
      reverse: false,
    },
  });

  /* ############################################################################################## */
  cenozo.providers.directive("cnOpalFormTemplateList", [
    "CnOpalFormTemplateModelFactory",
    function (CnOpalFormTemplateModelFactory) {
      return {
        templateUrl: module.getFileUrl("list.tpl.html"),
        restrict: "E",
        scope: { model: "=?" },
        controller: function ($scope) {
          if (angular.isUndefined($scope.model))
            $scope.model = CnOpalFormTemplateModelFactory.root;
        },
      };
    },
  ]);

  /* ############################################################################################## */
  cenozo.providers.factory("CnOpalFormTemplateListFactory", [
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
  cenozo.providers.factory("CnOpalFormTemplateModelFactory", [
    "CnBaseModelFactory",
    "CnOpalFormTemplateListFactory",
    function (CnBaseModelFactory, CnOpalFormTemplateListFactory) {
      var object = function (root) {
        CnBaseModelFactory.construct(this, module);
        this.listModel = CnOpalFormTemplateListFactory.instance(this);

        // need to explicitely disable the view option
        this.getViewEnabled = function () {
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
