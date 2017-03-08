<?php
/**
 * Implement event hooks for the OpenID authentication plugin.
 *
 * NOTE: this file needs to be sym-linked, eg.
 *
 *     cd ../moodle-auth_openid && ln -s ../moodle-auth_ouopenid/event.php
 *
 * @author Nick Freear, 07-March-2017.
 * @copyright (c) 2017 The Open University.
 *
 * @link https://github.com/remotelearner/moodle-auth_openid/blob/MOODLE_30_STABLE/example-event.php#L21-L34
 */

require_once __DIR__ . '/auth.php';


function on_openid_login(&$resp, &$user, $mainid = true) {
    auth_plugin_ouopenid::debug([ __FUNCTION__, $resp->identity_url, $resp->message->args->values, $user ]);

    $oucu = null;
    $identity_url = $resp->identity_url;
    if ($identity_url &&
        preg_match('@^http:\/\/openid\.open\.ac\.uk\/oucu\/(?P<oucu>\w+)$@', $identity_url, $matches)) {
        $oucu = $matches[ 'oucu' ];
    }

    if ($oucu && $user->auth == 'openid' && ( ! $user->firstname || $user->firstname === 'test' )) {
        $user->firstname = $oucu;
    }

    if ($oucu && $user->auth == 'openid' && ! $user->email) {
        $user->email = $oucu . '@openmail.open.ac.uk';
    }

    auth_plugin_ouopenid::debug([
      __FUNCTION__, $identity_url, $oucu, $user->email, $user->username, $user->auth, 'userid=', $user->id ]);
}

function on_openid_create_account(&$resp, &$user) {
    auth_plugin_ouopenid::debug([ __FUNCTION__, $resp, $user ]);
}

//End.
