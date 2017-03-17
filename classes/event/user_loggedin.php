<?php
/**
 * OU-OpenID: user login event.
 *
 * @package    auth_ouopuenid
 * @copyright  2017 Nick Freear & The Open University.
 */

namespace auth_ouopenid\event;

defined('MOODLE_INTERNAL') || die();

/**
 * User login event class.
 *
 * @property-read array $other {
 *      Extra information about event.
 *      - string username: name of user.
 * }
 *
 * @package    auth_ouopuenid
 * @since      Moodle 2.6
 * @copyright  2017 Nick Freear & The Open University.
 */
class user_loggedin extends \core\event\base {

    /**
     * Returns non-localised event description with id's for admin use only.
     * @return string
     */
    public function get_description() {
        return "OU-OpenID: The user with id '$this->userid' has logged in.";
    }

    /**
     * Return legacy data for add_to_log().
     * @return array
     */
    protected function get_legacy_logdata() {
        return [SITEID, 'user', 'login', 'view.php?id=' . $this->data['objectid'] . '&course=' . SITEID,
            $this->data['objectid'], 0, $this->data['objectid']];
    }

    /**
     * Return event name.
     * @return string
     */
    public static function get_name() {
        return 'event_ouopenid_userloggedin';
    }

    /**
     * Get URL related to the action.
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/user/profile.php', ['id' => $this->data['objectid']]);
    }

    /**
     * Return the username of the logged in user.
     * @return string
     */
    public function get_username() {
        return $this->other['username'];
    }

    /**
     * Init method.
     * @return void
     */
    protected function init() {
        $this->context = \context_system::instance();
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = 'user';
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception when validation does not pass.
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->other['username'])) {
            throw new \coding_exception('The \'username\' value must be set in other.');
        }
    }

    public static function get_objectid_mapping() {
        return array('db' => 'user', 'restore' => 'user');
    }

    public static function get_other_mapping() {
        return false;
    }
}
