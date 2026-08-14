const { CN_common } = await import(`${CENOZO_URL}/js/common.mjs`);
const { CN_modal_confirm } = await import(`${CENOZO_URL}/js/modal/confirm.mjs`);

const classes = await import(`${CENOZO_URL}/js/model/study.mjs`);
export class CN_model_study extends classes.CN_model_study {
  /**
   * Extend parent method
   */
  async clone_columns() {
    const columns = await super.clone_columns();
    CN_common.insert_property(columns, "after", "completed_event_type", "enable_status", {
      title: "Status Enabled",
      type: "boolean",
    });
    return columns;
  }

  /**
   * Extend parent method
   */
  async clone_properties() {
    const properties = await super.clone_properties();
    CN_common.insert_property(properties, "after", "completed_event_type_id", "enable_status", {
      title: "Enable Study Phase Status",
      type: "boolean",
      help: "Whether to track participant status for each phase of this study.",
    });
    return properties;
  }
}
