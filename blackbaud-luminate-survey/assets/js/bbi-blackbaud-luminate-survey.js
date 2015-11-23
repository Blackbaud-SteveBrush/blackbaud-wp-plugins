/*! (c) Blackbaud, Inc. */
(function (bbi) {
    "use strict";

    var appOptions = {
        alias: "BlackbaudLuminateSurvey",
        author: "Blackbaud Interactive"
    };



    function buildSurvey(app, bbi, $) {

        var _settings, _defaults = {
            consId: 0
        };

        var _$element;

        function luminate(req) {
            var deferred = $.Deferred();
            if (!req.callback) {
                req.callback = function (res) {
                    deferred.resolve(res);
                };
            }
            luminateExtend.api(req);
            return deferred.promise();
        }

        return {
            init: function (options, element) {

                var data = BBWP.plugins.blackbaud_luminate_survey.api;
                var opts = {
                    apiKey: data.key,
                    path: {
                        nonsecure: data.nonsecure,
                        secure: data.secure
                    }
                };

                if (bbi.isPageEditor()) {
                    return;
                }

                _settings = $.extend(true, {}, _defaults, options);
                _$element = $(element);

                // Initialize luminateExtend.
                luminateExtend.init(opts);

                // Add Handlebars template.
                luminateExtend.global.update('handlebarsTemplate', JSON.parse(data.handlebarsTemplate));

                // Trigger ready event.
                $(document).trigger("luminate-ready");
                luminateExtend.global.update('ready', true);


                // Get Handlebars.js
                var handlebarsDeferred = $.Deferred();
                bbi.require(['handlebars-helpers'], function () {
                    handlebarsDeferred.resolve();
                });

                // Login test...
                luminate({
                    api: 'cons',
                    data: 'method=loginTest'
                }).then(function (res) {

                    // Get the survey...
                    luminate({
                        api: 'survey',
                        requiresAuth: true,
                        data: 'method=getSurvey&survey_id=' + _settings.surveyId
                    }).then(function (res) {

                        var data;
                        if (res.getSurveyResponse) {

                            data = res.getSurveyResponse.survey;

                            // When the API returns only one field, it removes the array.
                            // Add it back!
                            if (data.surveyQuestions.categoryId) {
                                data.surveyQuestions = [data.surveyQuestions];
                            }

                            var luminateExtendReady = function () {
                                var template = Handlebars.compile(luminateExtend.global.handlebarsTemplate);

                                data.settings = {
                                    consId: _settings.consId,
                                    path: luminateExtend.global.path
                                };

                                _$element.html(template(data));

                                // Confirmation screen.
                                window.submitSurveyCallback = {
                                    error: function (res) {
                                        console.log("Error:", res);
                                    },
                                    success: function (res) {

                                        var q, e, c, i;
                                        var questions, errors;

                                        if (res.submitSurveyResponse.success === "true") {
                                            data.confirmation = res.submitSurveyResponse.thankYouPageContent;
                                        } else {
                                            if (res.submitSurveyResponse.errors.errorCode) {
                                                res.submitSurveyResponse.errors = [res.submitSurveyResponse.errors];
                                            }
                                            questions = data.surveyQuestions;
                                            errors = res.submitSurveyResponse.errors;
                                            // Does the question have any errors?
                                            for (q in questions) {

                                                // Set values
                                                switch (questions[q].questionType) {
                                                    case "MultiSingleRadio":
                                                        questions[q].value = _$element.find('#question_' + questions[q].questionId).find('input:checked').val();
                                                        break;
                                                    case "ConsQuestion":
                                                        for (i in questions[q].questionTypeData.consRegInfoData.contactInfoField) {
                                                            questions[q].questionTypeData.consRegInfoData.contactInfoField[i].value = _$element.find('#cons-' + questions[q].questionTypeData.consRegInfoData.contactInfoField[i].fieldName).val();
                                                        }
                                                        break;
                                                    default:
                                                        questions[q].value = _$element.find('#question_' + questions[q].questionId).val();
                                                        break;
                                                }

                                                // Set errors
                                                for (e in errors) {
                                                    if (errors[e].errorField === null) {
                                                        if (questions[q].questionId === errors[e].questionInError) {
                                                            questions[q].error = errors[e].errorMessage;
                                                            break;
                                                        }
                                                    } else {

                                                        // It's a constituent field.
                                                        if (questions[q].questionType === "ConsQuestion") {
                                                            for (c in questions[q].questionTypeData.consRegInfoData.contactInfoField) {
                                                                if (questions[q].questionTypeData.consRegInfoData.contactInfoField[c].fieldName === errors[e].errorField) {
                                                                    questions[q].questionTypeData.consRegInfoData.contactInfoField[c].error = errors[e].errorMessage;
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                            data.hasErrors = true;
                                        }
                                        _$element.html(template(data));
                                        luminateExtend.api.bind();
                                    }
                                };

                                luminateExtend.api.bind();

                            };

                            handlebarsDeferred.promise().then(function () {
                                if (luminateExtend.global.ready) {
                                    luminateExtendReady();
                                } else {
                                    bbi.jQuery('window')(document).on('luminate-ready', luminateExtendReady);
                                }
                            });
                        }
                    });
                }).fail(function (res) {
                    console.log("[FAILED] Luminate extend: ", res);
                });
            }
        };
    }

    bbi
        .register(appOptions)
        .action("BuildSurvey", buildSurvey)
        .build();
}(window.bbiGetInstance()));
