// Check browser (user-agent) compatibility.
//
// NDF, 01-December-2017.
// http://useragentstring.com/pages/useragentstring.php?name=Internet+Explorer

module.exports = function ($, resp) {
  'use strict';

  var agent = window.navigator.userAgent;
  var no_compat_regex = resp.config.no_compat_regex || / (MSIE |Trident\/|Edge\/)\d+/;
  var $notify = $('#user-notifications');
  var str = resp.strings;
  var url = str.no_ua_compat_url || 'https://browsehappy.com/';
  var no_compat_msg = str.no_ua_compat_msg || 'Sorry! This browser isn\'t supported by this TeSLA study. Would you like to try <a href="%s">a different browser</a>?';
  var m_agent = agent.match(no_compat_regex);

  if (m_agent) {
    no_compat_msg = no_compat_msg.replace(/%s/, url);

    $notify.prepend('<div class="alert alert-danger ouop-no-ua-compat-msg">%s</div>'.replace(/%s/, no_compat_msg));
  }

  $('body').attr('data-ua', m_agent ? m_agent[ 1 ] : 'other').addClass('ouop-ua-compat' + (m_agent ? 'no' : 'yes'));

  console.warn('ouop-ua-compat:', m_agent, no_compat_msg);
};
