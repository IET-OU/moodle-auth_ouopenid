<?php
/**
 * Open University wrapper around 3rd-party OpenID authentication plugin.
 *
 * @author  Nick Freear, 06-March-2017, 23:20.
 * @copyright (c) 2017 The Open University.
 */

// TODO: check if plugin is enabled or not !!


?>
<!doctype html><html lang="en"><meta charset="utf-8" />

<title>Log in &mdash; The Open University</title>

<style>
body{
  font: 1.2em sans-serif; background: #fefefe; color: #333; margin: 2em auto; max-width: 36em;
}
form { }
input, button {
  font-size: 1.1em; padding: 3px 16px; text-align: center; border-radius: 4px;
}
button { background: #eee; cursor: pointer; }
#XX-oucu { padding: 2px 10px; }
#openid_url { color: #666; display: none; }
</style>



<form
  action="http://moodle.ouuk.tesla-project.eu/login/index.php"
  method="post"
  id="openidlogin"
  name="openidlogin"
  data-X-onsubmit="if (document.openidlogin.openid_url.value == '') return false;">
          <div class="desc">
          <!--You can login or signup here with your Google Email or OpenID url.-->
          </div>
          <div style="padding: 1em;">

            <p>
              <label >Your Open University user-name (OUCU)
              <input
                type="text" id="oucu" name="oucu"
                required="required" aria-required=1 pattern="[a-z]\w{2,7}" />
            </p>

            <input type="url" id="openid_url" name="openid_url" value="" />

              <p><button type="submit">Login</button></p>

              <p><a href="http://openid.net/"><small>What's this?</small></a>

              <p>Note, we won't ask you for your Open University password on this site.</p>
          </div>
</form>



<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script>
window.jQuery(function ($) {

    $('#openidlogin').on('submit', function () {
        var oucu = $('#oucu').val();

        window.console.debug('Submit, OUCU: ', oucu);

        $('#openid_url').val('http://openid.open.ac.uk/oucu/' + oucu);
    });

});
</script>
</html>
