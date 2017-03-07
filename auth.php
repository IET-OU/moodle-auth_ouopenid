<?php
// Was: No-op ?!!

/**
 * OU-OpenID authentication plugin.
 *
 * @author  Nick Freear, 07-March-2017.
 * @copyright (c) 2017 The Open University.
 *
 * @link https://docs.moodle.org/dev/Authentication_plugins#Interfacing_to_API.27s
 */

require_once $CFG->libdir . '/authlib.php';
//require_once __DIR__ . '/../moodle-auth_openid/auth.php';


class auth_plugin_ouopenid extends auth_plugin_base {

    /**
     * Class constructor
     *
     * Assigns default config values and checks for requested actions
     */
    public function __construct() {
        $this->authtype     = 'ouopenid';
//--More--(50%)
        $this->pluginconfig = 'auth/'.$this->authtype;
        $this->roleauth     = 'auth_'.$this->authtype;
        $this->errorlogtag  = '['.strtoupper($this->roleauth).'] ';
        $this->config       = get_config($this->pluginconfig);
    }

    // TODO: override ??
    public function _X_pre_loginpage_hook() { }

    public function _X_pre_user_login_hook(&$user) {
        var_dump($user); exit;
    }

    public static function debug($obj) {
        static $count = 0;
        header(sprintf('X-auth-ou-openid-%02d: %s', $count, json_encode($obj)));
        $count++;
    }
}

//End.
