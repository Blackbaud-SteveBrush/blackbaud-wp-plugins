/*! (c) Blackbaud, Inc. */
(function (bbi) {
    "use strict";

    var appOptions = {
        alias: "BlackbaudFlyer",
        author: "Blackbaud Interactive"
    };

    function launchModal(app, bbi, $) {

        var settings, defaults = {
            flyerId: 0,
            showOnce: false,
            cookieExpires: 30
        };

        var methods = {
            launch: function() {
                $(function() {
                    $("#" + settings.flyerId).modal();
                });
            }
        };

        return {
            init: function(options, element) {

                settings = $.extend(true, {}, defaults, options);

                if (settings.showOnce == "true") {
                    bbi.require(['cookie'], function($) {
                        if (typeof $.cookie(settings.flyerId) == "undefined") {
                            $.cookie(settings.flyerId, true, {
                                expires: settings.cookieExpires,
                                path: '/'
                            });
                            methods.launch();
                        }
                    });
                } else {
                    methods.launch();
                }
            }
        };
    }

    bbi
        .register(appOptions)
        .action("LaunchModal", launchModal)
        .build();
}(window.bbiGetInstance()));
