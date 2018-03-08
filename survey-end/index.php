<?php
/**
 * OU-OpenID SURVEY-END.
 *
 * @package auth_ouopenid
 * @author  Nick Freear, 03-October-2017, 08:07.
 * @copyright (c) 2017 The Open University.
 */

// For 'print_string()' language support!
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../classes/local/base.php';
require_once __DIR__ . '/../classes/local/conditional_embedded_survey.php';
require_once __DIR__ . '/../classes/local/tesla_consent.php';

use auth_ouopenid\local\conditional_embedded_survey;
use auth_ouopenid\local\tesla_consent;

define( 'OUOP_STRING', 'auth_ouopenid' );

// TODO: check if plugin is enabled or not !!

class Ou_Open_Id_Survey_End {

    const JQUERY_URL  = 'https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js';

    public static function versionParam() {
        echo '?r=' . mt_rand(11, 9999);
    }

    public static function checkMaintenanceMode() {
        global $CFG;
        header('X-auth-ouopenid-maintain: ' . $CFG->maintenance_enabled);

        if ($CFG->maintenance_enabled && ! filter_input(INPUT_GET, 'admin')) {
            header('Location: '. $CFG->wwwroot, true, 302);
        }
    }

    public static function get_return_code() {
      $code = preg_replace_callback(
          '/(?:return-code-)?(?:\d-)?(?P<code>\w+)/',
          function ($matches) { return $matches[ 'code' ]; },
          // filter_input( INPUT_GET, 'return-code' ) );
          required_param( 'return-code', PARAM_ALPHANUMEXT ) );

      header( 'X-getreturncode: ' . $code );

      return $code;
    }

    public static function complete_conditional() {
      $conditional = new conditional_embedded_survey( self::get_return_code() );
      return $conditional->make_complete();
    }
}
Ou_Open_Id_Survey_End::checkMaintenanceMode();

$conditional_completed = Ou_Open_Id_Survey_End::complete_conditional();
$agreement_id = tesla_consent::agree();

header('Content-Language: en');
header('X-Frame-Options: sameorigin');
header('X-UA-Compatible: IE=edge');

?>
<!doctype html><html lang="en"><meta charset="utf-8" />

<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="robots" content="noindex, nofollow" />

<title><?php print_string( 'survey_end_title', OUOP_STRING ) ?> (survey-end)</title>

<link rel="X-stylesheet" href="/auth/ouopenid/style/login.css" />
<style>
body {
    background-color: #fff;
    color: #222;
    font: 1.1em sans-serif;
    line-height: 1.4em;
    margin: 2em auto;
    max-width: 33em;
    X-min-width: 26em;
}
.footer { margin-top: 2em; }
.X-is-embed { margin: 2px; }
.X-is-embed .footer { display: none; }
.from-pre-survey .post-survey-msg,
.from-post-survey .pre-survey-msg { display: none; }
</style>

<body class="survey-end-page">

<div>

  <div class="survey-end-msg pre-survey-msg">
    <?php print_string( 'survey_end_msg_pre', OUOP_STRING ) ?>

    <!--
    <p> Thank you for completing the questionnaire.
    <p> Redirecting to activity &hellip;
    -->
  </div>

  <div class="survey-end-msg post-survey-msg">
    <?php print_string( 'survey_end_msg_post', OUOP_STRING ) ?>

    <!--
    <p> Thank you for completing the questionnaire.
    <p> The end.
    <p> You can close your browser window or tab.
    -->
  </div>


  <p class="footer"><small>
      <a href="<?php print_string( 'login_footer_link', OUOP_STRING ) ?>"
        ><?php print_string( 'login_footer', OUOP_STRING ) ?></a>.
  </small></p>

</div>


<script id="survey-end-config" type="application/json">
<?php
  echo json_encode([
    'course_code' => Ou_Open_Id_Survey_End::get_return_code(),
    'cond_completed' => $conditional_completed,
    'agreement_id' => $agreement_id,
    'redirects' => $CFG->auth_ouopenid_redirects,
    'hash' => '#section-3',
    'timeout' => 3000,
    'other' => 1,
  ], JSON_PRETTY_PRINT);
?>
</script>


<script src="<?php echo Ou_Open_Id_Survey_End::JQUERY_URL ?>"></script>
<script src="/auth/ouopenid/user/ouop-analytics.js<?php Ou_Open_Id_Survey_End::versionParam() ?>"></script>
<script src="/auth/ouopenid/js/survey-end.js<?php Ou_Open_Id_Survey_End::versionParam() ?>"></script>
<script>
  OUOP.analytics($, { config: { ga: <?php echo json_encode($CFG->auth_ouopenid_js_config[ 'ga' ]) ?> } });
</script>
</body>
</html>
