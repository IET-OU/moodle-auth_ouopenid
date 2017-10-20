// TeSLA-specific fixes ~~ usability and clarity.
// OUOP.local_fixes = function

module.exports = function ($, resp) {

  fix_enrollment_calibrate_page($);
  fix_typing_enrollment_page($);
  fix_enrollment_start_page($);

  fix_pilot_fallback_link($, resp);
  fix_voice_enrollment_controls($, resp);

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

function fix_voice_enrollment_controls($, resp) {
  var $voxcounter = $('.ouop-enroll-vr button#counter');
  var $startbutton = $('.ouop-enroll-vr button#start_recording');

  if (! $voxcounter.length) {
    return;
  }

  window.setTimeout(function () {
    // $voxcounter.text('02:00').addClass('ouop-vr-counter-fix').attr({ title: 'Countdown timer' });
    $voxcounter.attr({ title: 'Voice recording timer' });
    $startbutton.attr({ title: 'Start voice recording' });

    console.warn('ouop. fix-voice-enrollment-controls: ', $voxcounter.text());
  }, 2000);
}

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

// Pilot alternative or fallback.
function fix_pilot_fallback_link($, resp) {
  var $msg = $('.ouop-pilot-fallback-msg');
  var $link = $('.path-course-view p a[ href *= "#!-pilot-fallback-" ]');

  var url = $link.attr('href');
  var m_inst = url ? url.match(/-pilot-fallback-for-(\w+)/) : null;
  var inst_code = m_inst ? m_inst[ 1 ] : null;
  var fallback = resp.config.fallback_for;
  var fallback_url = fallback[ inst_code ] || null;

  $msg.addClass('alert alert-info');
  $msg.find('a[href]').addClass('btn btn-primary');

  if ($link.length) {
    $link
      .addClass('ouop-pilot-fallback-link')
      .attr({ href: fallback_url + url });

    // $link.closest('p').addClass('ouop-pilot-fallback-msg'); // ????

    console.warn('ouop-pilot-fallback-link: ', inst_code, fallback_url);
  }

  // console.warn('>> ouop-pilot-fallback-link: ', $link, m_inst);
}
