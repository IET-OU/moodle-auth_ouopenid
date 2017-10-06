/*!
  OU-OpenID. © Nick Freear. © The Open University.

  NDF, 3-August-2017.
*/

// IMPORTANT: console.debug() does NOT print output !!

var BUILD_TIME = '-'; // <Auto>

var util = require('./src/ouop-utils');
var userJsonUrl = '/auth/ouopenid/user/ajax.php?r=' + util.rand();

require('./src/when-call')(
  function () {
    console.debug('>> when');
    return window.jQuery;
  },
  function ($) {
    var W = window;
    var L = W.location;

    console.debug('>> call');

    if (L.pathname.match(/^\/admin\//)) {
      return console.warn('ouopenid: admin page, exiting.');
    }

    var $body = $('body');

    $body.addClass(L.href.match(/debug=1/) ? 'debug-param' : '');

    console.warn('ouopenid $:', $.fn.jquery, BUILD_TIME, W.M.cfg); // W.Y.version

    $.getJSON(userJsonUrl).done(function (data, textStat, jqXHR) { // W.M.cfg.wwwroot + ..
      console.debug('>> getJSON..');

      util.set_strings(data);
      util.set_course_name($, data);
      util.site_message($, data);

      data.util = util;
      data.$ = $;

      if (! data.profile.ouop_oucu) {
        console.warn('ouopenid warning: missing profile.');

        $body.addClass('ouop-ouopenid-warn-profile');
      }

      console.warn('ouopenid JSON: ', data, jqXHR);

      require('./src/analytics').run($, data);
      // ga.analytics($, data);
      // ga.handle_moodle_events($);

      $body.addClass(data.body_class)
        .addClass(data.profile.ouop_is_team ? 'ouop-is-team' : 'ouop-not-team');

      $body.addClass(data.debug);

      require('./src/oupilot-ui')($, data);

      require('./src/moodle-user-profile')($, data);

      // ..

      require('./src/local-fixes')($, data);

      require('./src/survey-embed-link')($, data);

      require('./src/tesla-statistics')($);
    })
    .fail(function (jqXHR, textStat, ex) {
      console.error('ouopenid error: ', textStat, jqXHR, ex);

      $body.addClass('ouop-ouopenid-error-' + jqXHR.status);
    });

    util.less_test($);
  }
);

// End.
