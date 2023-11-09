cenozoApp.defineModule({
  name: "participant_data_template",
  models: ["add", "list", "view"],
  create: (module) => {
    angular.extend(module, {
      identifier: {
        parent: {
          subject: "participant_data",
          column: "participant_data.id",
        },
      },
      name: {
        singular: "data template file",
        plural: "data template files",
        possessive: "data template file's",
        friendlyColumn: "rank",
      },
      columnList: {
        participant_data_name: {
          title: "Participant Data",
          isIncluded: function( $state, model ) { return 'participant_data' != model.getSubjectFromState(); },
        },
        rank: { title: "Rank", type: "rank" },
        lang: { column: "language.name", title: "Language" },
        opal_view: { title: "Opal View", },
      },
      defaultOrder: {
        column: "rank",
        reverse: false,
      },
    });

    module.addInputGroup("", {
      participant_data_name: {
        title: "Participant Data",
        type: "string",
        isExcluded: 'add',
        isConstant: true,
      },
      rank: { title: "Rank", type: "rank" },
      language_id: { title: "Language", type: "enum" },
      opal_view: {
        title: "Opal View",
        type: "string",
        help: "The name of the view in Opal's \"mastodon\" project that contains the data needed to fill in the template.",
      },
      data: {
        title: "File",
        type: "base64",
        mimeType: "application/pdf",
      },
    });

    /* ############################################################################################## */
    cenozo.providers.factory("CnParticipantDataTemplateModelFactory", [
      "CnBaseModelFactory",
      "CnParticipantDataTemplateAddFactory",
      "CnParticipantDataTemplateListFactory",
      "CnParticipantDataTemplateViewFactory",
      "CnHttpFactory",
      function (
        CnBaseModelFactory,
        CnParticipantDataTemplateAddFactory,
        CnParticipantDataTemplateListFactory,
        CnParticipantDataTemplateViewFactory,
        CnHttpFactory
      ) {
        var object = function (root) {
          CnBaseModelFactory.construct(this, module);
          this.addModel = CnParticipantDataTemplateAddFactory.instance(this);
          this.listModel = CnParticipantDataTemplateListFactory.instance(this);
          this.viewModel = CnParticipantDataTemplateViewFactory.instance(this, root);

          // extend getMetadata
          this.getMetadata = async function () {
            await this.$$getMetadata();

            const response = await CnHttpFactory.instance({
              path: "language",
              data: {
                select: { column: ["id", "name"] },
                modifier: {
                  where: { column: "active", operator: "=", value: true },
                  order: "name",
                  limit: 1000
                },
              },
            }).query();

            this.metadata.columnList.language_id.enumList =
              response.data.reduce((list, item) => {
                list.push({
                  value: item.id,
                  name: item.name,
                });
                return list;
              }, []);
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
  },
});
