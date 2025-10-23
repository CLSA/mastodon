const CN_api = (await import(`${CENOZO_URL}/js/api.mjs`)).default;
const CN_common = (await import(`${CENOZO_URL}/js/common.mjs`)).default;
const CN_element = (await import(`${CENOZO_URL}/js/element.mjs`)).default;
const CN_session = (await import(`${CENOZO_URL}/js/session.mjs`)).default;

const { CN_base_action } = await import(`${CENOZO_URL}/js/base_action.mjs`);
const { CN_base_model } = await import(`${CENOZO_URL}/js/base_model.mjs`);
const { CN_base_view } = await import(`${CENOZO_URL}/js/base_view.mjs`);

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
      properties: {
        id: { title: "ID", is_constant: () => true },
        status: {
          meta: true,
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
        adjudicate: { meta: true, is_hidden: () => true },
        form_id: { meta: true, is_hidden: () => true },
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
  #entries;

  /**
   * Constructor
   * @param base_model model: The model that the action belongs to
   */
  constructor(model) {
    super("adjudicate", model);
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

  get_entries() { return this.#entries; }

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
    const model = this.get_model();

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

    // wire up the buttons
    const tfoot_el = this.get_element().querySelector("tfoot");

    const td_a_el = tfoot_el.querySelector("[name=a]");
    td_a_el.querySelector("[name=view]").onclick = async () => await CN_session.navigate_to(
      [model.get_view_url(), `${model.get_name()}_entry`, "view", this.#entries[0].id].join("/")
    );
    td_a_el.querySelector("[name=validate]").onclick = async () => {
      await CN_api.patch(model.get_view_url(null, "api"), { adjudicate: this.#entries[0].id });
      await this.on_navigate_to_parent();
    };

    const td_b_el = tfoot_el.querySelector("[name=b]");
    td_b_el.querySelector("[name=view]").onclick = async () => await CN_session.navigate_to(
      [model.get_view_url(), `${model.get_name()}_entry`, "view", this.#entries[1].id].join("/")
    );
    td_b_el.querySelector("[name=validate]").onclick = async () => {
      await CN_api.patch(model.get_view_url(null, "api"), { adjudicate: this.#entries[1].id });
      await this.on_navigate_to_parent();
    };
  }

  /**
   * Extend parent method
   */
  create_placeholder_element() {
    const tr_list = Array.from(Array(10).keys()).map((e,index) => `
      <tr>
        <th scope="row" class="text-end placeholder-glow">
        </th>
        <td class="text-center placeholder-glow">
          <span class="placeholder placeholder-lg col-${Math.ceil(Math.random()*3)+4}"></span>
        </td>
        <td class="text-center placeholder-glow">
          <span class="placeholder placeholder-lg col-${Math.ceil(Math.random()*3)+4}"></span>
        </td>
      </tr>
    `);
    return CN_element.create(`
      <table class="table table-hover">
        <thead>
          <tr>
            <th scope="col"></th>
            <th scope="col" class="text-center placeholder-glow">
              <span class="placeholder placeholder-lg col-${Math.ceil(Math.random()*3)+2}"></span>
            </th>
            <th scope="col" class="text-center placeholder-glow">
              <span class="placeholder placeholder-lg col-${Math.ceil(Math.random()*3)+2}"></span>
            </th>
          </tr>
        </thead>
        <tbody class="table-group-divider">${tr_list.join("")}</tbody>
          <tr>
            <th scope="row"></th>
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
    const body_el = CN_element.create(`
      <table class="table table-hover">
        <thead>
          <tr>
            <th scope="col" style="border-style: none;"></th>
            <th name="a" scope="col" class="text-center" style="border-style: none;"></th>
            <th name="b" scope="col" class="text-center" style="border-style: none;"></th>
          </tr>
          <tr>
            <th scope="row"></th>
            <td name="a" class="text-center">
              <div class="btn-group">
                <button name="view" type="button" class="btn btn-outline-primary">View</button>
                <button name="validate" type="button" class="btn btn-outline-primary">Validate</button>
              </div>
            </td>
            <td name="b" class="text-center">
              <div class="btn-group">
                <button name="view" type="button" class="btn btn-outline-primary">View</button>
                <button name="validate" type="button" class="btn btn-outline-primary">Validate</button>
              </div>
            </td>
          </tr>
        </thead>
        <tbody class="table-group-divider">
        </tbody>
        <tfoot class="table-group-divider">
          <tr>
            <th scope="row" style="border-style: none;"></th>
            <td name="a" class="text-center" style="border-style: none;">
              <div class="btn-group">
                <button name="view" type="button" class="btn btn-outline-primary">View</button>
                <button name="validate" type="button" class="btn btn-outline-primary">Validate</button>
              </div>
            </td>
            <td name="b" class="text-center" style="border-style: none;">
              <div class="btn-group">
                <button name="view" type="button" class="btn btn-outline-primary">View</button>
                <button name="validate" type="button" class="btn btn-outline-primary">Validate</button>
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
        tr_el.classList.add("table-active");
        tr_el.innerHTML = `
          <th style="box-shadow: none;"></th>
          <th colspan="2" class="align-bottom text-center" style="box-shadow: none; height: 4em; border-bottom: 2px black solid;">${group.title}</th>
        `;
        tbody_el.append(tr_el);
      }

      // add each of the properties in the group
      for (var prop_name in group.properties) {
        const prop = group.properties[prop_name];
        const tr_el = document.createElement("tr");
        tr_el.setAttribute("name", prop_name);
        tr_el.innerHTML = `
          <th scope="row" class="text-end">${prop.title}</th>
          <td name="a" class="text-center"></td>
          <td name="b" class="text-center"></td>
        `;
        tbody_el.append(tr_el);
      }
    }

    return body_el;
  }

  /**
   * Convenience method used by the create_footer_element() and create_topfooter_element() methods
   * @param element el
   */
  create_all_footer_elements(el) {
    // wire up the buttons
    const back_btn_el = el.querySelector("button[name=back]");
    back_btn_el.addEventListener("click", async () => await this.on_navigate_to_parent());

    const download_btn_el = el.querySelector("button[name=download]");
    download_btn_el.addEventListener("click", async () => await this.get_model().download_form());
  }

  /**
   * Extend parent method
   */
  create_footer_element() {
    const footer_el = CN_element.create(`
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
}

export class CN_base_form_view extends CN_base_view {
  /**
   * Constructor
   * @param base_model model: The model that the action belongs to
   */
  constructor(model) {
    super(model);
  }

  /**
   * Extend parent method
   */
  update_element() {
    super.update_element();

    if ("contact" != this.get_model().get_form_type()) {
      const view_btn_el = this.get_footer_element().querySelector("button[name=view]");
      if (this.get_property("form_id").state.get()) {
        view_btn_el.style.removeProperty("display");
      } else {
        view_btn_el.style.display = "none";
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
    const download_btn_el = CN_element.create(
      '<button name="download" type="button" class="btn btn-light btn-outline-primary">Download</button>'
    );
    download_btn_el.addEventListener("click", async () => await this.get_model().download_form());
    footer_el.append(download_btn_el);

    const adjudicate = this.get_property("adjudicate").state.get();
    const adjudicate_btn_el = CN_element.create(`
      <button
        name="adjudicate"
        type="button"
        class="btn btn-light btn-outline-primary"
        ${adjudicate ? "" : "disabled"}
      >Adjudicate</button>
    `);
    if (adjudicate) {
      adjudicate_btn_el.addEventListener("click", async () => {
        await CN_session.navigate_to([model.get_base_path("url"), "adjudicate", model.get_identifier()].join("/"));
      });
    }
    footer_el.append(adjudicate_btn_el);

    const form_type = this.get_model().get_form_type();
    if ("contact" != form_type) {
      const view_btn_el = CN_element.create(`
        <button
          name="view"
          type="button"
          class="btn btn-light btn-outline-primary"
          style="display: none;"
        >View Imported Form</button>
      `);
      view_btn_el.addEventListener("click", async () => {
        const form_id = this.get_property("form_id").state.get();
        if (form_id) {
          const entry_id = this.get_property("validated_form_entry_id").state.get();
          const response = await CN_api.get(`${form_type}_form_entry/${entry_id}`, {
            select: { column: "participant_id" },
          });
          await CN_session.navigate_to(`participant/view/${response.participant_id}/form/view/${form_id}`);
        }
      });
      footer_el.append(view_btn_el);
    }

    return footer_el;
  }
}
