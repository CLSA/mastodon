import { CN_model_base_form_entry } from "./base_form_entry.mjs"

const { CN_model_country } = await import(`${CENOZO_URL}/js/model/country.mjs`);

export class CN_model_general_proxy_form_entry extends CN_model_base_form_entry {
  constructor() {
    super({
      type: "general_proxy",
      wording: {
        singular: "general proxy form entry",
        plural: "general proxy form entries",
        posessive: "general proxy form entry's",
      },
      properties: {
        details: {
          title: "Details",
          open: true,
          properties: {
            continue_questionnaires: { title: "Continue Questionnaires", type: "boolean" },
            hin_future_access: { title: "Continue Health Card", type: "boolean" },
            continue_dcs_visits: { title: "Continue DCS Visits", type: "boolean" },
            signed: { title: "Signed", type: "boolean" },
            date: { title: "Date", type: "date" },
          },
        },

        dm: {
          title: "Decision Maker",
          open: true,
          properties: {
            proxy_first_name: { title: "First Name" },
            proxy_last_name: { title: "Last Name" },
            proxy_address_international: { title: "International Address", type: "boolean" },
            proxy_apartment_number: { title: "Apartment Number" },
            proxy_street_number: { title: "Street Number" },
            proxy_street_name: { title: "Street Name" },
            proxy_box: { title: "Box", type: "integer", help: "Include numbers only." },
            proxy_rural_route: { title: "Rural Route", type: "integer", help: "Include numbers only." },
            proxy_address_other: { title: "Address Other" },
            proxy_city: { title: "City" },
            proxy_region_id: {
              title: "Region",
              type: "enum",
              enum: {
                path: "region",
                select: { column: {
                  column: "CONCAT(region.name, ', ', country.name)",
                  alias: "name",
                  table_prefix: false,
                } },
                modifier: { order: ["country.name", "region.name"] },
              },
              is_hidden: (model) => (
                model.get_action().get_property_value("proxy_address_international")
              ),
            },
            proxy_international_region: {
              title: "International Region",
              is_hidden: (model) => (
                !model.get_action().get_property_value("proxy_address_international")
              ),
            },
            proxy_international_country_id: {
              title: "International Country",
              type: "typeahead",
              typeahead: CN_model_country.get_typeahead(),
              is_hidden: (model) => (
                !model.get_action().get_property_value("proxy_address_international")
              ),
            },
            proxy_postcode: {
              title: "Postcode",
              regex: "^(([A-Z][0-9][A-Z] [0-9][A-Z][0-9])|([0-9]{5}))$",
              help: 'Must be in "A1A 1A1" format, zip codes in "01234" format (if not international).',
            },
            proxy_address_note: { title: "Address Note", type: "text" },
            proxy_phone_international: { title: "International Phone", type: "boolean" },
            proxy_phone: {
              title: "Phone",
              regex: "^[2-9](1[02-9]|[02-8]1|[02-8][02-9])-[2-9](1[02-9]|[02-9]1|[02-9]{2})-[0-9]{4}$",
              help: "Must be a valid North American phone number in XXX-XXX-XXXX format (if not international).",
            },
            proxy_phone_note: { title: "Phone Note", type: "text" },
            proxy_note: { title: "Note", type: "text" },
            already_identified: { title: "Already Identified", type: "boolean" },
            same_as_proxy: { title: "Same As Proxy", type: "boolean" },
          },
        },

        ip: {
          title: "Information Provider",
          open: true,
          properties: {
            informant_first_name: {
              title: "First Name",
              help: "If informant is same as proxy then enter the first and last name only",
            },
            informant_last_name: {
              title: "Last Name",
              help: "If informant is same as proxy then enter the first and last name only",
            },
            informant_address_international: { title: "International", type: "boolean" },
            informant_apartment_number: { title: "Apartment Number" },
            informant_street_number: { title: "Street Number" },
            informant_street_name: { title: "Street Name" },
            informant_box: { title: "Box", type: "integer" },
            informant_rural_route: { title: "Rural Route", type: "integer" },
            informant_address_other: { title: "Address Other" },
            informant_city: { title: "City" },
            informant_region_id: {
              title: "Region",
              type: "enum",
              enum: {
                path: "region",
                select: { column: {
                  column: "CONCAT(region.name, ', ', country.name)",
                  alias: "name",
                  table_prefix: false,
                } },
                modifier: { order: ["country.name", "region.name"] },
              },
              is_hidden: (model) => (
                model.get_action().get_property_value("informant_address_international")
              ),
            },
            informant_international_region: {
              title: "International Region",
              is_hidden: (model) => (
                !model.get_action().get_property_value("informant_address_international")
              ),
            },
            informant_international_country_id: {
              title: "International Country",
              type: "typeahead",
              typeahead: CN_model_country.get_typeahead(),
              is_hidden: (model) => (
                !model.get_action().get_property_value("informant_address_international")
              ),
            },
            informant_postcode: {
              title: "Postcode",
              regex: "^(([A-Z][0-9][A-Z] [0-9][A-Z][0-9])|([0-9]{5}))$",
              help: 'Must be in "A1A 1A1" format, zip codes in "01234" format (if not international).',
            },
            informant_address_note: { title: "Address Note", type: "text" },
            informant_phone_international: { title: "International", type: "boolean" },
            informant_phone: {
              title: "Phone",
              regex: "^[2-9](1[02-9]|[02-8]1|[02-8][02-9])-[2-9](1[02-9]|[02-9]1|[02-9]{2})-[0-9]{4}$",
              help: "Must be a valid North American phone number in XXX-XXX-XXXX format (if not international).",
            },
            informant_phone_note: { title: "Phone Note", type: "text" },
            informant_note: { title: "Note", type: "text" },
          },
        },
      },
    });
  }
}
