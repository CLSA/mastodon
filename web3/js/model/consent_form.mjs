import { CN_consent_form_entry_model } from "./consent_form_entry.mjs"
import { CN_base_form_model, CN_base_form_adjudicate, CN_base_form_view } from "../base_form_model.mjs"

const entry_model = new CN_consent_form_entry_model();

export class CN_consent_form_model extends CN_base_form_model {
  constructor() { super("consent", entry_model); }
}

export class CN_consent_form_adjudicate extends CN_base_form_adjudicate {
  constructor(model) { super(model); }
}

export class CN_consent_form_view extends CN_base_form_view {
  constructor(model) { super(model); }
}
