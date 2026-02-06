import { CN_base_alternate_consent_form_entry_model } from "./base_alternate_consent_form_entry_model.mjs"

export class CN_dm_consent_form_entry_model extends CN_base_alternate_consent_form_entry_model {
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
