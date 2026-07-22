const { CN_base_model } = await import(`${CENOZO_URL}/js/model/base_model.mjs`);

export class CN_model_participant_data_template extends CN_base_model {
  constructor() {
    super({
      wording: {
        singular: "data template file",
        plural: "data template files",
        posessive: "data template file's",
      },
      columns: {
        participant_data_name: { title: "Participant Data", table_prefix: false },
        rank: { title: "Rank", type: "rank" },
        language: { column: "language.name", title: "Language" },
        opal_view: { title: "Opal View" },
      },
      properties: {
        participant_data_name: {
          title: "Participant Data",
          type: "string",
          is_hidden: () => "add" == this.get_action_name(),
          is_constant: () => true,
        },
        rank: { title: "Rank", type: "rank" },
        language_id: {
          title: "Language",
          type: "enum",
          enum: {
            path: "language",
            modifier: {
              where: { column: "active", operator: "=", value: true },
              order: "language.name",
            },
          },
        },
        opal_view: {
          title: "Opal View",
          help: "The name of the Opal view that contains the data needed to fill in the template.",
        },
        data: {
          title: "File",
          type: "base64",
          mime_type: "application/pdf",
          get_filename: async () => this.get_action().get_property_value("opal_view") + ".pdf",
        },
      },
    });
  }
}
