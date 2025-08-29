const CN_api = (await import(`${CENOZO_URL}/js/api.mjs`)).default;

import { CN_base_form_entry_model } from "../base_form_entry_model.mjs"

export class CN_dm_consent_form_entry_model extends CN_base_form_entry_model {
  constructor() {
    super({
      type: "dm_consent",
      wording: {
        singular: "decision maker form entry",
        plural: "decision maker form entries",
        posessive: "decision maker form entry's",
      },
      properties: {
        details: {
          title: "Details",
          open: true,
          properties: {
            accept: { title: "Consent to Act as Decision Maker", type: "boolean" },
            alternate_id: {
              title: "Alternate",
              type: "typeahead",
              typeahead: {
                get_list: async (value, element) => {
                  const participant_id = element.parent_model.get_formatted_property("participant_id");
                  return await CN_api.get(`participant/${participant_id}/alternate`, {
                    select: {
                      column: [{
                        table: "alternate",
                        column: "id",
                        alias: "key",
                      }, {
                        table: "alternate",
                        column:
                          'CONCAT( alternate.first_name, " ", alternate.last_name, " (", alternate_type_list, ")" )',
                        alias: "value",
                        table_prefix: false,
                      }],
                    },
                    modifier: {
                      where: [
                        { bracket: true, open: true },
                        { column: "alternate_type_list", operator: "LIKE", value: "%Decision Maker%" },
                        { column: "alternate_type_list", operator: "LIKE", value: "%Information Provider%", or: true },
                        { bracket: true, open: false },
                        { bracket: true, open: true },
                        { column: "alternate.first_name", operator: "like", value: `%${value}%`, or: true },
                        { column: "alternate.last_name", operator: "like", value: `%${value}%`, or: true },
                        { bracket: true, open: false },
                      ],
                      order: 'CONCAT( alternate.first_name, " ", alternate.last_name )',
                    },
                  });
                },
              },
            },
            signed: { title: "Signed", type: "boolean" },
            date: { title: "Date", type: "date" },
          },
        },
      },
    });
  }
}
