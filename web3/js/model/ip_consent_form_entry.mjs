import { CN_base_alternate_consent_form_entry_model } from "../base_alternate_consent_form_entry_model.mjs"

export class CN_ip_consent_form_entry_model extends CN_base_alternate_consent_form_entry_model {
  constructor() {
    super({
      type: "ip",
      wording: {
        singular: "information provider form entry",
        plural: "information provider form entries",
        posessive: "information provider form entry's",
      },
    });
  }
}
