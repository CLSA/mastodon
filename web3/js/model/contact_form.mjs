import { CN_base_form_model, CN_base_form_view } from "../base_form_model.mjs"

export class CN_contact_form_model extends CN_base_form_model {
  constructor() { super("contact"); }
}

export class CN_contact_form_view extends CN_base_form_view {
  constructor(model) { super("contact", model); }
}
