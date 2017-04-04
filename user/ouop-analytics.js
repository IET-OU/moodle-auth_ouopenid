/*!
  OU-OpenID. © Nick Freear. © The Open University. (NDF / 02-April-2017)
*/

/* global ga: false */

/* eslint-disable *//* jshint ignore:start */
(function (i, s, o, g, r, a, m) { i['GoogleAnalyticsObject'] = r; i[r] = i[r] || function () {
  (i[r].q = i[r].q || []).push(arguments) }, i[r]. l = 1 * new Date(); a = s.createElement(o),
  m = s.getElementsByTagName(o)[0]; a.async = 1; a.src = g; m.parentNode.insertBefore(a, m)
})(window, window.document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');
/* eslint-enable *//* jshint ignore:end */

(function (W) {
  'use strict';

  var OUOP = W.OUOP = W.OUOP || {};

  OUOP.analytics = function ($, resp) {
    /* Google Analytics.
    */
    ga('create', resp.config.ga, 'auto');
    ga('send', 'pageview');

    W.console.debug('ouop: analytics', resp.config.ga);
  };

  OUOP.handle_moodle_events = function ($, resp) {
    var $events = $('script[ data-ouop-event ]');

    $events.each(function (idx, el) { // if ($events.length > 0) {
      var event = JSON.parse($(el).text());

      C.warn('ouop: event', idx, event);
    });
  };

  // .
}(window));
