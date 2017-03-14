<?php
/**
 * JSON output. OU-OpenID authentication plugin.
 *
 * @author  Nick Freear, 08-March-2017.
 * @copyright (c) 2017 The Open University.
 */

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../../vendor/autoload.php';

use IET_OU\Moodle\Auth\Ouopenid\Db\User as OuUser;

global $USER;

$fields = [ 'auth', 'email', 'firstname', 'id', 'lastip', 'lastname', /*'profile',*/ 'username', 'currentcourseaccess' ];

$user = new StdClass();
$profile = OuUser::getMoodleProfile($USER);

foreach ($fields as $field) {
    $user->{ $field } = isset($USER->{ $field }) ? $USER->{ $field } : null;
}

$oucu = preg_match('/httpopenidopenacukoucu(\w+)/', $user->username, $matches) ? $matches[ 1 ] : $user->username;

if (filter_input(INPUT_GET, 'debug')) {
    header('X-auth-ouopenid-A: '. json_encode( $USER ));
}

echo json_encode([ 'stat' => 'ok', 'user' => $user, 'oucu' => $oucu, 'profile' => $profile->profile, 'body_class' => $profile->body_class ]);


#End.
