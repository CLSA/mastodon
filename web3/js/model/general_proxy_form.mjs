import { CN_model_base_form, CN_adjudicate_base_form, CN_view_base_form } from "./base_form.mjs"
import { CN_model_general_proxy_form_entry } from "./general_proxy_form_entry.mjs"

const entry_model = new CN_model_general_proxy_form_entry();

export class CN_model_general_proxy_form extends CN_model_base_form {
  constructor() { super("general_proxy", entry_model); }
}

export class CN_adjudicate_general_proxy_form extends CN_adjudicate_base_form {
  constructor(parent_el, model) { super(parent_el, model); }
}

export class CN_view_general_proxy_form extends CN_view_base_form {
  constructor(parent_el, model) { super(parent_el, model); }
}
