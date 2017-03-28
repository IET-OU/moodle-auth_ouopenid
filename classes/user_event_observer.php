<?php
/**
 * OU-OpenID. Event observer class.
 *
 * @package   auth_ouopenid
 * @copyright 2017 Nick Freear & The Open University.
 * @author    Nick Freear, 17-March-2017.
 */

namespace auth_ouopenid;

require_once __DIR__ . '/../../../vendor/autoload.php';

use IET_OU\Moodle\Auth\Ouopenid\Db\User as OuUser;

class user_event_observer {

    public static function core_user_created($event) {

        // TODO: Enroll the user on course!
        // self::enroll_user();

        self::redirect($user_created = true, __FUNCTION__);
    }

    public static function core_user_loggedin($event) {
        self::redirect($user_created = false, __FUNCTION__);
    }

    protected static function redirect($user_created, $fn) {
        // Moodle globals.
        global $CFG, $USER;

        $redirects  = $CFG->auth_ouopenid_redirects;

        $ou_profile = OuUser::getUser($USER->username);

        OuUser::debug([ __FUNCTION__, $fn, $redirects, $ou_profile ]);

        if (isset($redirects[ $ou_profile->teslainstrument ])) {
            $redirect_obj = $redirects[ $ou_profile->teslainstrument ];

            $url = sprintf($redirect_obj->url, $user_created ? 'newenrol' : 'return');

            header('Location: '. $url, true, 302);
            exit();
        }
        else {
            // ERROR !!
            OuUser::debug([ __FUNCTION__, 'ERROR', $ou_profile ]);
        }
    }
}

//End.
