const { CN_action_list } = await import(`${CENOZO_URL}/js/action/list.mjs`);
const { CN_action_view } = await import(`${CENOZO_URL}/js/action/view.mjs`);
const { CN_api } = await import(`${CENOZO_URL}/js/api.mjs`);
const { CN_base_action } = await import(`${CENOZO_URL}/js/action/base_action.mjs`);
const { CN_common } = await import(`${CENOZO_URL}/js/common.mjs`);
const { CN_element_card } = await import(`${CENOZO_URL}/js/element/card.mjs`);
const { CN_input_enum } = await import(`${CENOZO_URL}/js/input/enum.mjs`);
const { CN_input_file } = await import(`${CENOZO_URL}/js/input/file.mjs`);
const { CN_modal_message } = await import(`${CENOZO_URL}/js/modal/message.mjs`);
const { CN_session } = await import(`${CENOZO_URL}/js/session.mjs`);

const classes = await import(`${CENOZO_URL}/js/model/participant.mjs`);
const base_list_class = classes.CN_list_participant ? classes.CN_list_participant : CN_action_list;
const base_view_class = classes.CN_view_participant ? classes.CN_view_participant : CN_action_view;

export class CN_list_participant extends base_list_class {
  /**
   * Extends the parent method
   */
  create_footer_element() {
    const footer_el = super.create_footer_element();

    if ("participant" == CN_session.get_leaf_model().get_name()) {
      const multiedit_btn_el = this.constructor.html(
        '<button name="multiedit" class="btn btn-light btn-outline-primary">Multi-Edit</button>'
      );
      multiedit_btn_el.addEventListener("click", () => {
        CN_session.navigate_to("participant/multiedit");
      });
      footer_el.querySelector("div.btn-group").append(multiedit_btn_el);

      if (this.get_model().get_module().action_allowed("import")) {
        const import_btn_el = this.constructor.html(
          '<button name="import" class="btn btn-light btn-outline-primary">Import</button>'
        );
        import_btn_el.addEventListener("click", () => {
          CN_session.navigate_to("participant/import");
        });
        footer_el.querySelector("div.btn-group").append(import_btn_el);
      }

      const export_btn_el = this.constructor.html(
        '<button name="export" class="btn btn-light btn-outline-primary">Export</button>'
      );
      export_btn_el.addEventListener("click", () => {
        CN_session.navigate_to("export/list");
      });
      footer_el.querySelector("div.btn-group").append(export_btn_el);
    }

    return footer_el;
  }
}

export class CN_view_participant extends base_view_class {
  /**
   * Extend parent method
   */
  get_selector_child_list() {
    return super.get_selector_child_list().map(child => {
      if ("Application" == child.title) child.title = "Release";
      if ("Study" == child.title) child.title = "Eligible Study";
      return child;
    }).sort((a,b) => a.title>b.title);
  }

  /**
   * Extend parent method
   */
  create_footer_element() {
    const footer_el = super.create_footer_element();
    const left_btn_group_el = footer_el.querySelector("div[name=left-btn-group]");

    const participant_data_module = CN_session.get_module("participant_data");
    if (participant_data_module && participant_data_module.action_allowed("view")) {
      const data_btn_el = this.constructor.html(
        '<button name="data" type="button" class="btn btn-light btn-outline-primary">Data</button>'
      );
      this.constructor.set_disabled(data_btn_el, true);
      data_btn_el.addEventListener("click", async () => {
        await CN_session.navigate_to(
          this.get_model().get_view_url().replace(/participant\/view/, "participant/data")
        )
      });
      left_btn_group_el.append(data_btn_el);
    }

    return footer_el;
  }

  /**
   * Extend parent method
   */
  async on_load() {
    await super.on_load();

    const data_btn_el = this.get_footer_element().querySelector("button[name=data]");
    if (data_btn_el) {
      this.constructor.set_disabled(
        data_btn_el,
        !CN_session.get("application", "participant_data_cohort_list").includes(this.get_property_value("cohort"))
      );
    }
  }
}

export class CN_data_participant extends CN_base_action {
  #study_phase_data = [];

  /**
   * Constructor
   * @param base_model model: The model that the action belongs to
   */
  constructor(parent_el, model) {
    super("data", parent_el, model);
  }

  /**
   * Extend parent method
   */
  async get_text(type) {
    if ("crumb" == type) {
      return "Participant Data";
    }

    if ("header" == type) {
      const data = await CN_api.get(`participant/${this.get_model().get_identifier()}`, {
        select: { column: ["uid", "first_name", "last_name"] },
      });
      return `Participant data for ${data.first_name} ${data.last_name} (${data.uid})`;
    }

    return super.get_text(type);
  }

  /**
   * Extend parent method
   */
  async on_navigate_to_parent() {
    await CN_session.navigate_to(`participant/view/${this.get_model().get_identifier()}`);
  }

  /**
   * Extend parent method
   */
  async on_load() {
    await super.on_load();

    const response = await CN_api.get("participant_data", {
      identifier: this.get_model().get_identifier(),
      select: {
        column: [
          'category',
          'name',
          'filetype',
          'study_phase_id',
          'available',
          { table: 'study', column: 'name', alias: 'study' },
          { table: 'study_phase', column: 'name', alias: 'study_phase' },
        ]
      },
      modifier: { order: ['study', 'study_phase', 'category', 'name'] },
    });

    this.#study_phase_data = response.reduce((list, item) => {
      let study_phase = list.find(x => x.name == `${item.study}: ${item.study_phase}`);

      // create the study phase if it doesn't exist yet
      if (null == study_phase) {
        study_phase = {
          id: item.study_phase_id,
          name: `${item.study}: ${item.study_phase}`,
          categories: [],
        };
        list.push(study_phase);
      }

      // create the category if it doesn't exist yet
      let category = study_phase.categories.find(x => x.name == item.category);
      if (null == category) {
        category = { name: item.category, data_list: [], };
        study_phase.categories.push(category);
      }

      // now add the data item
      category.data_list.push(item);

      return list;
    }, []);
  }

  /**
   * Extend parent method
   */
  update_element() {
    const data_categories_el = this.get_body_element().querySelector("[name=data-categories]");

    // show the details in this.#study_phase_data
    data_categories_el.innerHTML = "";

    if (0 == this.#study_phase_data.length) return;
    const last_study_phase_id = this.#study_phase_data[this.#study_phase_data.length-1].id;

    const nav_el = this.constructor.html('<ul class="nav nav-tabs" role="tablist"></ul>');
    const content_el = this.constructor.html('<div class="tab-content"></div>');
    this.#study_phase_data.forEach(study_phase => {
      const active = last_study_phase_id == study_phase.id;
      const study_phase_el = this.constructor.html(`
        <li class="nav-item" role="presentation">
          <button
            class="nav-link ${active ? "active" : ""}"
            id="study-phase-${study_phase.id}-tab"
            data-bs-toggle="tab"
            data-bs-target="#study-phase-${study_phase.id}-tab-pane"
            type="button"
            role="tab"
            area-controls=="study-phase-${study_phase.id}-tab-pane"
            aria-selected="${active ? "true" : "false"}"
          >${study_phase.name}</button>
        </li>
      `);
      nav_el.append(study_phase_el);

      const tab_el = this.constructor.html(`
        <div
          class="tab-pane fade border border-top-0 pt-3 ${active ? "show active" : ""}"
          id="study-phase-${study_phase.id}-tab-pane"
          role="tabpanel"
          aria-labelledby="study-phase-${study_phase.id}-tab"
          tabindex="0"
        ></div>
      `);
      content_el.append(tab_el);

      study_phase.categories.forEach(category => {
        const category_el = this.constructor.html(`
          <div class="container-fluid pb-3">
            <div class="fs-5">${category.name}</div>
            <div class="btn-group-vertical w-100"></div>
          </div>
        `);
        tab_el.append(category_el);
        category.data_list.forEach(item => {
          const item_el = this.constructor.html(
            `<button class="btn btn-outline-primary w-100" type="button" >${item.name}</button>`
          );

          if (item.available) {
            item_el.classList.add("fw-bold");
            item_el.addEventListener("click", async () => {
              await this.constructor.wait_for(async () => {
                const response = await CN_api.file(
                  `participant_data/${item.id}`,
                  item.filetype,
                  { identifier: this.get_model().get_identifier() },
                  true,
                );
                CN_common.download_file(
                  await response.blob(),
                  response.headers.get('content-disposition').match(/filename=(.*);/)[1],
                );
              });
            });
          } else {
            this.constructor.set_disabled(item_el, true);
          }

          category_el.querySelector("div.btn-group-vertical").append(item_el);
        });
      });
    });

    data_categories_el.append(nav_el);
    data_categories_el.append(content_el);
  }

  /**
   * Extend parent method
   */
  create_body_element() {
    return this.constructor.html(`
      <div>
        <div class="text-info-emphasis pb-2">
          The following is data that is appropriate to release to the participant.
        </div>
        <div name="data-categories"></div>
      </div>
    `);
  }

  /**
   * Extend parent method
   */
  create_footer_element() {
    const footer_el = this.constructor.html(`
      <div class="btn-group" role="group">
        <button name="back" type="button" class="btn btn-primary">View Participant</button>
      </div>
    `);
    footer_el.querySelector("button[name=back]").addEventListener("click", this.on_navigate_to_parent.bind(this));
    return footer_el;
  }
}

const participant_module = CN_session.get_module("participant");
const address_module = CN_session.get_module("address");
const phone_module = CN_session.get_module("phone");

export class CN_import_participant extends CN_base_action {
  #file_form_input;
  #data = {
    source: "(loading...)",
    cohort_default: "(loading...)",
    cohort: "(loading...)",
    grouping: participant_module.get_property("grouping").max_length,
    relation_type: "(loading...)",
    honorific: participant_module.get_property("honorific").max_length,
    first_name: participant_module.get_property("first_name").max_length,
    other_name: participant_module.get_property("other_name").max_length,
    last_name: participant_module.get_property("last_name").max_length,
    sex_default: participant_module.get_property("sex").enum_list[0],
    sex: participant_module.get_property("sex").enum_list.join(", "),
    gender_identity_default: participant_module.get_property("gender_identity").enum_list[0],
    gender_identity: participant_module.get_property("gender_identity").enum_list.join(", "),
    pronouns: participant_module.get_property("pronouns").max_length,
    language_default: "(loading...)",
    language: "(loading...)",
    availability_type: "(loading...)",
    global_note: `${Math.round((participant_module.get_property("global_note").max_length)/1048576)} million`,
    address1: address_module.get_property("address1").max_length,
    address2: address_module.get_property("address2").max_length,
    city: address_module.get_property("city").max_length,
    postcode_default: CN_session.get("application", "default_postcode"),
    address_note: `${Math.round((address_module.get_property("note").max_length)/1048576)} million`,
    phone_type_default: phone_module.get_property("type").enum_list[0],
    phone_type: phone_module.get_property("type").enum_list.join(", "),
    phone_note: `${Math.round((phone_module.get_property("note").max_length)/1048576)} million`,
  };
  #valid_unique_columns;
  #valid_multi_columns;

  constructor(parent_el, model) {
    super("import", parent_el, model);

    this.#valid_unique_columns = [
      "source",
      "cohort",
      "grouping",
      "honorific",
      "first_name",
      "other_name",
      "last_name",
      "sex",
      "gender_identity",
      "pronouns",
      "date_of_birth",
      "language",
      "availability_type",
      "callback",
      "email",
      "mass_email",
      "low_education",
      "global_note",
    ];

    // add relation columns if use_relation is enabled
    if (CN_session.get("application", "use_relation")) {
      this.#valid_unique_columns = [
        ...this.#valid_unique_columns,
        ...["relationship_index", "relationship_type"],
      ];
    }

    this.#valid_multi_columns = [
      "address1",
      "address2",
      "city",
      "postcode",
      "address_note",
      "phone_type",
      "phone_number",
      "link_phone_to_address",
      "phone_note",
    ];
  }

  /**
   * Extend parent method
   */
  async get_text(type) {
    if ("crumb" == type) {
      return "Participant Import";
    }

    if ("header" == type) {
      return "Participant Import";
    }

    return super.get_text(type);
  }

  /**
   * Extend parent method
   */
  async on_navigate_to_parent() {
    await CN_session.navigate_to("participant/list");
  }

  /**
   * Extend parent method
   */
  async on_load() {
    await super.on_load();
    const [
      source_response,
      cohort_response,
      relation_type_response,
      language_response,
      availability_type_response,
    ] = await Promise.all([
      CN_api.get("source", { select: { column: "name" }, modifier: { order: "name" } }),
      CN_api.get("cohort", { select: { column: "name" }, modifier: { order: "name" } }),
      CN_api.get("relation_type", { select: { column: "name" }, modifier: { order: "rank" } }),
      CN_api.get("language", {
        select: { column: "code" },
        modifier: { where: { column: "active", operator: "=", value: true }, order: "code" } }
      ),
      CN_api.get("availability_type", { select: { column: "name" }, modifier: { order: "name" } }),
    ]);

    this.#data.source = source_response.map(r => r.name).join(", ");
    this.#data.cohort = cohort_response.map(r => r.name).join(", ");
    this.#data.cohort_default = cohort_response[0].name;
    this.#data.relation_type = relation_type_response.map(r => r.name).join(", ");
    this.#data.language_default = language_response[0].code;
    this.#data.language = language_response.map(r => r.code).join(", ");
    this.#data.availability_type = availability_type_response.map(r => r.name).join(", ");
  }

  /**
   * Extend parent method
   */
  update_element() {
    const body_el = this.get_body_element();

    body_el.querySelector("td[name=source]").innerHTML =
      `Must either be empty or one of the following: <span class="fst-italic">${this.#data.source}</span>`;
    body_el.querySelector("td[name=cohort-default]").innerHTML =
      `${this.#data.cohort_default}`;
    body_el.querySelector("td[name=cohort]").innerHTML =
      `Must be one of the following: <span class="fst-italic">${this.#data.cohort}</span>`;
    body_el.querySelector("td[name=grouping]").innerHTML =
      `Can be empty or any text up to ${this.#data.grouping} characters long.`;
    if (CN_session.get("application", "use_relation")) {
      body_el.querySelectorAll("tr[name=relation]").forEach(e => e.classList.remove("d-none"));
      body_el.querySelector("td[name=relation-type]").innerHTML = `
        Must be one of the following: <span class="fst-italic">${this.#data.relation_type}</span><br\>
        Can only be left blank if the relationship_index column is also blank.
      `;
    }
    body_el.querySelector("td[name=honorific]").innerHTML =
      `Can be empty or any text up to ${this.#data.honorific} characters long.`;
    body_el.querySelector("td[name=first-name]").innerHTML =
      `Can be empty or any text up to ${this.#data.first_name} characters long.`;
    body_el.querySelector("td[name=other-name]").innerHTML =
      `Can be empty or any text up to ${this.#data.other_name} characters long.`;
    body_el.querySelector("td[name=last-name]").innerHTML =
      `Can be empty or any text up to ${this.#data.last_name} characters long.`;
    body_el.querySelector("td[name=sex-default]").innerHTML =
      `${this.#data.sex_default}`;
    body_el.querySelector("td[name=sex]").innerHTML =
      `Must be one of the following: <span class="fst-italic">${this.#data.sex}</span>`;
    body_el.querySelector("td[name=gender-identity-default]").innerHTML =
      `${this.#data.gender_identity_default}`;
    body_el.querySelector("td[name=gender-identity]").innerHTML =
      `Must be one of the following: <span class="fst-italic">${this.#data.gender_identity}</span>`;
    body_el.querySelector("td[name=pronouns]").innerHTML =
      `Can be empty or any text up to ${this.#data.pronouns} characters long.`;
    body_el.querySelector("td[name=language-default]").innerHTML =
      `${this.#data.language_default}`;
    body_el.querySelector("td[name=language]").innerHTML =
      `Must be one of the following: <span class="fst-italic">${this.#data.language}</span>`;
    body_el.querySelector("td[name=availability-type]").innerHTML =
      `Can be empty or one of the following: <span class="fst-italic">${this.#data.availability_type}</span>`;
    body_el.querySelector("td[name=global-note]").innerHTML =
      `Can be empty or any text up to ${this.#data.global_note} characters long.`;
    body_el.querySelector("td[name=address1]").innerHTML =
      `Any text up to ${this.#data.address1} characters long.`;
    body_el.querySelector("td[name=address2]").innerHTML =
      `Can be empty or any text up to ${this.#data.address2} characters long.`;
    body_el.querySelector("td[name=city]").innerHTML =
      `Any text up to ${this.#data.city} characters long.`;
    body_el.querySelector("td[name=postcode-default]").innerHTML =
      `${this.#data.postcode_default}`;
    body_el.querySelector("td[name=address-note]").innerHTML =
      `Can be empty or any text up to ${this.#data.address_note} characters long.`;
    body_el.querySelector("td[name=phone-type-default]").innerHTML =
      `${this.#data.phone_type_default}`;
    body_el.querySelector("td[name=phone-type]").innerHTML =
      `Must be one of the following: <span class="fst-italic">${this.#data.phone_type}</span>`;
    body_el.querySelector("td[name=phone-note]").innerHTML =
      `Can be empty or any text up to ${this.#data.phone_note} characters long.`;
  }

  /**
   * Extend parent method
   */
  create_body_element() {
    const body_el = this.constructor.html(`
      <div>
        <div class="m-2 text-info-emphasis">
          This utility allows one or more participants to be imported from a CSV file.
          <ul>
            <li>
              The file must be encoded using the "Unicode UTF-8" character set, using a comma (,) as the field
              delimiter and double-quote (") text delimiter
            </li>
            <li>The first row must be a list of column headers</li>
            <li>Any column which has a header not found in the follow list will be ignored</li>
            <li>
              Any column which is not included in the following list will be given the default value as listed
              below
            </li>
            <li>If a duplicate column is detected the import will not proceed</li>
            <li>Columns can be in any order</li>
          </ul>
          Click one of the following headings for a description of all columns which may be included in the CSV
          file:
        </div>
        <div class="accordion accordion-flush mb-2" id="instructions">
          <div class="accordion-item">
            <div class="accordion-header">
              <button
                class="accordion-button fs-6 fw-bold"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#participant-details"
                aria-expanded="true"
                aria-controls="participant-details"
              >Participant Details</button>
            </div>
            <div id="participant-details" class="accordion-collapse collapse">
              <div class="accordion-body p-0">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th scope="col">Column Name</th>
                      <th scope="col">Default</th>
                      <th scope="col">Notes</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="allow-select">source</td>
                      <td>(empty)</td>
                      <td name="source"></td>
                    </tr>
                    <tr>
                      <td class="allow-select">cohort</td>
                      <td name="cohort-default"></td>
                      <td name="cohort"></td>
                    </tr>
                    <tr>
                      <td class="allow-select">grouping</td>
                      <td>(empty)</td>
                      <td name="grouping"></td>
                    </tr>
                    <tr name="relation" class="d-none">
                      <td class="allow-select">relationship_index</td>
                      <td>(empty)</td>
                      <td>
                        Must be an existing UID, or "self" if this participant is the relationship Index.<br\>
                        Can only be left blank if the relationship_type column is also blank.
                      </td>
                    </tr>
                    <tr name="relation" class="d-none">
                      <td class="allow-select">relationship_type</td>
                      <td>(empty)</td>
                      <td name="relation-type"></td>
                    </tr>
                    <tr>
                      <td class="allow-select">honorific</td>
                      <td>(empty)</td>
                      <td name="honorific"></td>
                    </tr>
                    <tr>
                      <td class="allow-select">first_name</td>
                      <td>"Unknown"</td>
                      <td name="first-name"></td>
                    </tr>
                    <tr>
                      <td class="allow-select">other_name</td>
                      <td>(empty)</td>
                      <td name="other-name"></td>
                    </tr>
                    <tr>
                      <td class="allow-select">last_name</td>
                      <td>"Unknown"</td>
                      <td name="last-name"></td>
                    </tr>
                    <tr>
                      <td class="allow-select">sex</td>
                      <td name="sex-default"></td>
                      <td name="sex"></td>
                    </tr>
                    <tr>
                      <td class="allow-select">gender_identity</td>
                      <td name="gender-identity-default"></td>
                      <td name="gender-identity"></td>
                    </tr>
                    <tr>
                      <td class="allow-select">pronouns</td>
                      <td>(empty)</td>
                      <td name="pronouns"></td>
                    </tr>
                    <tr>
                      <td class="allow-select">date_of_birth</td>
                      <td>(empty)</td>
                      <td>Can be empty or a date in YYYY-MM-DD format.</td>
                    </tr>
                    <tr>
                      <td class="allow-select">language</td>
                      <td name="language-default"></td>
                      <td name="language"></td>
                    </tr>
                    <tr>
                      <td class="allow-select">availability-type</td>
                      <td>(empty)</td>
                      <td name="availability-type"></td>
                    </tr>
                    <tr>
                      <td class="allow-select">callback</td>
                      <td>(empty)</td>
                      <td>Can be empty or a datetime in YYYY-MM-DD HH:MM:SS format.</td>
                    </tr>
                    <tr>
                      <td class="allow-select">email</td>
                      <td>(empty)</td>
                      <td>Can be empty or be a correctly formatted email address.</td>
                    </tr>
                    <tr>
                      <td class="allow-select">mass_email</td>
                      <td>true</td>
                      <td>Must either be true or false.</td>
                    </tr>
                    <tr>
                      <td class="allow-select">low_education</td>
                      <td>false</td>
                      <td>Must either be true or false.</td>
                    </tr>
                    <tr>
                      <td class="allow-select">global_note</td>
                      <td>(empty)</td>
                      <td name="global-note"></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="accordion-item">
            <div class="accordion-header">
              <button
                class="accordion-button fs-6 fw-bold"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#address-details"
                aria-expanded="true"
                aria-controls="address-details"
              >Address Details</button>
            </div>
            <div id="address-details" class="accordion-collapse collapse">
              <div class="accordion-body p-0">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th scope="col">Column Name</th>
                      <th scope="col">Default</th>
                      <th scope="col">Notes</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="allow-select">address1</td>
                      <td>"Unknown"</td>
                      <td name="address1"></td>
                    </tr>
                    <tr>
                      <td class="allow-select">address2</td>
                      <td>(empty)</td>
                      <td name="address2"></td>
                    </tr>
                    <tr>
                      <td class="allow-select">city</td>
                      <td>"Unknown"</td>
                      <td name="city"></td>
                    </tr>
                    <tr>
                      <td class="allow-select">postcode</td>
                      <td name="postcode-default"></td>
                      <td>Any postal code in A1A 1A1 format or zip code in 00000 format.</td>
                    </tr>
                    <tr>
                      <td class="allow-select">address_note</td>
                      <td>(empty)</td>
                      <td name="address-note"></td>
                    </tr>
                  </tbody>
                </table>
                <div class="m-2 text-info-emphasis">
                  More than one address can be added by including a numbered suffix at the end of all address
                  columns (e.g.: address1_2, city_2 etc...)<br/>
                  When using numbered suffixes numbering should start with 1 and continue sequentially without
                  missing any numbers.
                </div>
              </div>
            </div>
          </div>
          <div class="accordion-item">
            <div class="accordion-header">
              <button
                class="accordion-button fs-6 fw-bold"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#phone-details"
                aria-expanded="true"
                aria-controls="phone-details"
              >Phone Details</button>
            </div>
            <div id="phone-details" class="accordion-collapse collapse">
              <div class="accordion-body p-0">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th scope="col">Column Name</th>
                      <th scope="col">Default</th>
                      <th scope="col">Notes</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="allow-select">phone_type</td>
                      <td name="phone-type-default"></td>
                      <td name="phone-type"></td>
                    </tr>
                    <tr>
                      <td class="allow-select">phone_number</td>
                      <td>555-555-5555</td>
                      <td>
                        Must be any valid North American phone number with exactly 10 digits (without a leading
                        0 or 1).
                      </td>
                    </tr>
                    <tr>
                      <td class="allow-select">link_phone_to_address</td>
                      <td>true</td>
                      <td>Whether to link the phone number to the given address.  Must be true or false.</td>
                    </tr>
                    <tr>
                      <td class="allow-select">phone_note</td>
                      <td>(empty)</td>
                      <td name="phone-note"></td>
                    </tr>
                  </tbody>
                </table>
                <div class="container-fluid text-info-emphasis">
                  More than one phone number can be added by including a numbered suffix at the end of all phone
                  columns (e.g.: phone_type_2, phone_number_2, etc...)<br/>
                  When using numbered suffixes numbering should start with 1 and continue sequentially without
                  missing any numbers.<br/>
                  If link_phone_to_address is true then the phone number will be linked to the address with the
                  same number, or the first address if there is no matching address with the same number.
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    `);

    const card = CN_element_card.append(body_el, {
      header: "Import File",
      body: "",
    });
    const card_body_el = card.get_element().querySelector("div.card-body");

    this.#file_form_input = new CN_input_file(card_body_el, {
      id: "file",
      type: "file",
      file: { encoding: "text", mime_type: "text/csv" },
      on_change: async (form_input) => {
        const csv_file = await CN_common.convert_from_blob("text", form_input.get_value()[0]);
        this.#file_form_input.set_value(null);
        await this.#import_csv_data(CN_common.parse_csv(csv_file));
      },
    });
    card_body_el.append(this.#file_form_input.get_element());

    return body_el;
  }

  /**
   * Extend parent method
   */
  create_footer_element() {
    const footer_el = this.constructor.html(`
      <div class="d-flex w-100">
        <div class="me-auto btn-group" role="group" name="right-btn-group"></div>
        <div class="btn-group" role="group" name="left-btn-group">
          <button name="back" type="button" class="btn btn-primary">View Participant List</button>
        </div>
      </div>
    `);
    footer_el.querySelector("button[name=back]").addEventListener("click", this.on_navigate_to_parent.bind(this));
    return footer_el;
  }

  /**
   * Extend parent method
   */
  async #import_csv_data(data) {
    // assume the first line is a header and mark all invalid column names
    const header = data.shift().map(col => {
      const name = col.trim().toLowerCase().replace(" ", "_");
      return (
        this.#valid_unique_columns.includes(name) ||
        this.#valid_multi_columns.includes(name.replace(/_[0-9]+$/, "")) ?
        name :
        null
      );
    });

    if (0 == header.filter(col => null != col).length) {
      await CN_modal_message.create_and_open({
        title: "Unable to Parse File",
        type: "danger",
        message: `
          <div class="pb-2">There were no valid columns in the first line of the file.</div>
          <div>
            Please check that the first line of the CSV file contains the column names from the list above.
          </div>
        `,
      });
    } else {
      const participant_list = data.reduce((list, row) => {
        const participant = row.reduce((participant, value, index) => {
          if (null != header[index]) participant[header[index]] = value;
          return participant;
        }, {});

        if (0 < Object.keys(participant).length) list.push(participant);
        return list;
      }, []);

      const response = await CN_api.post("participant", participant_list);
      let message = `
        <div class="mb-2">
          A total of ${participant_list.length - response.length} out of ${participant_list.length}
          participants have been imported.
        </div>
      `;
      if (0 < response.length) {
        // format the list of errors returned by the post
        message += `<ul>${
          response.map(r => `<li>${r.replace(/^.+:/, '<span class="fw-bold">$&</span>')}</li>`).join("\n")
        }</ul>`;
      }
      await CN_modal_message.create_and_open({ title: "Import Results", size: "xl", message: message });
    }
  }
}

export class CN_release_participant extends CN_base_action {
  #application_list = [];

  /**
   * Constructor
   * @param base_model model: The model that the action belongs to
   */
  constructor(parent_el, model) {
    super("release", parent_el, model);
  }

  /**
   * Extend parent method
   */
  async get_text(type) {
    if ("crumb" == type) {
      return (await CN_api.get(
        `participant/${this.get_model().get_identifier()}`,
        { select: { column: "uid" },
      })).uid;
    }

    if ("header" == type) {
      const data = await CN_api.get(`participant/${this.get_model().get_identifier()}`, {
        select: { column: ["uid", "first_name", "last_name"] },
      });
      return `Application Management for ${data.first_name} ${data.last_name} (${data.uid})`;
    }

    return super.get_text(type);
  }

  /**
   * Extend parent method
   */
  async on_navigate_to_parent() {
    await CN_session.navigate_to(`participant/view/${this.get_model().get_identifier()}`);
  }

  /**
   * Extend parent method
   */
  async on_load() {
    await super.on_load();

    // get the application list
    this.#application_list = await CN_api.get(`participant/${this.get_model().get_identifier()}/application`, {
      select: { column: ["title", "release_based", "datetime", "default_site_id", "preferred_site_id"] },
      modifier: { order: "title" },
    });

    // get the site list for all applications in parallel
    await Promise.all(this.#application_list.map(application => (async () => {
      application.site_list = await CN_api.get(`application/${application.id}/site`, {
        select: { column: "name" },
        modifier: { order: "name" },
      });
    })()));
  }

  /**
   * Extend parent method
   */
  update_element() {
    const tbody_el = this.get_body_element().querySelector("tbody");
    tbody_el.innerHTML = "";

    this.#application_list.forEach(application => {
      const tr_el = this.constructor.html(`
        <tr class="align-middle">
          <td name="title" class="fw-bold">${application.title}</td>
          <td name="released"></td>
          <td name="default-site">${
            application.default_site_id ?
            application.site_list.find(site => application.default_site_id == site.id).name :
            "(none)"
          }</td>
          <td name="preferred-site"></td>
        </tr>
      `);

      const released_el = tr_el.querySelector("td[name=released]");
      if (application.release_based) {
        if (null == application.datetime) {
          const release_btn_el = this.constructor.html(
            '<button class="btn btn-outline-primary w-75">Release Now</button>'
          );
          release_btn_el.addEventListener("click", async () => {
            this.constructor.set_disabled(release_btn_el, true);
            try {
              await this.constructor.wait_for(async () => {
                await CN_api.post(
                  `application/${application.id}/participant`,
                  Number(this.get_model().get_identifier()),
                );
                release_btn_el.remove();
                application.datetime = CN_common.format_datetime(new Date, "record");
                released_el.innerHTML = CN_common.format_datetime(new Date, "datetime");
              });
            } finally {
              this.constructor.set_disabled(release_btn_el, false);
            }
          });
          released_el.append(release_btn_el);
        } else {
          released_el.innerHTML = CN_common.format_datetime(application.datetime, "datetime");
        }
      } else {
        released_el.innerHTML = "Not release-based";
      }

      CN_input_enum.append(tr_el.querySelector("td[name=preferred-site]"), {
        enum: { values: application.site_list.map(site => ({ key: site.id, value: site.name })) },
        get_default: () => application.preferred_site_id,
        on_change: async (form_input) => {
          form_input.set_disabled(true);
          try {
            await CN_api.patch(this.get_model().get_view_url(null, "api"), {
              application_id: application.id,
              preferred_site_id: await form_input.get_value_for_record(),
            });
          } finally {
            form_input.set_disabled(false);
          }
        },
      });

      tbody_el.append(tr_el);
    });
  }

  /**
   * Extend parent method
   */
  create_body_element() {
    return this.constructor.html(`
      <div>
        <div class="text-info-emphasis pb-2">
          Below is a list of all applications the participant may be released to.
          You may update the participant's preferred site or release them now.
        </div>
        <div class="text-info-emphasis text-danger pb-2">
          Warning: Once a participant has been released they cannot be unreleased.
        </div>
        <div class="table-responsive">
          <table class="table table-striped m-0">
            <thead>
              <tr>
                <th scope="col"></th>
                <th scope="col">Released</th>
                <th scope="col">Default Site</th>
                <th scope="col">Preferred Site</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    `);
  }

  /**
   * Extend parent method
   */
  create_footer_element() {
    const footer_el = this.constructor.html(`
      <div class="btn-group" role="group">
        <button name="back" type="button" class="btn btn-primary">View Participant</button>
      </div>
    `);
    footer_el.querySelector("button[name=back]").addEventListener("click", this.on_navigate_to_parent.bind(this));
    return footer_el;
  }
}
