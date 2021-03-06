<?php
/**
 * OU-OpenID authentication plugin.
 *
 * @package auth_ouopenid
 * @author  Nick Freear, 07-March-2017.
 * @copyright (c) 2017 The Open University.
 *
 * @link https://docs.moodle.org/dev/Authentication_plugins#Interfacing_to_API.27s
 */

require_once $CFG->libdir . '/authlib.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use IET_OU\Moodle\Auth\Ouopenid\Db\User as OuUser;

class auth_plugin_ouopenid extends auth_plugin_base {

    /**
     * Class constructor
     *
     * Assigns default config values and checks for requested actions
     */
    public function __construct() {
        $this->authtype     = 'ouopenid';
        $this->pluginconfig = 'auth/' . $this->authtype;
        $this->roleauth     = 'auth_' . $this->authtype;
        $this->errorlogtag  = '[' . strtoupper($this->roleauth) . '] ';
        $this->config       = get_config($this->pluginconfig);
    }

    public static function set_user(&$resp, &$user, $fn = null) {
        $oucu = null;
        $identity_url = $resp->identity_url;
        if ($identity_url && preg_match(OuUser::OPENID_URL_REGEX, $identity_url, $matches)) {
            $oucu = $matches[ 'oucu' ];
        }

        if ($oucu && $user->auth == 'openid' && ( ! $user->firstname || $user->firstname === 'test' )) {
            $user->firstname = $oucu;
        }

        /*if ($oucu && $user->auth == 'openid' && ! $user->email) {
            $user->email = $oucu . '@openmail.open.ac.uk';
        }*/

        // Was: OuUser::setMoodleUser($oucu, $user, $fn);

        self::debug([
          __FUNCTION__, $identity_url, $oucu, $user->email, $user->username, 'userid=', $user->id, $user->profile ]);
    }

    public static function debug($obj) {
        return OuUser::debug($obj);
    }
}

// End.
