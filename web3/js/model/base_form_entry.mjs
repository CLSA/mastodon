const { CN_model_base } = await import(`${CENOZO_URL}/js/model/base_model.mjs`);
const { CN_model_participant } = await import(`${CENOZO_URL}/js/model/participant.mjs`);
const { CN_model_user } = await import(`${CENOZO_URL}/js/model/user.mjs`);

export class CN_model_base_form_entry extends CN_model_base {
  #form_type;

  constructor(params) {
    let columns = {};
    columns[`${params.type}_form_id`] = { column: `${params.type}_form.id`, title: "ID" };
    columns = {
      ...columns,
      user: { column: "user.name", title: "User" },
      submitted: { title: "Submitted", type: "boolean" },
      validated: { title: "Validated", type: "boolean", table_prefix: false },
      date: { column: `${params.type}_form.date`, title: "Date Added", type: "date" },
    };

    // all forms have the user property
    let properties = {
      user_id: { title: "User", type: "typeahead", typeahead: CN_model_user.get_typeahead() },
    };

    // all but the contact form have the participant property
    if ("contact" != params.type) {
      properties = {
        ...properties,
        participant_id: {
          title: "Participant (UID)",
          type: "typeahead",
          typeahead: CN_model_participant.get_typeahead(),
        },
      };
    }

    // finally, add the form's specific properties
    properties = {
      ...properties,
      ...params.properties
    };

    super({
      wording: params.wording,
      columns: columns,
      properties: properties,
    });

    this.#form_type = params.type;
  }

  /**
   * Returns the form type
   * @return string
   */
  get_form_type() { return this.#form_type; }
}
