import { CN_model_base_form_entry } from "./base_form_entry.mjs"

export class CN_model_consent_form_entry extends CN_model_base_form_entry {
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
