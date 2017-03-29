/*!
  OU-OpenID. © Nick Freear. © The Open University.
*/

(function (W) {

  var OUOP = window.OUOP = window.OUOP || {}
    , C = W.console;


  OUOP.consent_document_embed = function ($, resp) {
    var $container = $('#page-local-tesla-views-agreement #consent_doc')
      , consent_embed_url = resp.consent_embed_url + '&r=' + Math.random();

    if (resp.consent_embed_url) {
        $container.html(
          '<iframe class="ouop-consent-doc" src="%s"></iframe>'.replace(/%s/, consent_embed_url)
        );
    }

    C.debug('ouop: consent-document-embed');
  };


  OUOP.complete_moodle_user_profile_form = function ($, resp) {
    var $form = $('#page-user-edit #region-main form')
      , $inp_fname = $form.find('[ name = firstname ]')
      , $inp_lname = $form.find('[ name = lastname ]')
      , $inp_email = $form.find('[ name = email ]') ;

    if ($form.length && ! $inp_fname.val()) {
      $inp_fname.val(resp.profile.ouop_oucu);
      $inp_lname.val('Test');
      $inp_email.val('tesla.ouuk+%s@gmail.com'.replace(/%s/, resp.profile.ouop_oucu));
    }

    C.debug('ouop: complete-user-profile-form');
  };


  OUOP.local_fixes = function ($) {
    var $wordcount = $('.path-local-tesla-views .btn #word_counter')
      , $btn = $wordcount.closest('button')
      , $form = $wordcount.closest('form')
      , $question = $form.find('legend').first()
      ;

    $btn.html(
      '<span id="ouop-wc-outer" title="Word count">Words: <span id="word_counter" class="badge">0 / 250</span></span> Continue');

    //$wordcount.attr('title', 'Word count');

    $form.addClass('ouop-tesla-ks-enrollment-form');

    var qm = $question.text().match(/Q #?(\d) of (\d)\./)
      , qn_text;

    if (qm) {
      qn_text = '<small data-ouop-qn="%1">Question <i>%2</i><span> of %3</span></small>'
          .replace(/%1/, qm[ 1 ]).replace(/%2/, qm[ 1 ]).replace(/%3/, qm[ 2 ]);

      $question.html($question.text().replace(qm[ 0 ], qn_text));
    }

    C.debug('ouop: local-fixes');
  };


  OUOP.rand = function () {
    var min = 11, max = 9999;
    return Math.floor(Math.random() * (max - min)) + min;
  };


}(window));
