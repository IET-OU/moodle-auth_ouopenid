<?php
/**
 * OU-OpenID login form.
 * Open University wrapper around 3rd-party OpenID authentication plugin.
 *
 * @package auth_ouopenid
 * @author  Nick Freear, 06-March-2017, 23:20.
 * @copyright (c) 2017 The Open University.
 */

// For 'print_string()' language support!
require_once __DIR__ . '/../../config.php';

define( 'OUOP_STRING', 'auth_ouopenid' );

// TODO: check if plugin is enabled or not !!

class Ou_Open_Id_Form {

    const ACTION = '/login/index.php';
    const OUCU_REGEX  = '[a-z]{1,6}\d{1,7}';  // Was: '[a-z]{2,4}\d{1,7}'.
    const OUCU_MIN    = 2;  // Was: 3.
    const OPEN_ID_URL = 'http://openid.open.ac.uk/oucu/%s';
    const JQUERY_URL  = 'https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js';

    public static function printOucu() {
        echo filter_input(INPUT_GET, 'oucu', FILTER_SANITIZE_STRING);
    }

    public static function printOpenIdUrl() {
        global $CFG;  // Moodle global.
        echo isset($CFG->auth_ouopenid_openid_url) ? $CFG->auth_ouopenid_openid_url : self::OPEN_ID_URL;
    }

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
}
Ou_Open_Id_Form::checkMaintenanceMode();


header('Content-Language: en');
header('X-Frame-Options: sameorigin');
header('X-UA-Compatible: IE=edge');

?>
<!doctype html><html lang="en"><meta charset="utf-8" />

<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="robots" content="noindex, nofollow" />

<title><?php print_string( 'login_title', OUOP_STRING ) ?></title>

<link rel="stylesheet" href="/auth/ouopenid/style/login.css" />

<div>

<?php require_once __DIR__ . '/login/intro.html' ?>

<form
  action="<?php echo Ou_Open_Id_Form::ACTION ?>"
  method="post"
  id="openidlogin"
  name="openidlogin"
  data-X-onsubmit="if (document.openidlogin.openid_url.value == '') return false;">
          <div>
            <p>
              <label ><?php
              print_string( 'login_label', OUOP_STRING, get_string( 'login_label_abbr', OUOP_STRING ));
              ?>
              <input
                id="oucu" value="<?php Ou_Open_Id_Form::printOucu() ?>"
                required="required" aria-required="true" pattern="<?php echo Ou_Open_Id_Form::OUCU_REGEX ?>" minlength="2" maxlength="9"
                title="<?php print_string( 'login_field_help', OUOP_STRING ) ?>" /></label>

              <input type="hidden" id="openid_base_url" value="<?php Ou_Open_Id_Form::printOpenIdUrl() ?>" />
              <input type="hidden" name="openid_url" />

              <button type="submit" ><?php print_string( 'login_submit', OUOP_STRING ) ?></button>
            </p>

              <!--<p><a href="http://openid.net/"><small>What's this?</small></a>-->

              <!--<p>We won't ask you for your Open University password on this site.</p>-->
          </div>
</form>

<p class="footer"><small>
    <a href="<?php print_string( 'login_footer_link', OUOP_STRING ) ?>"
      ><?php print_string( 'login_footer', OUOP_STRING ) ?></a>.
</small></p>

</div>


<script src="<?php echo Ou_Open_Id_Form::JQUERY_URL ?>"></script>
<script src="/auth/ouopenid/user/ouop-analytics.js<?php Ou_Open_Id_Form::versionParam() ?>"></script>
<script src="/auth/ouopenid/js/login.js<?php Ou_Open_Id_Form::versionParam() ?>"></script>
<script>
  OUOP.analytics($, { config: { ga: <?php echo json_encode($CFG->auth_ouopenid_js_config[ 'ga' ]) ?> } });
</script>
</html>
