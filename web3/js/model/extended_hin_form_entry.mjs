import { CN_base_form_entry_model } from "../base_form_entry_model.mjs"

export class CN_extended_hin_form_entry_model extends CN_base_form_entry_model {
  constructor() {
    super({
      type: "extended_hin",
      wording: {
        singular: "extended HIN form entry",
        plural: "extended HIN form entries",
        posessive: "extended HIN form entry's",
      },
      properties: {
        details: {
          title: "Details",
          open: true,
          properties: {
            hin10_access: { title: "HIN 10 Year Access", type: "boolean" },
            cihi_access: { title: "CIHI Access", type: "boolean" },
            cihi10_access: { title: "CIHI 10 Year Access", type: "boolean" },
            signed: { title: "Signed", type: "boolean" },
            date: { title: "Date", type: "date" },
          },
        },
      },
    });
  }
}
