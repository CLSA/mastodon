cenozoApp.defineModule({
  name: "participant_data",
  models: ["add", "list", "view"],
  create: (module) => {
    angular.extend(module, {
      identifier: {
        parent: {
          subject: "study_phase",
          column: "study_phase.id",
        },
      },
      name: {
        singular: "participant data",
        plural: "participant data",
        possessive: "participant data's",
      },
      columnList: {
        full_study_name: {
          title: "Study & Phase Name",
          isIncluded: function( $state, model ) { return 'study_phase' != model.getSubjectFromState(); },
        },
        category: { title: "Category", },
        name: { title: "Name", },
      },
      defaultOrder: {
        column: "category",
        reverse: false,
      },
    });

    module.addInputGroup("", {
      full_study_name: {
        title: "Full Study Name",
        type: "string",
        isConstant: true,
      },
      category: {
        title: "Category",
        type: "string",
        format: "identifier",
      },
      name: {
        title: "Name",
        type: "string",
        format: "identifier",
      },
      filetype: {
        title: "File Type",
        type: "string",
        help: "The type of file (as a file extension) this data provides.",
      },
      path: {
        title: "Path",
        type: "string",
        help: "For supplemental data only: the path to the data file (where <UID> should be used in place of the participant's unique identifier."
      },
    });
  },
});
