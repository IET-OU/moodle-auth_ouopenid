<?php
/**
 * JSON output. OU-OpenID authentication plugin.
 *
 * @author  Nick Freear, 08-March-2017.
 * @copyright (c) 2017 The Open University.
 */

require_once __DIR__ . '/../../config.php';
//require_once $CFG->libroot . '/???';

global $USER;

$fields = [ 'auth', 'email', 'firstname', 'id', 'lastip', 'lastname', 'profile', 'username', 'currentcourseaccess' ];

$user = new StdClass();

foreach ($fields as $field) {
    $user->{ $field } = isset($USER->{ $field }) ? $USER->{ $field } : null;
}

$oucu = preg_match('/httpopenidopenacukoucu(\w+)/', $user->username, $matches) ? $matches[ 1 ] : $user->username;

header('X-auth-ouopenid-A: '. json_encode( $USER ));

echo json_encode([ 'stat' => 'ok', 'user' => $user, 'oucu' => $oucu ]);


#End.
