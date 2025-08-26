const CN_api = (await import(`${CENOZO_URL}/js/api.mjs`)).default;
const CN_element = (await import(`${CENOZO_URL}/js/element.mjs`)).default;

const { CN_base_model } = await import(`${CENOZO_URL}/js/base_model.mjs`);
const { CN_base_view } = await import(`${CENOZO_URL}/js/base_view.mjs`);

export class CN_base_form_model extends CN_base_model {
  constructor(form_type) {
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
        singular: `${form_type.replace(/_/, " ")} form`,
        plural: `${form_type.replace(/_/, " ")} forms`,
        posessive: `${form_type.replace(/_/, " ")} form's`,
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
      },
    });
  }
}

export class CN_base_form_view extends CN_base_view {
  #form_type;

  constructor(form_type, model) {
    super(model);
    this.#form_type = form_type;
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
    download_btn_el.addEventListener("click", async () => {
      const response = await CN_api.file(`${this.#form_type}_form/${model.get_identifier()}`, "application/pdf" );
      const blob = await response.blob();

      // create a temporary link element and click it so the file is downloaded by the browser
      const link = document.createElement("a");
      link.href = URL.createObjectURL(blob);
      link.download = response.headers.get('content-disposition').match(/filename=(.*);/)[1];
      link.click();
      URL.revokeObjectURL(link.href);
    });
    footer_el.append(download_btn_el);

    const adjudicate_btn_el = CN_element.create(
      '<button name="adjudicate" type="button" class="btn btn-light btn-outline-primary">Adjudicate</button>'
    );
    adjudicate_btn_el.addEventListener("click", async () => {
      console.log("TODO: implement");
    });
    footer_el.append(adjudicate_btn_el);

    const view_btn_el = CN_element.create(
      '<button name="view" type="button" class="btn btn-light btn-outline-primary">View Imported Form</button>'
    );
    view_btn_el.addEventListener("click", async () => {
      console.log("TODO: implement");
    });
    footer_el.append(view_btn_el);

    return footer_el;
  }
}
