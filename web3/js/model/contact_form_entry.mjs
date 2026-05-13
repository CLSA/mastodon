import { CN_model_base_form_entry } from "./base_form_entry.mjs"

export class CN_model_contact_form_entry extends CN_model_base_form_entry {
  constructor() {
    super({
      type: "contact",
      wording: {
        singular: "contact form entry",
        plural: "contact form entries",
        posessive: "contact form entry's",
      },
      properties: {
        details: {
          title: "Details",
          open: true,
          properties: {
            first_name: { title: "First Name" },
            last_name: { title: "Last Name" },
            apartment_number: { title: "Apartment #" },
            street_number: { title: "Street #" },
            street_name: { title: "Street Name" },
            box: { title: "Post Office Box" },
            rural_route: { title: "Rural Route" },
            address_other: { title: "Other" },
            city: { title: "City" },
            region_id: {
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
            postcode: { title: "Postcode" },
            address_note: { title: "Address Note", type: "text" },
            home_phone: { title: "Home Phone" },
            home_phone_note: { title: "Home Phone Note", type: "text" },
            mobile_phone: { title: "Mobile Phone" },
            mobile_phone_note: { title: "Mobile Phone Note", type: "text" },
            phone_preference: { title: "Phone Preference" },
            email: { title: "Email" },
            gender: { title: "Sex" },
            age_bracket: { title: "Age Bracket" },
            monday: { title: "Monday", type: "boolean" },
            tuesday: { title: "Tuesday", type: "boolean" },
            wednesday: { title: "Wednesday", type: "boolean" },
            thursday: { title: "Thursday", type: "boolean" },
            friday: { title: "Friday", type: "boolean" },
            saturday: { title: "Saturday", type: "boolean" },
            time_9_10: { title: "9am to 10am", type: "boolean" },
            time_10_11: { title: "10am to 11am", type: "boolean" },
            time_11_12: { title: "11am to 12pm", type: "boolean" },
            time_12_13: { title: "12pm to 1pm", type: "boolean" },
            time_13_14: { title: "1pm to 2pm", type: "boolean" },
            time_14_15: { title: "2pm to 3pm", type: "boolean" },
            time_15_16: { title: "3pm to 4pm", type: "boolean" },
            time_16_17: { title: "4pm to 5pm", type: "boolean" },
            time_17_18: { title: "5pm to 6pm", type: "boolean" },
            time_18_19: { title: "6pm to 7pm", type: "boolean" },
            time_19_20: { title: "7pm to 8pm", type: "boolean" },
            time_20_21: { title: "8pm to 9pm", type: "boolean" },
            high_school: { title: "High School", type: "boolean" },
            post_secondary: { title: "Post Secondary", type: "boolean" },
            language_id: {
              title: "Language",
              type: "enum",
              enum: {
                path: "language",
                modifier: {
                  where: { column: "active", operator: "=", value: true },
                  order: "language.name",
                },
              },
            },
            cohort_id: {
              title: "Cohort",
              type: "enum",
              enum: { path: "cohort" },
            },
            code: { title: "Code" },
            signed: { title: "Signed", type: "boolean" },
            participant_date: { title: "Participant Date", type: "date" },
            stamped_date: { title: "Stamped Date", type: "date" },
            note: { title: "Note", type: "text" },
          },
        },
      },
    });
  }
}
