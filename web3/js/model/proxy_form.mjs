import { CN_base_form_model, CN_base_form_adjudicate, CN_base_form_view } from "./base_form_model.mjs"
import { CN_proxy_form_entry_model } from "./proxy_form_entry.mjs"

const entry_model = new CN_proxy_form_entry_model();

export class CN_proxy_form_model extends CN_base_form_model {
  constructor() { super("proxy", entry_model); }
}

export class CN_proxy_form_adjudicate extends CN_base_form_adjudicate {
  constructor(parent_el, model) { super(parent_el, model); }
}

export class CN_proxy_form_view extends CN_base_form_view {
  constructor(parent_el, model) { super(parent_el, model); }
}
