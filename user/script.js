/*!
  OU-OpenID. Nick Freear / © The Open University.
*/

(function (W) {

  var user_json = '/auth/ouopenid/user/ajax.php?r=' + rand()
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

      C.debug('ouopenid $:', $.fn.jquery);

      $.getJSON(user_json).done(function (data, textStat, jqXHR) {

        C.debug('ouopenid JSON: ', data, jqXHR);

        $('body').addClass(data.body_class);

      }).fail(function (jqXHR, textStat, ex) {
        C.error('ouopenid error: ', textStat, jqXHR, ex);

        $('body').addClass('ouop-ouopenid-error');
      });

    }); //End require.

  });


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
    }, interval || 200); // Milliseconds.
  }


}(window));
