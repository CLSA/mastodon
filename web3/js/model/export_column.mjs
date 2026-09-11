const { CN_api } = await import(`${CENOZO_URL}/js/api.mjs`);
const classes = await import(`${CENOZO_URL}/js/model/export_column.mjs`);

export class CN_list_export_column extends classes.CN_list_export_column {
  constructor(parent_el, model) {
    super(parent_el, model);

    // add the application datetime as a possible export column
    this.add_table("application", {
      column_enum_list: [{ key: "datetime", value: "Release Datetime" }],
      subtype_promise: CN_api.get("application", {
        select: { column: { column: "title", alias: "value" } },
        modifier: { order: "title" },
      }),
    });
  }
}
