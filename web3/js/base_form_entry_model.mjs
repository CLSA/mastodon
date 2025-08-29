const CN_api = (await import(`${CENOZO_URL}/js/api.mjs`)).default;

const { CN_base_model } = await import(`${CENOZO_URL}/js/base_model.mjs`);

export class CN_base_form_entry_model extends CN_base_model {
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
      user_id: {
        title: "User",
        type: "typeahead",
        typeahead: {
          get_list: async (value) => {
            return await CN_api.get("user", {
              select: {
                columns: [{
                  table: "user",
                  column: "id",
                  alias: "key",
                }, {
                  table: "user",
                  column: 'CONCAT( first_name, " ", last_name, " (", name, ")" )',
                  alias: "value",
                  table_prefix: false,
                }],
              },
              modifier: {
                where: [
                  { column: "name", operator: "like", value: `%${value}%` },
                  { column: "first_name", operator: "like", value: `%${value}%`, or: true },
                  { column: "last_name", operator: "like", value: `%${value}%`, or: true },
                ],
              },
              order: 'CONCAT( first_name, " ", last_name, " (", name, ")" )',
            });
          },
          table: "user",
          select: 'CONCAT( first_name, " ", last_name, " (", name, ")" )',
          where: ["first_name", "last_name", "name"],
        },
      },
    };

    // all but the contact form have the participant property
    if ("contact" != params.type) {
      properties = {
        ...properties,
        participant_id: {
          title: "Participant (UID)",
          type: "typeahead",
          typeahead: {
            get_list: async (value) => {
              return await CN_api.get("participant", {
                select: {
                  column: [{
                    table: "participant",
                    column: "id",
                    alias: "key",
                  }, {
                    table: "participant",
                    column: 'CONCAT( participant.first_name, " ", participant.last_name, " (", uid, ")" )',
                    alias: "value",
                    table_prefix: false,
                  }], 
                },  
                modifier: {
                  where: [
                    { column: "uid", operator: "like", value: `%${value}%` },
                    { column: "first_name", operator: "like", value: `%${value}%`, or: true },
                    { column: "last_name", operator: "like", value: `%${value}%`, or: true },
                  ],
                  order: 'CONCAT( participant.first_name, " ", participant.last_name, " (", uid, ")" )',
                },
              });
            },
            table: "participant",
            select: 'CONCAT( participant.first_name, " ", participant.last_name, " (", uid, ")" )',
            where: ["participant.first_name", "participant.last_name", "uid"],
          },
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
