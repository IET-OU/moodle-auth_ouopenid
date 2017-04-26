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
    var consent_embed_url = resp.config.consent_embed_url + '&r=' + OUOP.rand();

    if (resp.config.consent_embed_url) {
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
      $inp_email.val(isteam ? prof.ouop_email : OUOP.str('testmail', prof.ouop_oucu));
    }

    C.debug('ouop: complete-user-profile-form');
  };

  OUOP.user_profile_form_redirect = function ($, resp) {
    var $form = $('#page-user-edit #region-main form');
    var cfg = resp.config;

    if ($form.length && cfg.user_form_redirect) {
      W.setTimeout(function () {
        C.warn('ouop: user-profile-form-redirect - trigger');

        $form.trigger('submit');
      },
      cfg.redirect_timeout || 2000);
    }

    if (cfg.user_form_redirect) {
      $form.before(OUOP.alert(OUOP.str('form_warning'), 'ouop-form-disable'));
    } else {
      $form.before(OUOP.alert(OUOP.str('form_redirect_msg'), 'ouop-form-disable'));
    }
  };

  OUOP.user_profile_continue_link = function ($, resp) {
    var $pages = $('#page-user-profile, #page-user-preferences');

    $pages.find('#page-header')
      .after(OUOP.alert('<a href="' + resp.redirect_url + '">' + OUOP.str('continuelink') + '</a>'));

    if (!resp.profile.ouop_is_team && $('#page-user-edit').length) {
      C.debug('ouopenid redirecting');

      // Was: W.location = data.redirect_urL;
    }
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

  OUOP.fix_pilot_survey_links = function ($, resp) {
    var $links = $('#region-main a[ href *= OUCU ]');

    $links.each(function (idx, el) {
      var $link = $(el);
      var url = $link.attr('href');

      $link
        .attr('href', url.replace(/\{?OUCU\}?/, resp.profile.ouop_oucu))
        .addClass('ouop-pilot-survey-link').addClass('a' + idx);

      C.warn('ouop: pilot-survey-links', idx, $link);
    });
  };

  OUOP.inject_post_activity_survey_link = function ($, resp) {
    var $container_quiz = $('#page-mod-quiz-review #user-notifications');
    var $container_assign = $('#page-mod-assign-view');
    var survey_url = resp.config.post_survey_url.replace('{OUCU}', resp.profile.ouop_oucu);

    $container_quiz.append(OUOP.alert(OUOP.str('post_survey_msg', survey_url)));

    if ($container_assign.find('.submissionstatussubmitted').length) {
        $container_assign.find('#user-notifications').append(OUOP.alert(OUOP.str('post_survey_msg', survey_url)));
        $container_assign.addClass('ouop-submitted');

        C.warn('post-activity-survey-link: assign');
    }
  };

  OUOP.toggle_hidden_ui_button = function ($) {
    var $body = $('body');

    $body.append('<button id="ouop-toggle-hidden-ui-btn">Toggle hidden</button>');

    $('#ouop-toggle-hidden-ui-btn').on('click', function () {
      $body.toggleClass('ouop-hide-unused-ui').toggleClass('ouop-show-unused-ui');

      C.warn('ouop: toggle-hidden-ui-btn');
    });
  };

  OUOP.rand = function () {
    var min = 11;
    var max = 9999;
    return Math.floor(Math.random() * (max - min)) + min;
  };

  OUOP.alert = function (msg, id, cls) {
    return '<div id="' + (id || 'oua') + '" class="ouop alert ' +
       (cls || 'alert-info') + '" role="alert">' + msg + '</div>';
  };

  // Javascript translation/localisation [i18n].
  var trans = {};

  OUOP.set_strings = function (resp) {
    trans = resp.strings;
  };

  OUOP.str = function (sid, val) {
    return val ? trans[ sid ].replace('{$a}', val) : trans[ sid ];
  };

  // .
}(window));
