/*!
  OU-OpenID. © Nick Freear. © The Open University.
*/

/* eslint camelcase: 0 */

(function (W) {
  'use strict';

  var OUOP = W.OUOP = W.OUOP || {};
  var C = W.console;

  OUOP.consent_document_embed = function ($, resp) {
    var $container = $('#page-local-tesla-views-agreement #consent_doc');
    var consent_embed_url = resp.consent_embed_url + '&r=' + Math.random();

    if (resp.consent_embed_url) {
      $container.html(
        '<iframe class="ouop-consent-doc" src="%s"></iframe>'.replace(/%s/, consent_embed_url)
      );
    }

    C.debug('ouop: consent-document-embed');
  };

  OUOP.complete_moodle_user_profile_form = function ($, resp) {
    var $form = $('#page-user-edit #region-main form');
    var $inp_fname = $form.find('[ name = firstname ]');
    var $inp_lname = $form.find('[ name = lastname ]');
    var $inp_email = $form.find('[ name = email ]');
    var prof = resp.profile;
    var isteam = prof.ouop_is_team;

    if ($form.length && !$inp_fname.val()) {
      $inp_fname.val(isteam ? prof.ouop_firstname : prof.ouop_oucu);
      $inp_lname.val(isteam ? prof.ouop_lastname : 'test');
      $inp_email.val(isteam ? prof.ouop_email : 'tesla.ouuk+%s@gmail.com'.replace(/%s/, prof.ouop_oucu));
    }

    C.debug('ouop: complete-user-profile-form');
  };

  OUOP.local_fixes = function ($) {
    var $wordcount = $('.path-local-tesla-views .btn #word_counter');
    var $btn = $wordcount.closest('button');
    var $form = $wordcount.closest('form');
    var $question = $form.find('legend').first();

    $btn.html(
      '<span id="ouop-wc-outer" title="Word count">Words: <span id="word_counter" class="badge">0 / 250</span></span> Continue');

    // Was: $wordcount.attr('title', 'Word count');

    $form.addClass('ouop-tesla-ks-enrollment-form');

    var qm = $question.text().match(/Q #?(\d) of (\d)\./);
    var qn_text;

    if (qm) {
      qn_text = '<small data-ouop-qn="%1">Question <i>%2</i><span> of %3</span></small>'
          .replace(/%1/, qm[ 1 ]).replace(/%2/, qm[ 1 ]).replace(/%3/, qm[ 2 ]);

      $question.html($question.text().replace(qm[ 0 ], qn_text));
    }

    C.debug('ouop: local-fixes');
  };

  OUOP.fix_pilot_post_survey_link = function ($, resp) {
    var $link = $('#region-main a[ href *= OUCU ]').first();
    var url = $link.attr('href');

    if (url) {
      $link
        .attr('href', url.replace(/\{?OUCU\}?/, resp.profile.ouop_oucu))
        .addClass('ouop-pilot-post-survey-link');

      C.warn('ouop: ouop-pilot-post-survey-link', $link);
    }
  };

  function OUOP.toggle_hidden_ui_button = function ($) {
    var $body = $('body');

    $body.append('<button id="ouop-toggle-hidden-ui-btn">Toggle hidden</button>');

    $('#ouop-toggle-hidden-ui-btn').on('click', function () {
      $body.toggleClass('ouop-hide-unused-ui').toggleClass('ouop-show-unused-ui');
      C.warn('ouop: toggle-hidden-btn');
    });
  }

  OUOP.rand = function () {
    var min = 11;
    var max = 9999;
    return Math.floor(Math.random() * (max - min)) + min;
  };

  // Javascript translation/localisation [i18n].
  var strings = {};

  OUOP.set_strings = function (resp) {
    strings = resp.strings;
  };

  OUOP.str = function (sid) {
    return strings[ sid ];
  };

  // .
}(window));
