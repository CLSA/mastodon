cenozoApp.extendModule({
  name: "application",
  create: (module) => {
    if (angular.isDefined(cenozoApp.module("participant").actions.release)) {
      module.addExtraOperation("list", {
        title: "Manage Applications",
        isIncluded: function ($state, model) {
          return "participant" == model.getSubjectFromState();
        },
        operation: async function ($state, model) {
          await $state.go("participant.release", $state.params);
        },
      });
    }

    if (angular.isDefined(module.actions.release)) {
      module.addExtraOperation("view", {
        title: "Manage Participants",
        operation: async function ($state, model) {
          await $state.go("application.release", $state.params);
        },
        isIncluded: function ($state, model) {
          return model.viewModel.record.release_based;
        },
      });
    }

    /* ############################################################################################## */
    cenozo.providers.directive("cnApplicationRelease", [
      "CnApplicationReleaseFactory",
      function (CnApplicationReleaseFactory) {
        return {
          // look for the template in the application's path, not the framework
          templateUrl:
            cenozoApp.baseUrl +
            "/app/application/release.tpl.html?build=" +
            cenozoApp.build,
          restrict: "E",
          controller: function ($scope) {
            $scope.model = CnApplicationReleaseFactory.instance();
            // breadcrumbs are handled by the service
          },
        };
      },
    ]);

    /* ############################################################################################## */
    cenozo.providers.factory("CnApplicationReleaseFactory", [
      "CnApplicationModelFactory",
      "CnSession",
      "CnHttpFactory",
      "CnParticipantSelectionFactory",
      "CnModalMessageFactory",
      "$state",
      function (
        CnApplicationModelFactory,
        CnSession,
        CnHttpFactory,
        CnParticipantSelectionFactory,
        CnModalMessageFactory,
        $state
      ) {
        var object = function () {
          angular.extend(this, {
            parentModel: CnApplicationModelFactory.root,
            application: null,
            preferredSiteId: null,
            applicationSiteList: [],
            participantSelection: CnParticipantSelectionFactory.instance(),
            reset: function () {
              this.participantSelection.reset();
              this.cohortSiteList = null;
              this.preferredSiteId = null;
            },
            release: async function () {
              if (
                !this.participantSelection.confirmInProgress &&
                0 < this.participantSelection.confirmedCount
              ) {
                var response = await CnHttpFactory.instance({
                  path: "participant",
                  data: {
                    mode: "release",
                    application_id: this.application.id,
                    site_id: this.preferredSiteId,
                    identifier_id: this.participantSelection.identifierId,
                    identifier_list:
                      this.participantSelection.getIdentifierList(),
                  },
                }).post();

                await CnModalMessageFactory.instance({
                  title: "Participants Released",
                  message:
                    "You have successfully released " +
                    this.participantSelection.confirmedCount +
                    " participants to " +
                    this.application.title,
                }).show();

                self.reset();
              }
            },
          });

          this.reset();

          var self = this;
          async function init() {
            // get the application details and set up the breadcrumb trail
            var response = await CnHttpFactory.instance({
              path: "application/" + $state.params.identifier,
              data: { select: { column: ["title", "release_based"] } },
            }).get();

            self.application = response.data;
            self.application.identifier = $state.params.identifier;

            // Make modifications to the standard participant selection service
            // This is required because Mastodon extends the service by adding a site-list as well as identifier-list
            angular.extend(self.participantSelection, {
              data: {
                mode: "unreleased_only",
                application_id: self.application.id,
              },
              responseFn: function (model, response) {
                model.confirmedCount = response.data.identifier_list.length;
                model.identifierListString =
                  response.data.identifier_list.join(" ");
                model.confirmInProgress = false;
                self.cohortSiteList = response.data.site_list;
              },
            });

            // immediately send a 404 if this application is not release-based
            if (!self.application.release_based) {
              $state.go("error.404");
            } else {
              CnSession.setBreadcrumbTrail([
                {
                  title: "Applications",
                  go: async function () {
                    await $state.go("application.list");
                  },
                },
                {
                  title: response.data.title,
                  go: async function () {
                    await $state.go("application.view", {
                      identifier: $state.params.identifier,
                    });
                  },
                },
                {
                  title: "Release",
                },
              ]);
            }

            // get the application's site list
            var response = await CnHttpFactory.instance({
              path: "application/" + $state.params.identifier + "/site",
              data: { select: { column: ["name"] } },
            }).get();

            self.applicationSiteList = response.data;
            response.data.unshift({ id: null, name: "No Preferred Site" });
          }

          init();
        };

        return {
          instance: function () {
            return new object(false);
          },
        };
      },
    ]);
  },
});
