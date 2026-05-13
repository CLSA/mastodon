import { CN_model_base_form_entry } from "./base_form_entry.mjs"

export class CN_model_proxy_form_entry extends CN_model_base_form_entry {
  constructor() {
    super({
      type: "proxy",
      wording: {
        singular: "proxy form entry",
        plural: "proxy form entries",
        posessive: "proxy form entry's",
      },
      properties: {
        dm: {
          title: "Decision Maker",
          open: true,
          properties: {
            proxy: { title: "Use", type: "boolean" },
            already_identified: { title: "Already Identified", type: "boolean" },
            proxy_first_name: { title: "First Name" },
            proxy_last_name: { title: "Last Name" },
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
            },
            proxy_postcode: {
              title: "Postcode",
              regex: "^(([A-Z][0-9][A-Z] [0-9][A-Z][0-9])|([0-9]{5}))$",
              help: 'Must be in "A1A 1A1" format, zip codes in "01234" format.',
            },
            proxy_address_note: { title: "Address Note", type: "text" },
            proxy_phone: {
              title: "Phone",
              regex: "^[2-9](1[02-9]|[02-8]1|[02-8][02-9])-[2-9](1[02-9]|[02-9]1|[02-9]{2})-[0-9]{4}$",
              help: "Must be in NNN-NNN-NNNN format.",
            },
            proxy_phone_note: { title: "Phone Note", type: "text" },
            proxy_note: { title: "Note", type: "text" },
          },
        },

        ip: {
          title: "Information Provider",
          open: true,
          properties: {
            informant: { title: "Use", type: "boolean" },
            same_as_proxy: { title: "Same As Proxy", type: "boolean" },
            informant_first_name: { title: "First Name" },
            informant_last_name: { title: "Last Name" },
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
            },
            informant_postcode: {
              title: "Postcode",
              regex: "^(([A-Z][0-9][A-Z] [0-9][A-Z][0-9])|([0-9]{5}))$",
              help: 'Must be in "A1A 1A1" format, zip codes in "01234" format.',
            },
            informant_address_note: { title: "Address Note", type: "text" },
            informant_phone: {
              title: "Phone",
              regex: "^[2-9](1[02-9]|[02-8]1|[02-8][02-9])-[2-9](1[02-9]|[02-9]1|[02-9]{2})-[0-9]{4}$",
              help: "Must be in XXX-XXX-XXXX format.",
            },
            informant_phone_note: { title: "Phone Note", type: "text" },
            informant_note: { title: "Note", type: "text" },
          },
        },

        details: {
          title: "Additional Details",
          open: true,
          properties: {
            continue_questionnaires: { title: "Continue Questionnaires", type: "boolean" },
            continue_physical_tests: { title: "Continue Physical Tests", type: "boolean" },
            continue_draw_blood: { title: "Continue Blood and Urine", type: "boolean" },
            hin_future_access: { title: "Continue Health Card", type: "boolean" },
            signed: { title: "Signed", type: "boolean" },
            date: { title: "Date", type: "date" },
          },
        },
      },
    });
  }
}
