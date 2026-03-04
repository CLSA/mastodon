const { CN_api } = await import(`${CENOZO_URL}/js/api.mjs`);
const { CN_common } = await import(`${CENOZO_URL}/js/common.mjs`);
const { CN_session } = await import(`${CENOZO_URL}/js/session.mjs`);

const { CN_base_action } = await import(`${CENOZO_URL}/js/element/action/base_action.mjs`);
const { CN_action_view } = await import(`${CENOZO_URL}/js/element/action/view.mjs`);
const classes = await import(`${CENOZO_URL}/js/model/participant.mjs`);

const base_view_class = classes.CN_participant_view ? classes.CN_participant_view : CN_action_view;
export class CN_participant_view extends base_view_class {
  /**
   * Extend parent method
   */
  get_selector_child_list() {
    return super.get_selector_child_list().map(child => {
      if ("Applications" == child.title) child.title = "Release";
      if ("Studies" == child.title) child.title = "Eligible Studies";
      return child;
    }).sort((a,b) => a.title>b.title);
  }

  /**
   * Extend parent method
   */
  create_footer_element() {
    const footer_el = super.create_footer_element();

    if (
      ["administrator", "curator"].includes(CN_session.data.role.name) &&
      CN_session.data.application.participant_data_cohort_list.includes(this.get_property_value("cohort"))
    ) {
      const token_module = CN_session.get_module("token");
      if (token_module && token_module.action_allowed("add")) {
        const data_btn_el = this.constructor.html(
          '<button name="data" type="button" class="btn btn-light btn-outline-primary">Data</button>'
        );
        data_btn_el.addEventListener("click", async () => {
          await CN_session.navigate_to(
            this.get_model().get_view_url().replace(/participant\/view/, "participant/data")
          )
        });
        footer_el.append(data_btn_el);
      }
    }

    return footer_el;
  }
}

export class CN_participant_data extends CN_base_action {
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
          </div>
        `);
        tab_el.append(category_el);
        category.data_list.forEach(item => {
          const item_el = this.constructor.html(`
            <button
              class="btn btn-outline-primary w-100 ${item.available ? "fw-bold" : ""}"
              type="button"
              ${item.available ? "" : "disabled"}
            >${item.name}</button>
          `);

          if (item.available) {
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
          }

          category_el.append(item_el);
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

export class CN_participant_release extends CN_base_action {
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
      // TODONEXT
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
                <th class="border border-light border-2 border-top-0 p-0" scope="col"></th>
                <th class="border border-light border-2 border-top-0 p-0" scope="col">Released</th>
                <th class="border border-light border-2 border-top-0 p-0" scope="col">Default Site</th>
                <th class="border border-light border-2 border-top-0 p-0" scope="col">Preferred Site</th>
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
