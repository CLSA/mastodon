cenozoApp.extendModule({
  name: "form",
  create: (module) => {
    module.addInput("", "form_type_name", {
      column: "form_type.name",
      type: "hidden",
    });

    module.addExtraOperation("view", {
      title: "View in Data Entry",
      isDisabled: function ($state, model) {
        return angular.isUndefined(model.viewModel.viewDataEntryForm);
      },
      operation: async function ($state, model) {
        await model.viewModel.viewDataEntryForm();
      },
    });

    // extend the view factory
    cenozo.providers.decorator("CnFormViewFactory", [
      "$delegate",
      "CnHttpFactory",
      "$state",
      function ($delegate, CnHttpFactory, $state) {
        var instance = $delegate.instance;
        $delegate.instance = function (parentModel, root) {
          var object = instance(parentModel, root);

          // see if the form has a record in the data-entry module
          object.afterView(async function () {
            var form_subject = object.record.form_type_name + "_form";

            var response = await CnHttpFactory.instance({
              path:
                "form/" + object.record.getIdentifier() + "/" + form_subject,
              data: { select: { column: "id" } },
            }).query();

            if (0 < response.data.length) {
              object.viewDataEntryForm = async function () {
                await $state.go(form_subject + ".view", {
                  identifier: response.data[0].id,
                });
              };
            }
          });

          return object;
        };

        return $delegate;
      },
    ]);
  },
});
