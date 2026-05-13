import { CN_model_base_alternate_consent_form_entry } from "./base_alternate_consent_form_entry.mjs"

export class CN_model_dm_consent_form_entry extends CN_model_base_alternate_consent_form_entry {
  constructor() {
    super({
      type: "dm",
      wording: {
        singular: "decision maker form entry",
        plural: "decision maker form entries",
        posessive: "decision maker form entry's",
      },
    });
  }
}
