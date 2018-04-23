// Embed or link to BOS pilot surveys.

module.exports = function ($, resp) {

  embed_pilot_surveys($, resp);
  fix_pilot_survey_links($, resp);
  inject_post_activity_survey_link($, resp);
  survey_return_redirect ($, resp);

  generic_embeds($, resp); // resp.util);

  console.warn('ouop: survey-embed');
};

var W = window;
var L = window.location;
var C = window.console;

function generic_embeds ($, resp) {
  if (L.pathname.match(/edit.php/)) {
    return C.warn('ouop: Editing, embeds disabled.');
  }

  var $links = $('a[ href *= _EMBED_ME_ ]');
  var util = resp.util;

  $links.each(function (idx, el) {
    var $link = $(el);
    var url = util.replace($link.attr('href'), { '{oucu}': resp.profile.ouop_oucu });
    var m_height = url.match(/height=(\d+\w+);?/);
    var height = 'height: ' + (m_height ? m_height[ 1 ] : '1050px;');

    $link.replaceWith(util.replace('<iframe src="{u}" style="{h}" class="ouop-generic-ifr" id="ifr-{i}"></iframe>', {
      '{u}': url, '{h}': height, '{i}': idx
    })); // .replace(/%s/, url).replace(/%h/, height).replace(/%d/, idx)
    var $iframe = $('#s' + idx);

    C.warn('ouop: generic-embeds', idx, $iframe);
  });
}

function embed_pilot_surveys ($, resp) {
    var $links = $('a[ href *= -pre-survey-embed ], a[ href *= -post-survey-embed ]');

    $links.each(function (idx, el) {
      var $link = $(el);
      var url = $link.attr('href');
      var survey_urls = resp.survey_urls;
      var survey_url = url.match(/-pre-survey-/) ? survey_urls.pre : survey_urls.post;
      var m_height = url.match(/height=(\d+\w+);?/);
      var height = m_height ? ('height: '+ m_height[ 1 ]) : '';

      survey_url = survey_url.replace(/\{?OUCU\}?/, resp.profile.ouop_oucu).replace(/\{COURSE\}/gi, resp.course_code);

      $link.replaceWith(resp.util.replace('<iframe src="{u}" style="{h}" id="ifr-{i}"></iframe>', {
        '{u}': survey_url, '{h}': height, '{i}': idx
      })); // .replace(/%s/, survey_url).replace(/%h/, height).replace(/%d/, idx)

      var $iframe = $('#ifr-' + idx).addClass('ouop-pilot-survey-ifr');
      $('body').addClass('ouop-has-pilot-survey-ifr');

      C.warn('ouop: pilot-survey-embeds', idx, survey_url, $iframe);
    });
}

// DEPRECATED.
function fix_pilot_survey_links ($, resp) {
    var $links = $('a[ href = "#!-pre-survey-link" ], a[ href = "#!-post-survey-link" ]');
    // var $links = $('#region-main a[ href *= OUCU ]');

    $links.each(function (idx, el) {
      var $link = $(el);
      var url = $link.attr('href');
      var survey_urls = resp.survey_urls; // TODO: bug #5.

      if (url.match(/#!-pre-survey-/)) {
        $link.attr('href', survey_urls.pre.replace(/\{?OUCU\}?/, resp.profile.ouop_oucu));
      } else {
        $link.attr('href', survey_urls.post.replace(/\{?OUCU\}?/, resp.profile.ouop_oucu));
      }
      $link.addClass('ouop-pilot-survey-link').addClass('a' + idx);

      C.warn('ouop: pilot-survey-links', idx, $link);
    });
}

function inject_post_activity_survey_link ($, resp) {
    if (! $('.path-mod').length) { return; }

    // var $container_quiz_rev = $('#page-mod-quiz-review #user-notifications');
    var $container_quiz = $('.path-mod-quiz'); // Was: $('#page-mod-quiz-view');
    var $container_assign = $('#page-mod-assign-view');
    var survey_url = resp.survey_urls.post.replace('{OUCU}', resp.profile.ouop_oucu).replace('{COURSE}', resp.course_code);
    // WAS: var survey_url = resp.survey_urls[ resp.course_code ].post.replace('{OUCU}', resp.profile.ouop_oucu);
    var util = resp.util;

    // $container_quiz_rev.append(util.alert(util.str('post_survey_msg', survey_url)));

    if ($container_assign.find('.submissionstatussubmitted').length) {
      $container_assign.find('#user-notifications').append(util.alert(util.str('post_survey_msg', survey_url)));
      $container_assign.addClass('ouop-submitted');

      C.warn('ouop: post-activity-survey-link - assign');
    } else if ($container_quiz.find('.quizattemptsummary').length
      || $container_quiz.find('.quizsummaryofattempt').length) {

      $container_quiz.find('.box.quizattempt').after(util.alert(util.str('post_survey_msg', survey_url)));
      $container_quiz.find('.quizsummaryofattempt').after(util.alert(util.str('post_survey_msg', survey_url)));

      $container_quiz.addClass('ouop-submitted');

      C.warn('ouop: post-activity-survey-link - quiz-view');
    }

    // TODO: Is "preview" text just visible to teachers?
    var continue_btn = $container_quiz.find('#region-main button').first().text();
    var has_confusing_text = continue_btn && /Continue the last preview/.test(continue_btn);
    if (has_confusing_text) {
      $container_quiz.find('#region-main button').first().addClass('ouop-fix').text('Continue the last attempt');
    }
}

function survey_return_redirect ($, resp) {
    var $flag = $('.ouop-survey-return-redirect');
    var params = L.search;
    var is_survey_return = /utm_(source|medium)=.*(survey|questionnaire)/.test(params);

    if (is_survey_return && $flag.length) {
      C.warn('ouop: survey-return-redirect');

      W.location = resp.redirect_url;
    }
}
