import { CN_model_base_form_entry } from "./base_form_entry.mjs"

export class CN_model_hin_form_entry extends CN_model_base_form_entry {
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
