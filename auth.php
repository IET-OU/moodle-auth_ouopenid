<?php
/**
 * OU-OpenID authentication plugin.
 *
 * @author  Nick Freear, 07-March-2017.
 * @copyright (c) 2017 The Open University.
 *
 * @link https://docs.moodle.org/dev/Authentication_plugins#Interfacing_to_API.27s
 */

require_once $CFG->libdir . '/authlib.php';


class auth_plugin_ouopenid extends auth_plugin_base {

    const OPENID_URL_REGEX = '@^http:\/\/openid\.open\.ac\.uk\/oucu\/(?P<oucu>\w+)$@';

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

    public static function set_user(&$resp, &$user) {
        $oucu = null;
        $identity_url = $resp->identity_url;
        if ($identity_url && preg_match(self::OPENID_URL_REGEX, $identity_url, $matches)) {
            $oucu = $matches[ 'oucu' ];
        }

        if ($oucu && $user->auth == 'openid' && ( ! $user->firstname || $user->firstname === 'test' )) {
            $user->firstname = $oucu;
        }

        if ($oucu && $user->auth == 'openid' && ! $user->email) {
            $user->email = $oucu . '@openmail.open.ac.uk';
        }

        $user->profile[ 'ouopenid_custom' ] = [ 'a' => 1 ];

        self::debug([
          __FUNCTION__, $identity_url, $oucu, $user->email, $user->username, 'userid=', $user->id ]);
    }

    public static function debug($obj) {
        static $count = 0;
        header(sprintf('X-auth-ou-openid-%02d: %s', $count, json_encode($obj)));
        $count++;
    }
}

//End.
