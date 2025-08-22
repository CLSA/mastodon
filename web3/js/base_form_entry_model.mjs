const { CN_base_model } = await import(`${CENOZO_URL}/js/base_model.mjs`);
// import { CN_base_model } from "./base_model.mjs"

export class CN_base_form_entry_model extends CN_base_model {
  constructor(form_type) {
    let columns = {};
    columns[`${form_type}_form_id`] = { column: `${form_type}_form.id`, title: "ID" };
    columns = {
      ...columns,
      user: { column: "user.name", title: "User" },  
      submitted: { title: "Submitted", type: "boolean" },  
      validated: { title: "Validated", type: "boolean", table_prefix: false },  
      date: { column: `${form_type}_form.date`, title: "Date Added", type: "date" },  
    };

    let properties = {
      user_id: {
        title: "User",
        type: "typeahead",
        typeahead: {
          get_list: async (value) => {
            const response = await CN_api.get("user", {
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
            return (await response.json());
          },
          table: "user",
          select: 'CONCAT( first_name, " ", last_name, " (", name, ")" )',
          where: ["first_name", "last_name", "name"],
        },
      },
    };

    if ("contact" != form_type) {
      properties = {
        ...properties,
        participant_id: {
          title: "Participant (UID)",
          type: "typeahead",
          typeahead: {
            get_list: async (value) => {
              const response = await CN_api.get("", {
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
              return (await response.json());
            },
            table: "participant",
            select: 'CONCAT( participant.first_name, " ", participant.last_name, " (", uid, ")" )',
            where: ["participant.first_name", "participant.last_name", "uid"],
          },
        },
      };
    }

    super({
      wording: {
        singular: `${form_type.replace(/_/, " ")} form entry`,
        plural: `${form_type.replace(/_/, " ")} form entries`,
        posessive: `${form_type.replace(/_/, " ")} form entry's`,
      },
      columns: columns,
      properties: properties,
    });
  }
}
