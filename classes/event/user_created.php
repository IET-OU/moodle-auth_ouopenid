<?php
/**
 * OU-OpenID: user created event.
 *
 * @package    auth_ouopuenid
 * @copyright  2017 Nick Freear & The Open University.
 */

namespace auth_ouopenid\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Event when new user profile is created.
 *
 * @package    auth_ouopenid
 * @since      Moodle 2.6
 * @copyright  2017 Nick Freear & The Open University.
 */
class user_created extends \core\event\base {

    /**
     * Initialise required event data properties.
     */
    protected function init() {
        $this->data['objecttable'] = 'user';
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Returns event name.
     * @return string
     */
    public static function get_name() {
        return 'event_ouopenid_usercreated';
    }

    /**
     * Returns non-localised event description with id's for admin use only.
     * @return string
     */
    public function get_description() {
        return "OU-OpenID: The user with id '$this->userid' created the user with id '$this->objectid'.";
    }

    /**
     * Returns relevant URL.
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/user/view.php', array('id' => $this->objectid));
    }

    /**
     * Return the username of the logged in user.
     * @return string
     */
    public function get_username() {
        return $this->other['username'];
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

    /**
     * Return name of the legacy event, which is replaced by this event.
     * @return string legacy event name
     */
    public static function X_get_legacy_eventname() {
        return 'user_created';
    }

    /**
     * Return user_created legacy event data.
     * @return \stdClass user data.
     */
    protected function get_legacy_eventdata() {
        return $this->get_record_snapshot('user', $this->objectid);
    }

    /**
     * Returns array of parameters to be passed to legacy add_to_log() function.
     * @return array
     */
    protected function get_legacy_logdata() {
        return array(SITEID, 'user', 'add', '/view.php?id='.$this->objectid, fullname($this->get_legacy_eventdata()));
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->relateduserid)) {
            debugging('The \'relateduserid\' value must be specified in the event.', DEBUG_DEVELOPER);
            $this->relateduserid = $this->objectid;
        }
    }

    /**
     * Create instance of event.
     *
     * @since Moodle 2.6.4, 2.7.1
     *
     * @param int $userid id of user
     * @return user_created
     */
    public static function create_from_userid($userid) {
        $data = array(
            'objectid' => $userid,
            'relateduserid' => $userid,
            'context' => \context_user::instance($userid)
        );

        // Create user_created event.
        $event = self::create($data);
        return $event;
    }

    public static function get_objectid_mapping() {
        return array('db' => 'user', 'restore' => 'user');
    }
}
