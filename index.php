<?php
/**
 * Open University wrapper around 3rd-party OpenID authentication plugin.
 *
 * @author  Nick Freear, 06-March-2017, 23:20.
 * @copyright (c) 2017 The Open University.
 */

// TODO: check if plugin is enabled or not !!

class Ou_Open_Id_Form {

    const ACTION = '/login/index.php';
    const OUCU_REGEX  = '[a-z]\w{2,7}';
    const OPEN_ID_URL = 'http://openid.open.ac.uk/oucu/';
    const JQUERY_URL  = 'https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js';
}


?>
<!doctype html><html lang="en"><meta charset="utf-8" />

<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="robots" content="noindex, nofollow" />

<title>Log in &mdash; The Open University</title>

<style>
body{
  font: 1em sans-serif;
  background: #fefefe;
  color: #333;
  margin: 1em auto;
  max-width: 40em;
}
h1, h2 { color: #555; }
form { }
input, button {
  font-size: 1em;
  padding: .5rem 1rem;
  text-align: center;
  border-radius: .25rem;
  color: #373a3c;
  background-color: #fff;
  border: 1px solid #ccc;
}
button { background: #eee; cursor: pointer; }
input:focus { background: #ffffe0; }
input:focus, button:focus,
input:hover, button:hover { border-color: #999; }
#XX-oucu { padding: 2px 10px; }
#openid_url { color: #666; display: none; }
</style>


<h1> TeSLA Pilot 2 </h1>
<h2> Log in &mdash; The Open University </h2>

<form
  action="<?php echo Ou_Open_Id_Form::ACTION ?>"
  method="post"
  id="openidlogin"
  name="openidlogin"
  data-X-onsubmit="if (document.openidlogin.openid_url.value == '') return false;">
          <div class="desc">
          <!--You can login or signup here with your Google Email or OpenID url.-->
          </div>
          <div>

            <p>
              <label >Your Open University user-name (OUCU)
              <input
                id="oucu" name="oucu"
                required="required" aria-required="1" pattern="[a-z]\w{2,7}" /></label>

              <input type="url" id="openid_url" name="openid_url" value="" />

              <button type="submit">Login</button>
            </p>

              <p><a href="http://openid.net/"><small>What's this?</small></a>

              <p>Note, we won't ask you for your Open University password on this site.</p>
          </div>
</form>

<p class="footer"><small>
    <a href="https://www.open.ac.uk">&copy; 2017 The Open University</a>.
</small></p>


<script src="<?php echo Ou_Open_Id_Form::JQUERY_URL ?>"></script>
<script>
window.jQuery(function ($) {

    $('#openidlogin').on('submit', function () {
        var oucu = $('#oucu').val();

        window.console.debug('Submit, OUCU: ', oucu);

        $('#openid_url').val('<?php echo Ou_Open_Id_Form::OPEN_ID_URL ?>' + oucu);
    });

});
</script>
</html>
