const { CN_base_view } = await import(`${CENOZO_URL}/js/base_view.mjs`);
const classes = await import(`${CENOZO_URL}/js/model/participant.mjs`);

const base_view_class = classes.CN_participant_view ? classes.CN_participant_view : CN_base_view;
export class CN_participant_view extends base_view_class {
  // TODO: change "Application" sub-list title to "Release"
  // TODO: change "Study" sub-list title to "Eligible Study"
}
