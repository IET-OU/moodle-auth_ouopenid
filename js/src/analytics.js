/*!
  OU-OpenID. © Nick Freear. © The Open University. (NDF / 02-April-2017)
*/

module.exports = {
  run: function ($, resp) {
    include_ga_javascript();

    pageview(resp);

    handle_moodle_events($);
  },

  analytics: function ($, resp) {
    include_ga_javascript();

    pageview(resp);
  },

  handle_moodle_events: handle_moodle_events
};

var W = window;
var C = W.console;

function include_ga_javascript () {
  /* eslint-disable *//* jshint ignore:start */
  (function (i, s, o, g, r, a, m) { i['GoogleAnalyticsObject'] = r; i[r] = i[r] || function () {
    (i[r].q = i[r].q || []).push(arguments) }, i[r]. l = 1 * new Date(); a = s.createElement(o),
    m = s.getElementsByTagName(o)[0]; a.async = 1; a.src = g; m.parentNode.insertBefore(a, m)
  })(window, window.document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');
  /* eslint-enable *//* jshint ignore:end */
}

function pageview (resp) {
  /* Google Analytics.
  */
  ga('create', resp.config.ga, 'auto');
  ga('send', 'pageview');

  C.debug('ouop: analytics', resp.config.ga);
}

function handle_moodle_events ($) {
  var $events = $('script[ data-ouop-event ]');

  $events.each(function (idx, el) { // if ($events.length > 0) {
    var event = JSON.parse($(el).text());

    C.warn('ouop: event', idx, event);
  });
}
