const { CN_base_model } = await import(`${CENOZO_URL}/js/base_model.mjs`);
// import { CN_base_model } from "./base_model.mjs"

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
