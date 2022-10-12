cenozoApp.extendModule({
  name: "participant",
  create: (module) => {
    delete module.columnList.site;
    var index = module.inputGroupList.findIndexByProperty(
      "title",
      "Site & Contact Details"
    );
    if (null != index) {
      var inputGroup = module.inputGroupList[index];
      inputGroup.title = "Contact Details";
      delete inputGroup.inputList.default_site;
      delete inputGroup.inputList.preferred_site_id;
    }

    module.addExtraOperation("view", {
      title: "Download Opal Forms",
      isIncluded: function ($state, model) {
        return angular.isDefined(model.viewModel.downloadOpalForms);
      },
      operation: function ($state, model) {
        return model.viewModel.downloadOpalForms();
      },
    });

    // extend the view factory
    cenozo.providers.decorator("CnParticipantViewFactory", [
      "$delegate",
      "CnSession",
      "CnHttpFactory",
      "CnModalMessageFactory",
      function ($delegate, CnSession, CnHttpFactory, CnModalMessageFactory) {
        var instance = $delegate.instance;
        $delegate.instance = function (parentModel, root) {
          var object = instance(parentModel, root);

          object.getChildTitle = function (child) {
            let title = this.$$getChildTitle(child);

            // change some of the default titles
            if ("application" == child.subject.snake) {
              title = title.replace("Application", "Release");
            } else if ("study" == child.subject.snake) {
              title = title.replace("Study", "Eligible Study");
            }

            return title;
          };

          if (
            "administrator" == CnSession.role.name ||
            "curator" == CnSession.role.name
          ) {
            object.downloadOpalForms = async function () {
              var modal = CnModalMessageFactory.instance({
                title: "Please Wait",
                message:
                  "Please wait while the participant's data is retrieved from Opal.",
                block: true,
              });
              modal.show();

              try {
                await CnHttpFactory.instance({
                  path:
                    "participant/" +
                    object.record.getIdentifier() +
                    "?opal_forms=1",
                  format: "zip",
                }).file();
              } finally {
                modal.close();
              }
            };
          }

          return object;
        };
        return $delegate;
      },
    ]);

    /* ############################################################################################## */
    cenozo.providers.directive("cnParticipantRelease", [
      "CnParticipantReleaseFactory",
      function (CnParticipantReleaseFactory) {
        return {
          // look for the template in the application's path, not the framework
          templateUrl:
            cenozoApp.baseUrl +
            "/app/participant/release.tpl.html?build=" +
            cenozoApp.build,
          restrict: "E",
          controller: function ($scope) {
            $scope.model = CnParticipantReleaseFactory.instance();

            $scope.model.onLoad(); // breadcrumbs are handled by the service
          },
        };
      },
    ]);

    /* ############################################################################################## */
    cenozo.providers.factory("CnParticipantReleaseFactory", [
      "CnParticipantModelFactory",
      "CnSession",
      "CnHttpFactory",
      "CnModalMessageFactory",
      "$state",
      function (CnParticipantModelFactory, CnSession, CnHttpFactory, CnModalMessageFactory, $state) {
        var object = function () {
          angular.extend(this, {
            parentModel: CnParticipantModelFactory.root,
            promise: null,
            participant: null,

            viewParticipant: async function () {
              await $state.go("participant.view", {
                identifier: $state.params.identifier,
              });
            },

            releaseParticipant: async function (application) {
              await this.promise;

              await CnHttpFactory.instance({
                path: "application/" + application.id + "/participant",
                data: this.participant.id,
              }).post();

              application.datetime = moment().format();
            },

            setPreferredSite: async function (application) {
              await this.promise;

              // get the new site
              var site = application.siteList.findByProperty(
                "id",
                application.preferred_site_id
              );

              await CnHttpFactory.instance({
                path: "participant/" + $state.params.identifier,
                data: {
                  application_id: application.id,
                  preferred_site_id: angular.isDefined(site.id)
                    ? site.id
                    : null,
                },
                onError: function (error) {
                  CnModalMessageFactory.instance({
                    title: "Unable To Set Preferred Site",
                    message:
                      "There was a problem while trying to set the participant's preferred site for " +
                      application.title +
                      " to " +
                      (angular.isDefined(site.id) ? site.name : "no site"),
                    error: true,
                  }).show();
                },
              }).patch();
            },

            reset: function () {
              this.isLoading = false;
              this.applicationList = [];
            },

            onLoad: async function () {
              await this.promise;
              this.reset();

              try {
                // get the application list with respect to this participant
                this.isLoading = true;
                var response = await CnHttpFactory.instance({
                  path:
                    "participant/" + $state.params.identifier + "/application",
                  data: {
                    select: {
                      column: [
                        "title",
                        "release_based",
                        "datetime",
                        "default_site_id",
                        "preferred_site_id",
                      ],
                    },
                    modifier: { order: "title" },
                  },
                }).get();

                this.applicationList = response.data;

                // get the site list for each application
                var promiseList = this.applicationList.reduce(function (
                  list,
                  application
                ) {
                  var getSiteListFn = async function () {
                    if (null == application.preferred_site_id)
                      application.preferred_site_id = undefined;
                    var response = await CnHttpFactory.instance({
                      path: "application/" + application.id + "/site",
                      data: { select: { column: ["name"] } },
                    }).get();

                    application.siteList = response.data;
                    application.siteList.unshift({
                      id: undefined,
                      name: "(none)",
                    });
                  };

                  list.push(getSiteListFn());
                  return list;
                },
                []);

                await Promise.all(promiseList);
              } finally {
                this.isLoading = false;
              }
            },
          });

          var self = this;
          async function init() {
            // set up the breadcrumb trail
            self.promise = CnHttpFactory.instance({
              path: "participant/" + $state.params.identifier,
              data: { select: { column: ["uid"] } },
            }).get();

            var response = await self.promise;

            self.participant = response.data;
            self.participant.identifier = $state.params.identifier;
            CnSession.setBreadcrumbTrail([
              {
                title: "Participants",
                go: async function () {
                  await $state.go("participant.list");
                },
              },
              {
                title: response.data.uid,
                go: async function () {
                  await $state.go("participant.view", {
                    identifier: $state.params.identifier,
                  });
                },
              },
              {
                title: "Release",
              },
            ]);
          }

          init();
          this.reset();
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
