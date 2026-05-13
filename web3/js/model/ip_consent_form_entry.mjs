import { CN_model_base_alternate_consent_form_entry } from "./base_alternate_consent_form_entry.mjs"

export class CN_model_ip_consent_form_entry extends CN_model_base_alternate_consent_form_entry {
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
