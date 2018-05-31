
module.exports = function ($, resp) {
  ouop_course_welcome_alert ($, resp);
  consent_document_embed($, resp);
  fix_mod_assign_redirect($);

  add_course_code_to_urls ($, resp);
};

var W = window;
var L = W.location;
var C = W.console;

function add_course_code_to_urls ($, resp) {
  var $links = $('a').filter('[href *= "/mod/"], [href *= "/course/"]');

  $links.each(function (idx, el) {
    var url = $(el).attr('href');

    url = /#/.test(url) ? url.replace(/#/, '&_code=' + resp.course_code + '#') : (url + '&_code=' + resp.course_code);

    $(el).attr('href', url);
  });
}

function consent_document_embed ($, resp) {
  var $container = $('#page-local-tesla-views-agreement #consent_doc');
  var consent_embed_url = resp.config.consent_embed_url + '&r=' + resp.util.rand();

  if (resp.config.consent_embed_url) {
    $container.html(
      '<iframe class="ouop-consent-doc" src="%s"></iframe>'.replace(/%s/, consent_embed_url)
    );
  }

  C.debug('ouop: consent-document-embed');
}

function fix_mod_assign_redirect ($) {
  var $page = $('#page-mod-assign-redirect.ouop-fix-assign-redirect');
  var errormsg = $('.debuggingmessage').text();
  var $link = $page.find('.continuebutton a');
  var url = $link.attr('href');

  if ($page.length && errormsg && errormsg.match(/Error calling message processor email/)) {
    W.setTimeout(function () {
      C.warn('ouop: mod-assign-redirect-fix - trigger:', url);

      W.location = url;

      // Does not work ~ $link.trigger('click');
    }, 200);
  }
}

function ouop_course_welcome_alert ($, resp) {
  var match = L.href.match(/[?&]ouop_action=(return|newenrol)/);
  var ouop_action = match ? match[ 1 ] : null;
  var course_title = $('#page-header h1:first').text();
  var util = resp.util;
  var msg;

  if (ouop_action) {
    msg = util.str(ouop_action + '_msg', course_title);

    $('#page-header').after(util.alert(msg));
  }
}
