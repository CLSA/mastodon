const { CN_api } = await import(`${CENOZO_URL}/js/api.mjs`);
const { CN_common } = await import(`${CENOZO_URL}/js/common.mjs`);
const { CN_session } = await import(`${CENOZO_URL}/js/session.mjs`);

const { CN_base_action } = await import(`${CENOZO_URL}/js/element/action/base_action.mjs`);
const { CN_base_model } = await import(`${CENOZO_URL}/js/model/base_model.mjs`);
const { CN_action_view } = await import(`${CENOZO_URL}/js/element/action/view.mjs`);

export class CN_base_form_model extends CN_base_model {
  #form_type;
  #entry_model;

  constructor(form_type, entry_model) {
    let columns  = { id: { title: "ID" } };
    if ("contact" != form_type) {
      columns = {
        ...columns,
        cohort: {
          column: `${form_type}_form_total.cohort`,
          title: "Cohort",
          help: "A list of all cohorts entered by typists for this form (separated by a comma).",
        },
        uid: {
          column: `${form_type}_form_total.uid`,
          title: "UID",
          help: "A list of all UIDs entered by typists for this form (separated by a comma).",
        },
      };
    }
    columns = {
      ...columns,
      status: {
        title: "Status",
        help: 'One of "completed", "invalid", "adjudication", "started" or "new".',
        table_prefix: false,
      },
      entry_total: {
        column: `${form_type}_form_total.entry_total`,
        title: "Entries",
        type: "number",
      },
      submitted_total: {
        column: `${form_type}_form_total.submitted_total`,
        title: "Submitted Entries",
        type: "number",
      },
      date: { title: "Date", type: "date" },
    };

    super({
      wording: {
        singular: entry_model.get_singular().replace( /form entry$/, "form" ),
        plural: entry_model.get_plural().replace( /form entries$/, "forms" ),
        posessive: entry_model.get_posessive().replace( /form entry's$/, "form's" ),
      },
      columns: columns,
      default_order: { column: "id", desc: true }, // sort by ID descending
      properties: {
        id: { title: "ID", is_constant: () => true },
        status: {
          meta: {}, // predefined by the service
          title: "Status",
          is_constant: () => true,
          help:
            'Set to "completed" when done, ' +
            '"invalid" when marked invalid, ' +
            '"adjudication" when two entries have been submitted but do not match, ' +
            '"started" when there are less than two entries submitted and ' +
            '"new" when no entries have been submitted.',
        },
        completed: { title: "Complete", type: "boolean", is_constant: () => true },
        invalid: { title: "Invalid", type: "boolean" },
        date: { title: "Date", type: "date" },
        adjudicate: { meta: {}, is_hidden: () => true },
        form_id: { meta: {}, is_hidden: () => true },
        validated_form_entry_id: {
          meta: { table: `${form_type}_form`, column: `validated_${form_type}_form_entry_id` },
          is_hidden: () => true,
        }
      },
    });

    this.#form_type = form_type;
    this.#entry_model = entry_model;
  }

  /**
   * Returns the form type
   * @return string
   */
  get_form_type() { return this.#form_type; }

  /**
   * Returns the entry model
   * @return base_model
   */
  get_entry_model() { return this.#entry_model; }

  /**
   * Downloads the form
   */
  async download_form() {
    const response = await CN_api.file(this.get_view_url(null, "api"), "application/pdf", {}, true);
    CN_common.download_file(
      await response.blob(),
      response.headers.get('content-disposition').match(/filename=(.*);/)[1],
    );
  }
}

export class CN_base_form_adjudicate extends CN_base_action {
  #property_groups;
  #entries = [];

  /**
   * Constructor
   * @param base_model model: The model that the action belongs to
   */
  constructor(parent_el, model) {
    super("adjudicate", parent_el, model);
    this.set_footer_at_top(true);

    // add all form groups from the entry model
    this.#property_groups = { "$main": { title: null, properties: {} } };
    const properties = this.get_model().get_entry_model().clone_properties();
    for (var prop_name in properties) {
      if ("user_id" == prop_name) continue;
      if (properties[prop_name].hasOwnProperty("properties")) {
        this.#property_groups[prop_name] = properties[prop_name];
      } else {
        this.#property_groups["$main"].properties[prop_name] = properties[prop_name];
      }
    }
  }

  /**
   * Extend parent method
   */
  async get_text(type) {
    const model = this.get_model();

    if ("crumb" == type) {
      return `${CN_common.uc_words(model.get_singular())} #${model.get_identifier()}`;
    }

    if ("header" == type) {
      return `Adjudication for ${CN_common.uc_words(model.get_singular())} #${model.get_identifier()}`;
    }

    return super.get_text(type);
  }

  /**
   * Extend parent method
   */
  async on_navigate_to_parent() {
    await CN_session.navigate_to(this.get_model().get_view_url());
  }

  /**
   * Extend parent method
   */
  async on_load() {
    await super.on_load();

    const model = this.get_model();
    this.#entries = await CN_api.get(`${model.get_view_url(null, "api")}/${model.get_name()}_entry`);
  }

  /**
   * Extend parent method
   */
  update_element() {
    super.update_element();

    if (0 == this.#entries.length) return;

    // add in the header data
    const thead_el = this.get_element().querySelector("thead");
    thead_el.querySelector("[name=a]").innerHTML = this.#entries[0].user;
    thead_el.querySelector("[name=b]").innerHTML = this.#entries[1].user;

    // fill in the body data

    for (var group_name in this.#property_groups) {
      const group = this.#property_groups[group_name];
      Object.keys(group.properties).forEach(prop_name => {
        const tr_el = this.get_element().querySelector(`tr[name=${prop_name}]`);

        const val_a = "participant_id" == prop_name ? this.#entries[0].uid : this.#entries[0][prop_name];
        const td_a_el = tr_el.querySelector("[name=a]");
        td_a_el.innerHTML = val_a;

        const val_b = "participant_id" == prop_name ? this.#entries[1].uid : this.#entries[1][prop_name];
        const td_b_el = tr_el.querySelector("[name=b]");
        td_b_el.innerHTML = val_b;

        if (val_a != val_b) {
          td_a_el.classList.add("table-warning");
          td_b_el.classList.add("table-warning");
        }
      });
    }
  }

  /**
   * Extend parent method
   */
  create_placeholder_element() {
    const tr_list = Array.from(Array(10).keys()).map((e,index) => `
      <tr>
        <td scope="row" class="text-end placeholder-glow">
        </td>
        <td class="text-center placeholder-glow">
          <span class="placeholder placeholder-lg col-${Math.ceil(Math.random()*3)+4}"></span>
        </td>
        <td class="text-center placeholder-glow">
          <span class="placeholder placeholder-lg col-${Math.ceil(Math.random()*3)+4}"></span>
        </td>
      </tr>
    `);
    return this.constructor.html(`
      <table class="table table-hover">
        <thead>
          <tr>
            <td scope="col"></td>
            <td scope="col" class="text-center placeholder-glow">
              <span class="placeholder placeholder-lg col-${Math.ceil(Math.random()*3)+2}"></span>
            </td>
            <td scope="col" class="text-center placeholder-glow">
              <span class="placeholder placeholder-lg col-${Math.ceil(Math.random()*3)+2}"></span>
            </td>
          </tr>
        </thead>
        <tbody class="table-group-divider">${tr_list.join("")}</tbody>
          <tr>
            <td scope="row"></td>
            <td class="text-center"></td>
            <td class="text-center"></td>
          </tr>
        </tfoot>
      </table>
    `);
  }

  /**
   * Extend parent method
   */
  create_body_element() {
    const body_el = this.constructor.html(`
      <table class="table table-hover">
        <thead>
          <tr>
            <th scope="col" class="border-0"></th>
            <th name="a" scope="col" class="text-center border-0"></th>
            <th name="b" scope="col" class="text-center border-0"></th>
          </tr>
          <tr>
            <td scope="row"></td>
            <td name="a" class="text-center">
              <div class="btn-group">
                <button name="view_a" type="button" class="btn btn-outline-primary">View</button>
                <button name="validate_a" type="button" class="btn btn-outline-primary">Validate</button>
              </div>
            </td>
            <td name="b" class="text-center">
              <div class="btn-group">
                <button name="view_b" type="button" class="btn btn-outline-primary">View</button>
                <button name="validate_b" type="button" class="btn btn-outline-primary">Validate</button>
              </div>
            </td>
          </tr>
        </thead>
        <tbody class="table-group-divider">
        </tbody>
        <tfoot class="table-group-divider">
          <tr>
            <td scope="row" class="border-0"></td>
            <td name="a" class="text-center border-0">
              <div class="btn-group">
                <button name="view_a" type="button" class="btn btn-outline-primary">View</button>
                <button name="validate_a" type="button" class="btn btn-outline-primary">Validate</button>
              </div>
            </td>
            <td name="b" class="text-center border-0">
              <div class="btn-group">
                <button name="view_b" type="button" class="btn btn-outline-primary">View</button>
                <button name="validate_b" type="button" class="btn btn-outline-primary">Validate</button>
              </div>
            </td>
          </tr>
        </tfoot>
      </table>
    `);

    const tbody_el = body_el.querySelector("tbody");
    for (var group_name in this.#property_groups) {
      const group = this.#property_groups[group_name];

      // add the group header
      if ("$main" != group_name) {
        const tr_el = document.createElement("tr");
        tr_el.innerHTML = `
          <th class="bg-body border-0"></th>
          <th colspan="2" class="table-active align-bottom text-center">${group.title}</th>
        `;
        tbody_el.append(tr_el);
      }

      // add each of the properties in the group
      for (var prop_name in group.properties) {
        const prop = group.properties[prop_name];
        const tr_el = document.createElement("tr");
        tr_el.setAttribute("name", prop_name);
        tr_el.innerHTML = `
          <th scope="row" class="text-end border-0">${prop.title}</th>
          <td name="a" class="text-center"></td>
          <td name="b" class="text-center"></td>
        `;
        tbody_el.append(tr_el);
      }
    }

    // wire up the buttons
    const model = this.get_model();
    const tfoot_el = body_el.querySelector("tfoot");

    Array.from(body_el.querySelectorAll("button[name=view_a]")).forEach(
      btn_el => btn_el.addEventListener("click", this._view_entry.bind(this, 0))
    );
    Array.from(body_el.querySelectorAll("button[name=view_b]")).forEach(
      btn_el => btn_el.addEventListener("click", this._view_entry.bind(this, 1))
    );
    Array.from(body_el.querySelectorAll("button[name=validate_a]")).forEach(
      btn_el => btn_el.addEventListener("click", this._adjudicate_entry.bind(this, 0))
    );
    Array.from(body_el.querySelectorAll("button[name=validate_b]")).forEach(
      btn_el => btn_el.addEventListener("click", this._adjudicate_entry.bind(this, 1))
    );

    const td_b_el = tfoot_el.querySelector("[name=b]");
    return body_el;
  }

  /**
   * Convenience method used by the create_footer_element() and create_topfooter_element() methods
   * @param element el
   */
  create_all_footer_elements(el) {
    // wire up the buttons
    const back_btn_el = el.querySelector("button[name=back]");
    back_btn_el.addEventListener("click", this.on_navigate_to_parent.bind(this));

    const download_btn_el = el.querySelector("button[name=download]");
    download_btn_el.addEventListener("click", this.get_model().download_form.bind(this));
  }

  /**
   * Extend parent method
   */
  create_footer_element() {
    const footer_el = this.constructor.html(`
      <div class="btn-group" role="group">
        <button name="back" type="button" class="btn btn-primary">View ${this.get_model().get_singular()}</button>
        <button name="download" type="button" class="btn btn-light btn-outline-primary">Download</button>
      </div>
    `);
    this.create_all_footer_elements(footer_el);
    return footer_el;
  }

  /**
   * Extend parent method
   */
  create_topfooter_element() {
    // no need to create the top-footer as it gets cloned from the footer
    const topfooter_el = super.create_topfooter_element();
    this.create_all_footer_elements(topfooter_el);
    return topfooter_el;
  }

  /**
   * ADD DOCS
   */
  async _view_entry(index) {
    const model = this.get_model();
    await CN_session.navigate_to([
      model.get_view_url(),
      `${model.get_name()}_entry`,
      "view",
      this.#entries[index].id
    ].join("/"));
  }

  /**
   * ADD DOCS
   */
  async _adjudicate_entry(index) {
    const btn_el_list = Array.from(this.get_body_element().querySelectorAll("button"));
    btn_el_list.forEach(el => el.setAttribute("disabled", true));
    try {
      await this.constructor.wait_for(async () => {
        await CN_api.patch(
          this.get_model().get_view_url(null, "api"),
          { adjudicate: this.#entries[index].id }
        );
      });
    } catch (error) {
      // convert newlines in error message to line breaks
      error.message = error.message.replace(/\n/g, "<br/>\n");
      throw error;
    } finally {
      btn_el_list.forEach(el => el.removeAttribute("disabled"));
    }
    await this.on_navigate_to_parent();
  }
}

export class CN_base_form_view extends CN_action_view {
  #view_imported_form_btn_el;

  /**
   * Constructor
   * @param base_model model: The model that the action belongs to
   */
  constructor(parent_el, model) {
    super(parent_el, model);
  }

  /**
   * Extend parent method
   */
  update_element() {
    super.update_element();

    const footer_el = this.get_footer_element();

    const adjudicate_btn_el = footer_el.querySelector("button[name=adjudicate]");
    if (this.get_property_value("adjudicate")) {
      adjudicate_btn_el.removeAttribute("disabled");
    } else {
      adjudicate_btn_el.setAttribute("disabled", true);
    }

    if ("contact" != this.get_model().get_form_type()) {
      if (this.get_property_value("form_id")) {
        footer_el.append(this.#view_imported_form_btn_el);
      } else {
        this.#view_imported_form_btn_el.remove();
      }
    }
  }

  /**
   * Add operations to the footer element
   */
  create_footer_element() {
    const model = this.get_model();
    const footer_el = super.create_footer_element();

    // add all form actions
    const download_btn_el = this.constructor.html(
      '<button name="download" type="button" class="btn btn-light btn-outline-primary">Download</button>'
    );
    download_btn_el.addEventListener("click", this.get_model().download_form.bind(this));
    footer_el.append(download_btn_el);

    const adjudicate_btn_el = this.constructor.html(`
      <button
        name="adjudicate"
        type="button"
        class="btn btn-light btn-outline-primary"
        disabled
      >Adjudicate</button>
    `);
    adjudicate_btn_el.addEventListener("click", async () => {
      await CN_session.navigate_to([model.get_base_path("url"), "adjudicate", model.get_identifier()].join("/"));
    });
    footer_el.append(adjudicate_btn_el);

    // create the view imported form button but don't add it (that's done when updating the element)
    const form_type = this.get_model().get_form_type();
    if ("contact" != form_type) {
      this.#view_imported_form_btn_el = this.constructor.html(
        '<button name="view" type="button" class="btn btn-light btn-outline-primary">View Imported Form</button>'
      );
      this.#view_imported_form_btn_el.addEventListener("click", async () => {
        const form_id = this.get_property_value("form_id");
        if (form_id) {
          const entry_id = this.get_property_value("validated_form_entry_id");
          const response = await CN_api.get(`${form_type}_form_entry/${entry_id}`, {
            select: { column: "participant_id" },
          });
          await CN_session.navigate_to(`participant/view/${response.participant_id}/form/view/${form_id}`);
        }
      });
    }

    return footer_el;
  }
}
