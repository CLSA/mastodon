import { CN_base_form_entry_model } from "./base_form_entry_model.mjs"

export class CN_hin_form_entry_model extends CN_base_form_entry_model {
  constructor() {
    super({
      type: "hin",
      wording: {
        singular: "HIN form entry",
        plural: "HIN form entries",
        posessive: "HIN form entry's",
      },
      properties: {
        details: {
          title: "Details",
          open: true,
          properties: {
            accept: { title: "Accept", type: "boolean" },
            signed: { title: "Signed", type: "boolean" },
            date: { title: "Date", type: "date" },
          },
        },
      },
    });
  }
}
