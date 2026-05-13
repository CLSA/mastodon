import { CN_model_base_form, CN_adjudicate_base_form, CN_view_base_form } from "./base_form.mjs"
import { CN_model_ip_consent_form_entry } from "./ip_consent_form_entry.mjs"

const entry_model = new CN_model_ip_consent_form_entry();

export class CN_model_ip_consent_form extends CN_model_base_form {
  constructor() { super("ip_consent", entry_model); }
}

export class CN_adjudicate_ip_consent_form extends CN_adjudicate_base_form {
  constructor(parent_el, model) { super(parent_el, model); }
}

export class CN_view_ip_consent_form extends CN_view_base_form {
  constructor(parent_el, model) { super(parent_el, model); }
}
