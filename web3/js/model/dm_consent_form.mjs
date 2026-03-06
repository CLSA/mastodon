import { CN_base_form_model, CN_base_form_adjudicate, CN_base_form_view } from "./base_form_model.mjs"
import { CN_dm_consent_form_entry_model } from "./dm_consent_form_entry.mjs"

const entry_model = new CN_dm_consent_form_entry_model();

export class CN_dm_consent_form_model extends CN_base_form_model {
  constructor() { super("dm_consent", entry_model); }
}

export class CN_dm_consent_form_adjudicate extends CN_base_form_adjudicate {
  constructor(parent_el, model) { super(parent_el, model); }
}

export class CN_dm_consent_form_view extends CN_base_form_view {
  constructor(parent_el, model) { super(parent_el, model); }
}
