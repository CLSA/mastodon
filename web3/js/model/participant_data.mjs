const { CN_base_model } = await import(`${CENOZO_URL}/js/model/base_model.mjs`);
const { CN_model_study_phase } = await import(`${CENOZO_URL}/js/model/study_phase.mjs`);

export class CN_model_participant_data extends CN_base_model {
  constructor() {
    super({
      wording: {
        singular: "participant data",
        plural: "participant data",
        posessive: "participant data's",
      },
      columns: {
        study: { column: "study.name", title: "Study" },
        study_phase: { column: "study_phase.name", title: "Phase" },
        category: { title: "Category" },
        name: { title: "Name" },
      },
      properties: {
        study_phase_id: {
          title: "Study & Phase Name",
          type: "typeahead",
          typeahead: CN_model_study_phase.get_typeahead(),
          is_constant: () => true,
        },
        category: { title: "Category", format: "identifier" },
        name: { title: "Name", format: "identifier" },
        filetype: { title: "File Type", help: "The type of file (as a file extension) this data provides." },
      },
    });
  }

  /**
   * Extend parent method
   */
  configure_child(name) {
    const child_model = super.configure_child(name);
    if ("cohort" == name) child_model.allow_choose = () => true; // allow choosing
    return child_model;
  }
}
