import { CN_base_form_entry_model } from "./base_form_entry_model.mjs"

export class CN_consent_form_entry_model extends CN_base_form_entry_model {
  constructor() {
    super({
      type: "consent",
      wording: {
        singular: "consent form entry",
        plural: "consent form entries",
        posessive: "consent form entry's",
      },
      properties: {
        details: {
          title: "Details",
          open: true,
          properties: {
            participation: { title: "Consent to Participate", type: "boolean" },
            blood_urine: { title: "Consent to Blood/Urine", type: "boolean" },
            hin_access: { title: "Consent to HIN Access", type: "boolean" },
            signed: { title: "Signed", type: "boolean" },
            date: { title: "Date", type: "date" },
          },
        },
      },
    });
  }
}
