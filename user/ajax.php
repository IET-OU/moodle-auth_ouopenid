<?php
/**
 * JSON output. OU-OpenID authentication plugin.
 *
 * @package auth_ouopenid
 * @author  Nick Freear, 08-March-2017.
 * @copyright (c) 2017 The Open University.
 */

require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../vendor/autoload.php';

define('DEBUG', filter_input(INPUT_GET, 'debug'));
define('USER_LOGGED_IN', isloggedin());  // Was: ( 0 !== $USER->id )

if (DEBUG) {
    $CFG->debug = DEBUG_DEVELOPER;
    $CFG->debugdisplay = 1;
    ini_set( 'display_errors', 1 );
}

use IET_OU\Moodle\Auth\Ouopenid\Db\User as OuUser;

global $USER;  // Moodle global.

$fields = [ 'auth', 'email', 'firstname', 'id', 'lastip', 'lastname', 'username', 'currentcourseaccess' ];

$prof = OuUser::getMoodleProfile($USER);

$user = new StdClass();
foreach ($fields as $field) {
    $user->{ $field } = isset($USER->{ $field }) ? $USER->{ $field } : null;
}

$oucu = preg_match(OuUser::USERNAME_REGEX, $user->username, $matches) ? $matches[ 1 ] : $user->username;
$stat = $oucu ? 'ok' : 'warn';
$msg = USER_LOGGED_IN ? '' : 'Not logged in.';

if (DEBUG) {
    OuUser::debug($USER);
}

header('Access-Control-Allow-Origin: ' . $CFG->wwwroot); // Security.
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'stat' => $stat, 'msg' => $msg, 'debug' => OuUser::debugLevel(),
    'user' => $user,
    'profile' => $prof->profile,
    'body_class' => $prof->body_class,
    'user_roles' => OuUser::getRoles(),
    'redirect_url' => USER_LOGGED_IN ? $prof->redirect_url : null,
    'consent_embed_url' => USER_LOGGED_IN ? OuUser::getConsentEmbedUrl() : null,
    'strings' => USER_LOGGED_IN ? OuUser::getStringsAjax() : [],
    'config' => isset($CFG->auth_ouopenid_js_config) ? $CFG->auth_ouopenid_js_config : [],
]);

// End.
