const { CN_action_view } = await import(`${CENOZO_URL}/js/action/view.mjs`);
const { CN_api } = await import(`${CENOZO_URL}/js/api.mjs`);
const { CN_base_action } = await import(`${CENOZO_URL}/js/action/base_action.mjs`);
const { CN_common } = await import(`${CENOZO_URL}/js/common.mjs`);
const { CN_element_card } = await import(`${CENOZO_URL}/js/element/card.mjs`);
const { CN_element_label } = await import(`${CENOZO_URL}/js/element/label.mjs`);
const { CN_input_enum } = await import(`${CENOZO_URL}/js/input/enum.mjs`);
const { CN_modal_message } = await import(`${CENOZO_URL}/js/modal/message.mjs`);
const { CN_participant_selection }  = await import(`${CENOZO_URL}/js/model/participant.mjs`);
const { CN_session } = await import(`${CENOZO_URL}/js/session.mjs`);

const classes = await import(`${CENOZO_URL}/js/model/application.mjs`);
const base_view_class = classes.CN_application_view ? classes.CN_application_view : CN_action_view;
export class CN_application_view extends base_view_class {
  /**
   * Add extra operations to the footer
   */
  create_footer_element() {
    const footer_el = super.create_footer_element();

    if (
      this.get_model().get_module().action_allowed("release") &&
      this.get_property_value("release_based")
    ) {
      const release_btn_el = this.constructor.html(`
        <button name="release" type="button" class="btn btn-light btn-outline-primary">
          Manage Participants
        </button>
      `);
      release_btn_el.addEventListener("click", async () => {
        await CN_session.navigate_to(`application/release/${this.get_model().get_identifier()}`);
      });
      footer_el.append(release_btn_el);
    }

    return footer_el;
  }
}

export class CN_application_release extends CN_base_action {
  #application = null;
  #participant_selection = new CN_participant_selection(null, {
    data: {
      mode: "unreleased_only",
      application_id: this.get_model().get_identifier(),
    },
  });

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
      return `${this.#application.title} Release`;
    }

    if ("header" == type) {
      return `Participant Management for ${this.#application.title}`;
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
    const model = this.get_model();

    // reset the list and confirm components
    await this.#participant_selection.reset();

    // load the application details and site list
    this.#application = await CN_api.get(`application/${model.get_identifier()}`);

    // populate the preferred site selection list
    const preferred_site_el = this.get_body_element().querySelector("#preferred_site_id");
    preferred_site_el.replaceChildren(this.constructor.html(
      '<option value="null" selected>No Preferred Site</option>'
    ));
    const site_list = await CN_api.get(
      `${model.get_view_url(null, "api")}/site`,
      { select: { column: ['id', 'name'] } }
    );
    site_list.forEach(site => {
      preferred_site_el.append(this.constructor.html(`<option value="${site.id}">${site.name}</option>`));
    });
  }

  /**
   * Extend parent method
   */
  create_body_element() {
    const body_el = this.constructor.html(`
      <div class="container-fluid text-info-emphasis">
        <div class="pb-2">
          This utility allows you to release a batch of participants to, or update their preferred site for
          Sabretooth F2.  In order to do either you must first select which participants to affect.
          This can be done typing the unique identifiers (eg: A123456) of all participants you wish to have
          included in the operation, then confirm that list to ensure each of the identifiers can be linked
          to a participant.
        </div>
        <div class="pb-2">
          Once you have confirmed the list of participant identifiers you will be presented with a summary
          of how many participants belong to which sites, broken down by cohort.
        </div>
        <div class="text-warning-emphasis pb-2">
          Note: only participants with the correct cohort and who have not already been released will be allowed.
          If you wish to update the preferred site for participants who have already been released you must use
          that application's <em>participant multi-edit</em> utility instead.
        </div>
        <div name="participant-list" class="py-1"></div>
        <div name="participant-confirm" class="py-1 d-none"></div>
      </div>
    `);

    const footer_el = this.constructor.html('<div class="row"></div>');
    CN_element_card.append(body_el.querySelector("[name=participant-confirm]"), {
      header: "Confirm Selection",
      body: "",
      footer: footer_el,
    });

    this.#participant_selection.add_event_listener("selectionchanged", () => {
      const confirm_el = body_el.querySelector("[name=participant-confirm]");
      const summary_el = confirm_el.querySelector("div.card-body");
      summary_el.innerHTML = "";
      if (this.#participant_selection.get_identifier_list().length) {
        confirm_el.className.remove("d-none");

        let first = true;
        const site_list = this.#participant_selection.get_site_list();
        for (let cohort_name in site_list) {
          if (!first) summary_el.append(this.constructor.html("<hr />"));
          summary_el.append(this.constructor.html(
            `<div class="text-center fs-5 fw-bold">${CN_common.uc_words(cohort_name)}</div>`
          ));

          for (let site_name in site_list[cohort_name]) {
            const row_el = this.constructor.html('<div class="d-flex justify-content-center">');
            const total = site_list[cohort_name][site_name];
            CN_element_label.append(row_el, { value: site_name });
            row_el.append(this.constructor.html(`<div class="col-form-label px-2">${total}</div>`));
            summary_el.append(row_el);
          }

          first = false;
        }
      } else {
        confirm_el.className.add("d-none");
      }
    });

    const participant_list_el = body_el.querySelector("[name=participant-list]");
    this.#participant_selection.set_parent_element(participant_list_el);
    participant_list_el.append(this.#participant_selection.get_element());

    CN_element_label.append(footer_el, {
      for: "preferred_site_id",
      value: "Preferred Site",
      class: "col-sm-3",
    });

    CN_input_enum.append(footer_el, {
      id: "preferred_site_id",
      class: "d-flex align-items-center col-sm-9",
      required: true,
      // add the release participants button as a postfix to the site selector
      postfix: (el) => {
        const btn_el = this.constructor.html(
          '<button name="confirm" type="button" class="btn btn-primary ms-2">Release Participants</button>'
        );
        btn_el.addEventListener("click", async () => {
          let response = null;
          await this.constructor.wait_for(async () => {
            const site_id = document.getElementById("preferred_site_id").value;
            response = await CN_api.post("participant", {
              mode: "release",
              application_id: this.get_model().get_identifier(),
              site_id: "null" == site_id ? null : Number(site_id),
              identifier_id: this.#participant_selection.get_idtype(),
              identifier_list: this.#participant_selection.get_identifier_list(),
            });
          });

          await CN_modal_message.create_and_open({
            title: "Participants Released",
            message: `A total of ${response} participant(s) have been successfully released.`,
          });

          await this.#participant_selection.reset();
        });
        el.append(btn_el);
      },
    });

    return body_el;
  }

  /**
   * Extend parent method
   */
  create_footer_element() {
    const footer_el = this.constructor.html(`
      <div class="btn-group" role="group">
        <button name="back" type="button" class="btn btn-primary">View Application</button>
      </div>
    `);
    footer_el.querySelector("button[name=back]").addEventListener("click", this.on_navigate_to_parent.bind(this));
    return footer_el;
  }
}
