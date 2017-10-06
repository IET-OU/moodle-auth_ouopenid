/*!
  OU-OpenID. © Nick Freear, 03-Oct-2017. © The Open University.
*/

/* eslint camelcase: 0 */

// https://moodle.ouuk.tesla-project.eu/survey-end/?from=pre-survey&returncode=VR&embed=1

window.jQuery(function ($) {
  'use strict';

  var L = window.location;
  var m_from = L.search.match(/from=((pre|post)-?survey)/);
  var m_code = L.search.match(/return-?code=(return-code-)?(\w+)/);
  var source = m_from ? m_from[ 1 ] : null;
  var code = m_code ? m_code[ 2 ].toLowerCase() : null;

  var config = getConfig('#survey-end-config');
  var redirect = config.redirects[ code ] || null;

  $('body').addClass(inIframe() ? 'is-embed' : 'not-embed');

  console.warn(source, code, config);
  console.warn(inIframe() ? 'is-embed' : 'not-embed');

  if (inIframe() && redirect) {
    $('body').addClass('redirect');

    console.warn('Redirecting:', redirect.url);

    window.setTimeout(function () {
      window.location = redirect.url;
    }, config.timeout || 500);
  } else {
    $('body').addClass('no-redirect');

    console.error('No-redirect. Is "returncode" URL parameter present and correct?');
  }

  // https://stackoverflow.com/questions/326069/how-to-identify-if-a-webpage-is-being-loaded-inside-an-iframe-or-directly-into-t
  function inIframe () {
    try {
      return window.self !== window.top || window.location.test(/&embed=/);
    } catch (e) {
      return true;
    }
  }

  function getConfig(selector) {
    return JSON.parse($(selector).text());
  }
});
