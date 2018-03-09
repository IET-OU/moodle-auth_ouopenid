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
require_once __DIR__ . '/local/base.php';

use IET_OU\Moodle\Auth\Ouopenid\Db\User as OuUser;
use auth_ouopenid\local\base;

class user_event_observer extends base {

    public static function core_module_completed($event) {
        self::debug(__METHOD__);
    }

    public static function core_user_created($event) {

        // TODO: Enroll the user on course!
        // self::enroll_user();

        // Was: self::redirect($user_created = true, __FUNCTION__);

        self::debug(__FUNCTION__);
    }

    public static function core_user_loggedin($event) {
        self::redirect($user_created = false, __FUNCTION__);
    }

    public static function embed_event_data(\core\event\base $event) {
        if (headers_sent()) {
            echo "\n<script data-ouop-event='1' type='application/json'>" .
                json_encode( $event->get_data() ) .
                "</script>\n";
        } else {
            self::debug([ __FUNCTION__, $event->get_data() ]);
        }
    }

    protected static function redirect($user_created, $fn) {
        global $CFG, $USER;  // Moodle globals.

        $enabled = isset($CFG->auth_ouopenid_redirect_enable) ? $CFG->auth_ouopenid_redirect_enable : null;
        $redirects  = isset($CFG->auth_ouopenid_redirects) ? $CFG->auth_ouopenid_redirects : null;

        if (! isset($USER->username)) {
            return self::debug([ __FUNCTION__, 'Warning, no username.', $USER ]);
        }

        $ou_profile = OuUser::getUser($USER->username);

        self::debug([ __FUNCTION__, $fn, $redirects, $ou_profile ]);

        if (! $redirects || ! $enabled) {
            return self::debug([ __FUNCTION__, 'Warning, redirect is disabled (or missing).' ]);
        }

        $url = OuUser::getRedirectUrl($ou_profile, $user_created ? 'newenrol' : 'return');

        header('Location: '. $url, true, 302);
        exit();
    }
}

//End.
