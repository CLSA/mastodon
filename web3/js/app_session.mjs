const { CN_base_app_session } = await import(`${CENOZO_URL}/js/base_app_session.mjs`);

class app_session extends CN_base_app_session {}

// Now create the app_session singleton and export it
const CN_app_session = new app_session();
export { CN_app_session };
