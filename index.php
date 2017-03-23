<?php
/**
 * OU-OpenID login form.
 * Open University wrapper around 3rd-party OpenID authentication plugin.
 *
 * @author  Nick Freear, 06-March-2017, 23:20.
 * @copyright (c) 2017 The Open University.
 */

// TODO: check if plugin is enabled or not !!

class Ou_Open_Id_Form {

    const ACTION = '/login/index.php';
    const OUCU_REGEX  = '[a-z]{2,4}\d{1,7}';
    const OPEN_ID_URL = 'http://openid.open.ac.uk/oucu/';
    const JQUERY_URL  = 'https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js';

    public static function printOucu() {
        echo filter_input(INPUT_GET, 'oucu', FILTER_SANITIZE_STRING);
    }
}


?>
<!doctype html><html lang="en"><meta charset="utf-8" />

<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="robots" content="noindex, nofollow" />

<title>Sign in &mdash; TeSLA Pilot 2 (The Open University)</title>

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
              <label >Open University username
                (<abbr title="Open University computer username">OUCU</abbr>)
              <input
                id="oucu" name="oucu" value="<?php Ou_Open_Id_Form::printOucu() ?>"
                required="required" aria-required="1" pattern="[a-z]{2,4}\d{1,7}"
                title="Your OUCU &mdash; 2 to 4 letters, followed by 1 to 7 numbers." /></label>

              <input type="hidden" name="openid_url" />

              <button type="submit" >Sign in</button>
            </p>

              <!--<p><a href="http://openid.net/"><small>What's this?</small></a>-->

              <!--<p>We won't ask you for your Open University password on this site.</p>-->
          </div>
</form>

<p class="footer"><small>
    <a href="https://www.open.ac.uk">&copy; 2017 The Open University</a>.
</small></p>

</div>


<script src="<?php echo Ou_Open_Id_Form::JQUERY_URL ?>"></script>
<script>
window.jQuery(function ($) {

    $('form#openidlogin').on('submit', function () {
        var oucu = $('#oucu').val();

        window.console.debug('Submit, OUCU: ', oucu);

        $('[ name = openid_url ]').val('<?php echo Ou_Open_Id_Form::OPEN_ID_URL ?>' + oucu);
    });

});
</script>
</html>
