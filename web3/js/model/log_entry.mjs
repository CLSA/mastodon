const classes = await import(`${CENOZO_URL}/js/model/log_entry.mjs`);

export class CN_model_log_entry extends classes.CN_model_log_entry {
  /**
   * Extend parent method to add application column
   */
  async clone_properties() {
    return {
      application: { meta: { table: "application", column: "title" }, title: "Application" },
      ...await super.clone_properties()
    };
  }

  /**
   * Extend parent method to add application column
   */
  async clone_columns() {
    return {
      application: { column: "application.title", title: "Application" },
      ...await super.clone_columns()
    };
  }
}
