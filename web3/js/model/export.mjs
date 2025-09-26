const classes = await import(`${CENOZO_URL}/js/model/export.mjs`);

export class CN_export_model extends classes.CN_export_model {
  /**
   * Extends parent method
   */
  static get_export_columns() {
    const columns = super.get_export_columns();
    console.log(columns.subtype);
    return columns;
  }
}
