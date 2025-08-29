import { CN_base_form_entry_model } from "../base_form_entry_model.mjs"

export class CN_proxy_form_entry_model extends CN_base_form_entry_model {
  constructor() {
    super({
      type: "proxy",
      wording: {
        singular: "proxy form entry",
        plural: "proxy form entries",
        posessive: "proxy form entry's",
      },
      properties: {
        details: {
          title: "Details",
          open: true,
          properties: {
            proxy: { title: "Use", type: "boolean" },  
            already_identified: { title: "Already Identified", type: "boolean" },  
            proxy_first_name: { title: "First Name" },  
            proxy_last_name: { title: "Last Name" },  
            proxy_apartment_number: { title: "Apartment Number" },  
            proxy_street_number: { title: "Street Number" },  
            proxy_street_name: { title: "Street Name" },  
            proxy_box: {
              title: "Box",
              type: "string",
              format: "integer",
              help: "Include numbers only.",
            },  
            proxy_rural_route: {
              title: "Rural Route",
              type: "string",
              format: "integer",
              help: "Include numbers only.",
            },  
            proxy_address_other: { title: "Address Other" },  
            proxy_city: { title: "City" },
            proxy_region_id: { title: "Region", type: "enum" },
            proxy_postcode: {
              title: "Postcode",
              type: "string",
              regex: "^(([A-Z][0-9][A-Z] [0-9][A-Z][0-9])|([0-9]{5}))$",
              help: 'Must be in "A1A 1A1" format, zip codes in "01234" format.',
            },
            proxy_address_note: { title: "Address Note", type: "text" },
            proxy_phone: {
              title: "Phone",
              type: "string",
              regex:
                "^[2-9](1[02-9]|[02-8]1|[02-8][02-9])-[2-9](1[02-9]|[02-9]1|[02-9]{2})-[0-9]{4}$",
              help: "Must be in NNN-NNN-NNNN format.",
            },
            proxy_phone_note: { title: "Phone Note", type: "text" },
            proxy_note: { title: "Note", type: "text" },
          },
        },
      },
    });
  }
}
