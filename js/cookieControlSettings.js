(function($) {
    'use strict';

    Drupal.behaviors.cookieControlWidget = {
        attach: function (context, setting) {

            try {

                var config = JSON.parse(drupalSettings.civiccookiecontrol);

                config.onLoad = ccEval(config.onLoad);
                if (config.optionalCookies != null) {
                    config.optionalCookies.forEach(
                        function (optCookie) {
                            optCookie.onAccept = ccEval(optCookie.onAccept);
                            optCookie.onRevoke = ccEval(optCookie.onRevoke);
                            if (optCookie.thirdPartyCookies != null) {
                                optCookie.thirdPartyCookies = JSON.parse(optCookie.thirdPartyCookies);
                            }
                        }
                    );
                } else {
                    console.error("No Cookie Categories added in Cookie Control module. You need to add at least one Cookie Category for the Cookie Control module to properly operate.");
                }
                if (config.locales != null) {
                    config.locales.forEach(
                        function (locale) {
                            locale.text.optionalCookies = JSON.parse(locale.text.optionalCookies);
                        }
                    );
                }

                if (config.debug) {
                    console.log(config);
                }

                CookieControl.load(config);
            }catch (e) {
                console.log(e.message.toString());
            }

            function ccEval(cc) {
                try {
                    return new Function('return ' + cc)();
                } catch (e) {
                    console.log(e);
                }
            }

            function ccFunc(cc) {
                return new Function(cc)();
            }
        }
    }
})(jQuery);
