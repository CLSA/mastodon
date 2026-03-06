const classes = await import(`${CENOZO_URL}/js/model/log_entry.mjs`);

export class CN_log_entry_model extends classes.CN_log_entry_model {
  /**
   * Extend parent method to add application column
   */
  clone_properties() {
    return {
      application: { meta: { table: "application", column: "title" }, title: "Application" },
      ...super.clone_properties()
    };
  }

  /**
   * Extend parent method to add application column
   */
  clone_columns() {
    return {
      application: { column: "application.title", title: "Application" },
      ...super.clone_columns()
    };
  }
}
