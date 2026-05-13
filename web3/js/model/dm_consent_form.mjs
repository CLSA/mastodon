import { CN_model_base_form, CN_adjudicate_base_form, CN_view_base_form } from "./base_form.mjs"
import { CN_model_dm_consent_form_entry } from "./dm_consent_form_entry.mjs"

const entry_model = new CN_model_dm_consent_form_entry();

export class CN_model_dm_consent_form extends CN_model_base_form {
  constructor() { super("dm_consent", entry_model); }
}

export class CN_adjudicate_dm_consent_form extends CN_adjudicate_base_form {
  constructor(parent_el, model) { super(parent_el, model); }
}

export class CN_view_dm_consent_form extends CN_view_base_form {
  constructor(parent_el, model) { super(parent_el, model); }
}
