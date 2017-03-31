<?php namespace IET_OU\Moodle\Auth\Ouopenid\Db;

/**
 * DB model for an OU-OpenID 'User' or potential pilot participant.
 *
 * (Note: follows PSR-2, not Moodle coding style.)
 *
 * @package auth_ouopenid
 * @author  Nick Freear, 13-March-2017.
 * @copyright (c) 2017 The Open University.
 *
 * @link https://docs.moodle.org/dev/Data_manipulation_API
 * @link https://github.com/goodby/csv#import-to-database-via-pdo
 * @link http://csv.thephpleague.com/8.0/examples/#importing-a-csv-into-a-database-table
 */

use Goodby\CSV\Import\Standard\Lexer;
use Goodby\CSV\Import\Standard\Interpreter;
use Goodby\CSV\Import\Standard\LexerConfig;

class User
{
    const USER_TABLE = 'auth_ouopenid_users';

    const CSV_OUCU = 0;  // CSV file column offsets.
    const CSV_TEAM = 4;

    const PREFIX = 'ouop_';
    const UNDEF_INSTRUMENT = 'kd';

    const OPENID_URL_REGEX = '@^http:\/\/openid\.open\.ac\.uk\/oucu\/(?P<oucu>\w+)$@';
    const USERNAME_REGEX = '@^httpopenidopenacukoucu(?P<oucu>\w+)$@';
    const USERNAME_REPLACE = '@^(httpopenidopenacukoucu)?@';

    /** Get plugin DB record for given username.
     * @return object
     */
    public static function getUser($username)
    {
        global $DB;  // Moodle global.

        $oucu = preg_replace(self::USERNAME_REPLACE, '', $username);

        $user = $DB->get_record(self::USER_TABLE, [ 'oucu' => $oucu ], $fields = '*', $strictness = IGNORE_MISSING);
        return $user;
    }

    /** Delete all records from plugin DB table.
     */
    public static function delete()
    {
        global $DB;  // Moodle global.

        return $DB->delete_records(self::USER_TABLE);
    }

    /** Insert user to plugin DB table.
     * @return int User ID.
     */
    public static function insertUser($user)
    {
        global $DB;  // Moodle global.

        $user_record = (object) [
            'oucu' => $user->oucu,
            'course_presentation' => $user->presentation,
            'teslainstrument' => $user->teslaintrument,
            'is_team'   => $user->is_team,
            'firstname' => $user->firstname,
            'lastname'  => $user->lastname,
            'email'     => $user->email,
            'timecreated' => time(),
        ];
        $user_id = $DB->insert_record(self::USER_TABLE, $user_record, $returnid = true);
        return $user_id;
    }

    /** Insert rows from CSV file to plugin DB table.
     * @return int Count of rows added.
     */
    public static function insertFromCsv($filename = '../example.csv', $ignore_heading = true, $unstrict = true, $callback = null)
    {
        $config = new LexerConfig();
        $lexer = new Lexer($config);

        $interpreter = new Interpreter();
        if ($unstrict) {
            $interpreter->unstrict(); // Ignore row column count consistency
        }

        $count = 0;

        $interpreter->addObserver(function (array $row) use (&$count, $ignore_heading, $callback) {
            global $DB;  // Moodle global.

            $count++;
            if ($count <= 1 && $ignore_heading) {
                echo "Ignore CSV heading row.\n";
                return;
            }

            $user_record = (object) [
                'oucu' => $row[ self::CSV_OUCU ],  # 0,
                'course_presentation' => $row[ self::CSV_OUCU + 1 ], # 1,
                'teslainstrument' => self::row($row, self::CSV_OUCU + 2),
                'notes'     => self::row($row, self::CSV_OUCU + 3),  # 3,
                'is_team'   => self::row($row, self::CSV_TEAM),      # 4,
                'firstname' => self::row($row, self::CSV_TEAM + 1),  # 5,
                'lastname'  => self::row($row, self::CSV_TEAM + 2),  # 6,
                'email'     => self::row($row, self::CSV_TEAM + 3),  # 7,
                'timecreated' => time(),
            ];
            $user_id = $DB->insert_record(self::USER_TABLE, $user_record, $returnid = true);

            if ($callback) {
                $callback ($count, $user_id);
            }
        });

        $lexer->parse($filename, $interpreter);

        return $count;
    }

    /** Attempt to add plugin data to Moodle custom profile fields [ DEPRECATED ]
    */
    public static function setMoodleUser($oucu, &$m_user, $fn = null)
    {
        $ou_user = self::getUser($oucu);

        if ($ou_user->is_team) {
            $m_user->firstname = $ou_user->firstname;
            $m_user->lastname  = $ou_user->lastname;
            $m_user->email = $ou_user->email;
        }

        $m_user->profile[ self::PREFIX . 'id' ] = $ou_user->id;
        $m_user->profile[ self::PREFIX . 'oucu' ] = $ou_user->oucu;
        $m_user->profile[ self::PREFIX . 'course_presentation' ] = $ou_user->course_presentation;
        $m_user->profile[ self::PREFIX . 'teslainstrument' ] = $ou_user->teslainstrument;
        $m_user->profile[ self::PREFIX . 'is_team' ] = (boolean) $ou_user->is_team;
        $m_user->profile[ self::PREFIX . 'notes' ] = $ou_user->notes;
        $m_user->profile[ self::PREFIX . 'fn' ] = $fn;
    }

    /** Get authentication plugin-related profile data [ MIS-NAMED ]
     * @return object
     */
    public static function getMoodleProfile($mdl_user)
    {
        if (0 === $mdl_user->id) {
            self::debug([ __FUNCTION__, 'Guest user. Aborting' ]);
            return (object) [
                'profile' => (object) [],
                'body_class' => null,
                'redirect_url' => null,
            ];
        }

        $profile = [];
        $mdl_profile = isset($mdl_user->profile) ? $mdl_user->profile : [];

        if (isset($mdl_user->username)) {
            $mdl_profile = self::getUser($mdl_user->username);
        }

        foreach ($mdl_profile as $key => $value) {
            if ('is_team' === $key) {
                 $profile[ self::PREFIX . $key ] = (boolean) $value;
            } else {
                 $profile[ self::PREFIX . $key ] = $value;
            }
        }

        /*foreach ($mdl_profile as $key => $value) {
            if (0 === strpos($key, self::PREFIX)) {
                $profile[ $key ] = $value;
            }
        }*/

        return (object) [
            'profile' => (object) $profile,
            'body_class' => self::bodyClasses($profile),
            'redirect_url' => self::getRedirectUrl($mdl_profile),
        ];
    }

    /** Get Moodle roles for currently logged in user.
     * @return object
     */
    public static function getRoles()
    {
        global $USER;  // Moodle global.

        $context = \context_system::instance();
        $roles = get_user_roles($context, $USER->id, false);

        self::debug([ __FUNCTION__, $roles, $USER->id ]);
        return (object) [ 'is_admin' => is_siteadmin(), 'roles' => $roles, 'is_loggedin' => isloggedin() ];
    }

    // ====================================================================

    /** Get URL relating to the TeSLA instrument assigned to the user (in the DB) [ MOVE ] ?
     * @return string
     */
    protected static function getRedirectUrl($profile, $action = null)
    {
        global $CFG;  // Moodle global.

        $redirects = $CFG->auth_ouopenid_redirects;
        $instrument = isset($profile->teslainstrument) ? $profile->teslainstrument : self::UNDEF_INSTRUMENT;

        if (! isset($profile->teslainstrument)) {
            self::debug([ __FUNCTION__, 'Undefined instrument', $profile ]);
        }

        $url = $redirects[ $instrument ]->url;

        return $CFG->wwwroot . sprintf($url, $action);
    }

    /** Get Google Doc. embed URL [ MOVE ]
     * @return string
     */
    public static function getConsentEmbedUrl()
    {
        global $CFG;  // Moodle global.

        return isset($CFG->auth_ouopenid_consent_embed_url) ? $CFG->auth_ouopenid_consent_embed_url : null;
    }

    /** Get language strings for Javascript / Ajax [ MOVE ]
     * @return object
     */
    public static function getStringsAjax()
    {
        $string_ids = [ 'continuelink', 'form_warning', 'wordcount', 'wordcount_title', 'continuebutton', 'question_progress', 'return_msg', 'newenrol_msg' ];

        return get_strings($string_ids, 'auth_ouopenid');
    }

    /** Output arbitrary data, eg. to HTTP header.
    */
    public static function debug($obj)
    {
        static $count = 0;
        header(sprintf('X-auth-ou-openid-%02d: %s', $count, json_encode($obj)));
        $count++;
    }

    /** Output the Moodle debug level.
     * @link https://github.com/moodle/moodle/blob/master/lib/setuplib.php#L30-L40
     * @return string
     */
    public static function debugLevel()
    {
        global $CFG;  // Moodle global.

        switch ($CFG->debug) {
            case DEBUG_NONE:
                $level = 'debug-none';
                break;
            case DEBUG_ALL:
            case DEBUG_DEVELOPER:  // Fall-through.
                $level = 'debug-dev';
                break;
            default:
                $level = 'debug-other';
                break;
        }
        return $level;
    }

    protected static function row($row, $offset)
    {
        return isset($row[ $offset ]) ? $row[ $offset ] : null;
    }

    protected static function bodyClasses($fields)
    {
        global $CFG; // Moodle global.

        $body_classes = [ isset($CFG->auth_ouopenid_body_class) ? $CFG->auth_ouopenid_body_class : '' ];

        foreach ($fields as $key => $value) {
            $body_classes[] = preg_replace('/[^a-z\d_\-]+/i', '', "$key-$value");
        }
        return implode(' ', $body_classes);
    }
}

//End.
