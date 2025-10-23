const { CN_base_view } = await import(`${CENOZO_URL}/js/base_view.mjs`);
const classes = await import(`${CENOZO_URL}/js/model/participant.mjs`);

const base_view_class = classes.CN_participant_view ? classes.CN_participant_view : CN_base_view;
export class CN_participant_view extends base_view_class {
  /**
   * Extend parent method
   */
  get_selector_child_list() {
    return super.get_selector_child_list().map(child => {
      console.log(child.title);
      if ("Applications" == child.title) child.title = "Release";
      if ("Studies" == child.title) child.title = "Eligible Studies";
      return child;
    }).sort((a,b) => a.title>b.title);
  }
}
