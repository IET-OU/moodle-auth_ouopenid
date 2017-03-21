/*!
  OU-OpenID. © Nick Freear. © The Open University.
*/

(function (W) {

  var user_json_url = '/auth/ouopenid/user/ajax.php?r=' + rand()
    , form_warning = "Please don't edit your user profile!"
    , C = W.console
    , L = W.location;

  if (L.pathname.match(/^\/admin\//)) {
    return C.debug('ouopenid: admin page, exiting.');
  }


  when_call(function () {
    return W.require;
  },
  function () {
    var require = W.require;

    require([ 'jquery' ], function ($) {
      var $body = $('body');

      C.debug('ouopenid $:', $.fn.jquery);

      $.getJSON(user_json_url).done(function (data, textStat, jqXHR) {

        if (! data.profile.ouop_oucu) {
          C.error('ouopenid error: missing profile.');

          $body.addClass('ouop-ouopenid-error-profile');
        }

        C.debug('ouopenid JSON: ', data, jqXHR);

        $body.addClass(data.body_class)
          .addClass(data.profile.is_team ? 'ouop-is-team' : 'ouop-not-team');

        //if ( L.pathname.match(/^\/user\/edit/) )
        if (! data.profile.is_team) {
          disable_moodle_user_profile_form($);
        }

        ouop_course_welcome_alert($);

      }).fail(function (jqXHR, textStat, ex) {
        C.error('ouopenid error: ', textStat, jqXHR, ex);

        $body.addClass('ouop-ouopenid-error-' + jqXHR.status);
      });

    }); //End require.

  });


  /* ------------------------------------------- */

  function ouop_course_welcome_alert($) {
    var match = L.href.match(/[\?&]ouop_action=(return|newenrol)/)
      , ouop_action = match ? match[ 1 ] : null
      , course_title = $('#page-header h1:first').text()
      , msg;

    if ('return' === ouop_action) {
      msg = "Welcome back to the %s!";
    }
    else if ('newenrol' === ouop_action) {
      msg = "You've been enrolled in the %s course. Welcome!";
    }

    if (msg) {
      msg = msg.replace(/%s/, course_title);

      $('#page-header').after('<p class="oup-action-alert alert alert-success">%s</p>'.replace(/%s/, msg));
      //$('#page').prepend(..);
    }
  }

  function disable_moodle_user_profile_form($) {
    $('form[ action *= "/user/edit" ]')
      .attr('title', form_warning)
      .before('<p class="ouop-form-disable alert alert-warning">%s</p>'.replace(/%s/, form_warning))
      .find('input, select').each(function () {
      $(this).attr('disabled', 'disabled');
    });
  }

  function rand() {
    var min = 11, max = 999;
    return Math.floor(Math.random() * (max - min)) + min;
  }

  // https://gist.github.com/nfreear/f40470e1aec63f442a8a
  function when_call(when_true_FN, callback_FN, interval) {
    var int_id = W.setInterval(function () {
      if (when_true_FN()) {
        W.clearInterval(int_id);
        callback_FN();
      }
    }, interval || 300); // Milliseconds.
  }

}(window));
