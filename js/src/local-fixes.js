// TeSLA-specific fixes ~~ usability and clarity.
// OUOP.local_fixes = function

module.exports = function ($) {

  fix_enrollment_calibrate_page($)
  fix_typing_enrollment_page($);
  fix_enrollment_start_page($);

  console.warn('ouop: tesla-local-fixes');
};

var tesla_inst_names = {
  'Keystroke Dynamics': 'ks',
  'Anti-plagiarism': 'tpt',
  'Forensic Analysis': 'fa',
  'Face Recognition': 'fr',
  'Voice Recognition': 'vr'
};
var tesla_inst_url_regex = /&target=(ks|tpt|fa|fr|vr)/;


function fix_typing_enrollment_page($) {
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
}

// 14-August-2017.
function fix_enrollment_start_page($) {
  var $alert = $('#page-local-tesla-views-enrollment #user-notifications .alert');
  var message = $alert.text();
  var m_enroll = message ? message.match(/The required enrollments are: ([\w ]+)/) : null;
  var inst = m_enroll ? m_enroll[ 1 ] : null;
  var inst_code = inst in tesla_inst_names ? tesla_inst_names[ inst ] : null;

  if (inst) {
    $('body')
      .addClass('ouop-enroll-page')
      .addClass('ouop-enroll-' + inst.replace(' ', '-'))
      .addClass('ouop-enroll-' + inst_code);

    $alert.removeClass('alert-danger').addClass('alert-warning');

    console.warn('ouop. Enroll instrument:', inst_code, inst);
  }
}

function fix_enrollment_calibrate_page($) {
  var m_instrument = window.location.search.match(tesla_inst_url_regex);
  var inst_code = m_instrument ? m_instrument[ 1 ] : null;

  if (inst_code) {
    $('body')
      .addClass('ouop-enroll-calibrate')
      .addClass('ouop-enroll-' + inst_code);

    console.warn('ouop. Calibrate instrument:', inst_code);
  }
}
