/*! (c) Blackbaud, Inc. */
(function (bbi) {
    "use strict";

    var appOptions = {
    	alias: "BlackbaudOLX",
    	author: "Blackbaud Interactive"
    };


    function confirmation(app, bbi, $) {

    	var defaults = {};
    	var settings = {};

    	var $modal;

    	var data = BBWP.plugins.olx_forms.socialSharing;

    	var methods = {
    		activateModal: function () {

    			// Activate the modal with options.
    			if (settings.activateOnLoad == "true") {
    				$modal.modal();
    			}

    		},
    		activateShareThis: function () {

    			window.stLight.options({
    				publisher: settings.shareThisPublisherId,
    				doNotHash: false,
    				doNotCopy: false,
    				hashAddressBar: false
    			});

    			// Facebook
    			window.stWidget.addEntry({
    				 "service": "facebook",
    				 "element": document.getElementById('olx-forms-modal-share-facebook'),
    				 "url": settings.shareUrl,
    				 "title": settings.shareTitle,
    				 "type": "custom",
    				 "text": "Facebook",
    				 "image": settings.shareImage,
    				 "summary": settings.shareSummary
    			});

    			// Twitter
    			window.stWidget.addEntry({
    				 "service": "twitter",
    				 "element": document.getElementById('olx-forms-modal-share-twitter'),
    				 "url": settings.shareUrl,
    				 "title": settings.shareTitle,
    				 "type": "custom",
    				 "text": "Twitter",
    				 "image": settings.shareImage,
    				 "summary": settings.shareSummary
    			});

    			// Google Plus
    			window.stWidget.addEntry({
    				 "service": "googleplus",
    				 "element": document.getElementById('olx-forms-modal-share-google-plus'),
    				 "url": settings.shareUrl,
    				 "title": settings.shareTitle,
    				 "type": "custom",
    				 "text": "Google Plus",
    				 "image": settings.shareImage,
    				 "summary": settings.shareSummary
    			});

    			// Email
    			window.stWidget.addEntry({
    				 "service": "email",
    				 "element": document.getElementById('olx-forms-modal-share-email'),
    				 "url": settings.shareUrl,
    				 "title": settings.shareTitle,
    				 "type": "custom",
    				 "text": "Email",
    				 "image": settings.shareImage,
    				 "summary": settings.shareSummary
    			});

    		},
    		buildModal: function () {
    			bbi.require(['handlebars-helpers'], function () {
        			// Add the modal to the page.
        			var template = Handlebars.compile(JSON.parse(data.handlebarsTemplate));
        			$('body').prepend(template(settings));
        			$modal = $('#olx-forms-modal');
                });

    		},
    		loadShareThis: function (callback) {

    			// Load the ShareThis library, if it doesn't exist.
    			if (typeof stlib === "undefined") {
    				bbi.helper.loadScript("//ws.sharethis.com/button/buttons.js", function () {
    					callback();
    				});
    			} else {
    				callback();
    			}

    		},
    		onConfirmation: function () {
    			// Launch modal with button.
    			$('#mongo-form').append('<p><a id="olx-forms-launch-modal" class="btn btn-primary" href="#"><i class="fa fa-' + settings.buttonIcon + '"></i>' + settings.buttonLabel + '</a></p>');
    			$('#olx-forms-launch-modal').on("click", function (e) {
    				e.preventDefault();
    				$modal.modal('show');
    			});

    		}
    	};

    	return {
    		init: function (options, element) {

    			settings = $.extend(true, {}, defaults, options);

    			if (settings.active == "true") {

    				methods.buildModal();
    				methods.loadShareThis(function () {
    					methods.activateShareThis();
    				});

    				bbi.olx.on("success", function (form) {
    					methods.activateModal();
    					methods.onConfirmation();
    				});

    			}
    		}
    	};

    }


    bbi
        .register(appOptions)
        .action("ConfirmationSocialSharing", confirmation)
        .build();
}(window.bbiGetInstance()));
