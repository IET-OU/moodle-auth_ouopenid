/*!
  OU-OpenID. © Nick Freear. © The Open University.
*/

/* eslint camelcase: 0 */

(function (W) {
  'use strict';

  var OUOP = W.OUOP;
  var user_json_url = '/auth/ouopenid/user/ajax.php?r=' + OUOP.rand();
  var C = W.console;
  var L = W.location;

  if (L.pathname.match(/^\/admin\//)) {
    return C.warn('ouopenid: admin page, exiting.');
  }

  when_call(function () {
    return W.require;
  },
  function () {
    var require = W.require;

    require([ 'jquery' ], function ($) {
      var $body = $('body');

      $body.addClass(L.href.match(/debug=1/) ? 'debug-param' : '');

      C.debug('ouopenid $:', $.fn.jquery);

      $.getJSON(user_json_url).done(function (data, textStat, jqXHR) {
        OUOP.set_strings(data);

        if (!data.profile.ouop_oucu) {
          C.warn('ouopenid warning: missing profile.');

          $body.addClass('ouop-ouopenid-warn-profile');
        }

        C.debug('ouopenid JSON: ', data, OUOP, W.M.cfg, jqXHR);

        OUOP.analytics($, data);

        OUOP.handle_moodle_events($);

        $body.addClass(data.body_class)
          .addClass(data.profile.ouop_is_team ? 'ouop-is-team' : 'ouop-not-team');

        $body.addClass(data.debug);

        OUOP.consent_document_embed($, data);

        OUOP.fix_pilot_survey_links($, data);
        OUOP.embed_pilot_surveys($, data);
        OUOP.inject_post_activity_survey_link($, data);
        OUOP.survey_return_redirect($, data);

        OUOP.fix_mod_assign_redirect($);

        OUOP.complete_moodle_user_profile_form($, data);
        OUOP.user_profile_form_redirect($, data);

        // Was: if ( L.pathname.match(/^\/user\/edit/) )
        if (!data.profile.ouop_is_team) {
          disable_moodle_user_profile_form($);
        }

        OUOP.user_profile_continue_link($, data);

        ouop_course_welcome_alert($);

        OUOP.tesla_results_statistics($);

        OUOP.toggle_hidden_ui_button($);
      })
      .fail(function (jqXHR, textStat, ex) {
        C.error('ouopenid error: ', textStat, jqXHR, ex);

        $body.addClass('ouop-ouopenid-error-' + jqXHR.status);
      });

      OUOP.local_fixes($);

      ouop_less_test($);
    }); // End require.
  });

  /* ------------------------------------------- */

  function ouop_course_welcome_alert ($) {
    var match = L.href.match(/[?&]ouop_action=(return|newenrol)/);
    var ouop_action = match ? match[ 1 ] : null;
    var course_title = $('#page-header h1:first').text();
    var msg;

    if (ouop_action) {
      msg = OUOP.str(ouop_action + '_msg', course_title);

      $('#page-header').after(OUOP.alert(msg));
    }
  }

  function disable_moodle_user_profile_form ($) {
    var $form = $('#page-user-edit #region-main form');

    $form
      .attr('title', OUOP.str('form_warning'))
      // Was: .before(OUOP.alert(OUOP.str('form_warning'), 'ouop-form-disable'))
      .find('input, select').each(function () {
        // Was: $(this).attr('disabled', 'disabled');
        if (!$(this).hasClass('btn')) {
          $(this).attr('readonly', 'readonly');
        }
      });

    $form.find('#id_submitbutton').val('Continue').text('Continue');
  }

  function ouop_less_test ($) {
    var $less_error = $('style[ id = "less:error-message" ]');
    var $less = $('style[ id ^= less ]');
    var $css = $('link[ href *= "ouop-styles.css" ]');

    if ($less_error.length) {
      C.error('ouopenid LESS error:', $less.attr('id'), $('.less-error-message').text());
    } else if ($css.length) {
      C.debug('ouopenid: ', $css.attr('href'));
    } else if ($less.length) {
      C.debug('ouopenid: ', $less.attr('id'));
    } else {
      C.error('ouopenid error: LESS CSS missing.');
    }
  }

  // https://gist.github.com/nfreear/f40470e1aec63f442a8a
  function when_call (when_true_FN, callback_FN, interval) {
    var int_id = W.setInterval(function () {
      if (when_true_FN()) {
        W.clearInterval(int_id);
        callback_FN();
      }
    }, interval || 300); // Milliseconds.
  }

  // .
}(window));
