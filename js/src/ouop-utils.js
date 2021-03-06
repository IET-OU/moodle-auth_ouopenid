// Utility functions.

// Javascript translation/localisation [i18n].
var trans = {};

var L = window.location;
var $;

module.exports = {

  set_jQuery: function () {
    $ = window.jQuery;
    return $;
  },

  is_debug_param: function () {
    return L.href.match(/debug=1/);
  },

  is_admin_page: function () {
    return L.pathname.match(/^\/admin\//);
  },

  is_tesla_enrol_page: function () {
    return $('#page-local-tesla-views-enrollment').length;
  },

  rand: function () {
    var min = 11;
    var max = 9999;
    return Math.floor(Math.random() * (max - min)) + min;
  },

  site_message: function ($, resp) {
    var message = resp.config.site_message;

    if (message) {
      $('#page').prepend(alert(message, 'oum', 'alert-info site-message'));
    }
  },

  alert: alert,

  // Javascript translation/localisation [i18n].
  set_strings: function (resp) {
    trans = resp.strings;
  },

  str: function (sid, val) {
    return val ? trans[ sid ].replace('{$a}', val) : trans[ sid ];
  },

  objToCsv: function (obj) {
    return JSON.stringify(obj, null, 2).replace(/:/g, ',').replace(/_/g, ' ');
  },

  set_course_name: function ($, resp) {
    var $course_name = $('.path-course-view, .path-mod').find('.breadcrumb-item a[ href *= "course/view.php" ]').first(); // .XX-path-mod-page,
    var course_code = $course_name.text();

    resp.course_code = course_code || null;

    if (course_code) {
      $('body').addClass('ouop-course-code-' + course_code).attr('data-course-code', course_code);
    }
  },

  console_mod_edit: function () {
    var $form = $('form[ action = "modedit.php" ]');

    if (!$form.length) { return; }

    var mod_info = {
      course_id: form_val('course'),
      cmid: form_val('coursemodule'),
      module: form_val('modulename'),
      instance: form_val('instance'),
      activity_id: form_val('instance')
    };

    console.warn('Modedit:', mod_info);

    function form_val (key) {
      return $form.find('[ name = %s ]'.replace(/%s/, key)).val();
    }
  },

  accessibility_fixes: function () {
    var a11y_fixes = {
      'fieldset.coursesearchbox': {
        title: 'Search courses',
        'aria-label': 'Search'
      }
    };

    $.each(a11y_fixes, function (sel, attrs) {
      $(sel).attr(attrs);
    });

    console.warn('A11y fixes:', a11y_fixes);
  },

  replace: replace_object,
  less_test: ouop_less_test
};

function ouop_less_test ($) {
  var $less_error = $('style[ id = "less:error-message" ]');
  var $less = $('style[ id ^= less ]');
  var $css = $('link[ href *= "ouop-styles.css" ]');

  if ($less_error.length) {
    console.error('ouopenid LESS error:', $less.attr('id'), $('.less-error-message').text());
  } else if ($css.length) {
    console.warn('ouopenid: ', $css.attr('href'));
  } else if ($less.length) {
    console.warn('ouopenid: ', $less.attr('id'));
  } else {
    console.error('ouopenid error: LESS CSS missing.');
  }

  // console.warn('less_test:', $less, $css);
}

// https://github.com/nfreear/gaad-widget/blob/3.x/src/methods.js#L79-L85
function replace_object (str, mapObj, reFlags) {
  var re = new RegExp(Object.keys(mapObj).join('|'), reFlags || 'g'); // Was: "gi".

  return str.replace(re, function (matched) {
    // console.warn('replace_obj:', re, matched, mapObj);
    return mapObj[ matched ]; // Was: matched.toLowerCase().
  });
}

function alert (msg, id, cls) {
  return '<div id="' + (id || 'oua') + '" class="ouop alert ' +
     (cls || 'alert-warning') + '" role="alert">' +
     (msg ? msg.replace(/href=/g, 'class="alert-link" href=') : 'no-msg') + '</div>';
}
