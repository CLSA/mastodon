const { CN_api } = await import(`${CENOZO_URL}/js/api.mjs`);
const classes = await import(`${CENOZO_URL}/js/model/export_column.mjs`);

export class CN_model_export_column extends classes.CN_model_export_column {
  /**
   * Extends parent method
   */
  clone_columns() {
    const columns = super.clone_columns();
    const filter_fn = columns.subtype.filter
    columns.subtype.filter = async (model, record) => {
      if ("site" == record.table_name) {
        // the site will include the site type and application ID separated by an underscore
        const [type, id] = record.subtype.split("_");
        const application = (await CN_api.get(`application/${id}`, { select: { column: "title" } })).title;
        return `${application} (${type})`;
      }

      return await filter_fn(model, record);
    };
    return columns;
  }

  /**
   * Extends parent method
   */
  clone_properties() {
    const properties = super.clone_properties();
    const get_enums_fn = properties.subtype.enum.get_enums;
    properties.subtype.enum.get_enums = async (model) => {
      const table_name = model.get_action().get_property_value("table_name");
      if ("site" == table_name) {
        // the site must include the site type and application ID separated by an underscore
        const types = ["default", "effective", "preferred"];

        const response = await CN_api.get("application", {
          select: { column: ["id", "title"] },
          modifier: { where: { column: "site_based", operator: "=", value: true }, order: "title" },
        });

        return response.reduce((list, record) => {
          list.push(...types.map(type => ({ key: `${type}_${record.id}`, value: `${record.title} (${type})` })));
          return list;
        }, []);
      }

      return await get_enums_fn(model);
    };
    return properties;
  }
}
