/*!
  OU-OpenID. Nick Freear / © The Open University.
*/

//window.console.debug('auth/ouopenid/script.js');

window.setTimeout(function () {

  var user_json = '/auth/ouopenid/user/ajax.php'
    , require = window.require
    , C = window.console
    , L = window.location;

  if (L.pathname.match(/^\/admin\//)) {
    return C.debug('ouopenid: admin page, exiting.');
  }


  require([ 'jquery' ], function ($) {

    C.debug('ouopenid $:', $.fn.jquery);

    $.getJSON(user_json).done(function (data) {

      C.debug('ouopenid JSON: ', data);

    }).fail(function (p1) {
      C.error('ouopenid error: ', p1);
    });

  });

}, 300);
