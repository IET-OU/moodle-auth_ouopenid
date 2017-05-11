/*!
  OU-OpenID. © Nick Freear. © The Open University.
*/

/* eslint camelcase: 0 */

window.jQuery(function ($) {
  'use strict';

  $('form#openidlogin').on('submit', function () {
    var oucu = $('#oucu').val();
    var openid_base_url = $('#openid_base_url').val();
    var $openidurl = $('input[ name = openid_url ]');
    var url = /%s/.test(openid_base_url) ? openid_base_url.replace(/%s/, oucu) : (openid_base_url + oucu);

    window.console.warn('ouop: submit, OUCU - ', oucu, url);

    $openidurl.val(url);
  });
});
