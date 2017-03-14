<?php
/**
 * Implement event hooks for the OpenID authentication plugin.
 *
 * NOTE: this file needs linked to via a symbolic link, eg.
 *
 *     cd ../moodle-auth-openid && ln -s ../moodle-auth-ouopenid/event.php
 *
 * @author Nick Freear, 07-March-2017.
 * @copyright (c) 2017 The Open University.
 *
 * @link https://github.com/remotelearner/moodle-auth_openid/blob/MOODLE_30_STABLE/example-event.php#L21-L34
 */

require_once __DIR__ . '/auth.php';


function on_openid_login(&$resp, &$user, $mainid = true) {
    auth_plugin_ouopenid::debug([ __FUNCTION__, $resp->identity_url, $resp->message->args->values, $user ]);

    auth_plugin_ouopenid::set_user($resp, $user);
}

function on_openid_create_account(&$resp, &$user) {
    auth_plugin_ouopenid::debug([ __FUNCTION__, $resp->identity_url, $resp->message->args->values, $user ]);

    auth_plugin_ouopenid::set_user($resp, $user);
}

//End.
