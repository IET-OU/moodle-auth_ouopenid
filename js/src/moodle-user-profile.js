// Streamline the Moodle user profile form, and pages.

var util;

module.exports = function ($, resp) {
  util = resp.util;

  complete_user_profile_form($, resp);
  user_profile_form_redirect($, resp);
  user_profile_continue_link($, resp);

  // Was: if ( L.pathname.match(/^\/user\/edit/) )
  if (! resp.profile.ouop_is_team) {
    disable_user_profile_form($);
  }
};

var W = window;
var C = W.console;

function complete_user_profile_form ($, resp) {
  var $form = $('#page-user-edit #region-main form');
  var $inp_fname = $form.find('[ name = firstname ]');
  var $inp_lname = $form.find('[ name = lastname ]');
  var $inp_email = $form.find('[ name = email ]');
  var prof = resp.profile;
  var isteam = prof.ouop_is_team;

  if ($form.length && !$inp_fname.val()) {
    $inp_fname.val(isteam ? prof.ouop_firstname : prof.ouop_oucu);
    $inp_lname.val(isteam ? prof.ouop_lastname : 'test');
    $inp_email.val(isteam ? prof.ouop_email : util.str('testmail', prof.ouop_oucu));
  }

  C.debug('ouop: complete-user-profile-form');
}

function user_profile_form_redirect ($, resp) {
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
    $form.before(util.alert(util.str('form_warning'), 'ouop-form-disable'));
  } else {
    $form.before(util.alert(util.str('form_redirect_msg'), 'ouop-form-disable'));
  }
}

function user_profile_continue_link ($, resp) {
  var $pages = $('#page-user-profile, #page-user-preferences');

  $pages.find('#page-header')
    .after(util.alert('<a href="' + resp.redirect_url + '">' + util.str('continuelink') + '</a>'));

  if (!resp.profile.ouop_is_team && $('#page-user-edit').length) {
    C.debug('ouopenid redirecting');

    // Was: W.location = data.redirect_urL;
  }
}

function disable_user_profile_form ($) {
  var $form = $('#page-user-edit #region-main form');

  $form
    .attr('title', util.str('form_warning'))
    // Was: .before(util.alert(util.str('form_warning'), 'ouop-form-disable'))
    .find('input, select').each(function () {
      // Was: $(this).attr('disabled', 'disabled');
      if (!$(this).hasClass('btn')) {
        $(this).attr('readonly', 'readonly');
      }
    });

  $form.find('#id_submitbutton').val('Continue').text('Continue');
}
