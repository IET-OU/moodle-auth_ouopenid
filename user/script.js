/*!
  OU-OpenID. Nick Freear / © The Open University.
*/

//window.console.debug('auth/ouopenid/script.js');

window.setTimeout(function () {

  var user_json = '/auth/ouopenid/user/ajax.php?r=' + rand()
    , require = window.require
    , C = window.console
    , L = window.location;

  if (L.pathname.match(/^\/admin\//)) {
    return C.debug('ouopenid: admin page, exiting.');
  }


  require([ 'jquery' ], function ($) {

    C.debug('ouopenid $:', $.fn.jquery);

    $.getJSON(user_json).done(function (data, textStat, jqXHR) {

      C.debug('ouopenid JSON: ', data);

      $('body').addClass(data.body_class);
      //document.body.className += ' ' + data.body_class;

    }).fail(function (jqXHR, textStat, ex) {
      C.error('ouopenid error: ', textStat, jqXHR, ex);

      $('body').addClass('ouop-ouopenid-error');
    });

  });


  function rand() {
    var min = 11, max = 999;
    return Math.floor(Math.random() * (max - min)) + min;
  }


}, 3000);
