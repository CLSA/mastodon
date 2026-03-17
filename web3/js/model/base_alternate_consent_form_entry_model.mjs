const { CN_api } = await import(`${CENOZO_URL}/js/api.mjs`);
const { CN_common } = await import(`${CENOZO_URL}/js/common.mjs`);

import { CN_base_form_entry_model } from "./base_form_entry_model.mjs"

export class CN_base_alternate_consent_form_entry_model extends CN_base_form_entry_model {
  constructor(params) {
    super({
      type: `${params.type}_consent`,
      wording: params.wording,
      properties: {
        details: {
          title: "Details",
          open: true,
          properties: {
            accept: {
              title: `Consent to Act as ${params.type.toUpperCase()}`,
              type: "boolean"
            },
            alternate_id: {
              title: "Alternate",
              type: "typeahead",
              typeahead: {
                // NOTE: this is a special typeahead that can't be pulled from the alternate model because it
                // references this model's participant_id property
                get_list: async (value, form_input) => {
                  const participant_id = await form_input.get_action().get_formatted_property("participant_id");
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
                      limit: 20,
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
