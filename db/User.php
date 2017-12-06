<?php namespace IET_OU\Moodle\Auth\Ouopenid\Db;

/**
 * DB model for an OU-OpenID 'User' or potential pilot participant.
 *
 * (Note: this file follows PSR-2, not Moodle coding style.)
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
use Exception;

class User
{
    const USER_TABLE = 'auth_ouopenid_users';

    const CSV_OUCU = 0;  // CSV file column offsets.
    const CSV_TEAM = 4;
    const CSV_BATCH = 8; // Bug #5.

    const PREFIX = 'ouop_';
    const UNDEF_INSTRUMENT = 'kd';  // 'tpt';
    const INSTRUMENT_REGEX = '/^(kd|tpt|fa|vr|fr)/';

    const OUCU_REGEX = '@^[a-z]{2,4}\d{1,7}$@';
    const OPENID_URL_REGEX = '@^https?:\/\/openid\.open\.ac\.uk\/oucu\/(?P<oucu>\w+)$@';
    const USERNAME_REGEX = '@^https?openidopenacukoucu(?P<oucu>\w+)$@';
    const USERNAME_REPLACE = '@^(https?openidopenacukoucu)?@';

    protected static $warnings = [];

    /** Get plugin DB record for given username.
     * @return object
     */
    public static function getUser($username, $strictness = IGNORE_MISSING | IGNORE_MULTIPLE)
    {
        global $DB;  // Moodle global.

        $oucu = preg_replace(self::USERNAME_REPLACE, '', $username);

        $user = $DB->get_record(self::USER_TABLE, [ 'oucu' => $oucu ], $fields = '*', $strictness);
        return $user;
    }

    /** Count DB records, with or without conditions.
     * @return int
     */
    public static function count($conditions = null)
    {
        global $DB;  // Moodle global.
        return $DB->count_records(self::USER_TABLE, $conditions);
    }

    /** Select DB records, based on conditions.
     * @return array Array of objects.
     */
    public static function query($conditions, $limitnum = 4)
    {
        global $DB;  // Moodle global.
        return $DB->get_records(self::USER_TABLE, $conditions, $sort = '', $fields = '*', $from = 0, $limitnum);
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
                'oucu' => self::validateOucu($row[ self::CSV_OUCU ], $count),  # 0,
                'course_presentation' => $row[ self::CSV_OUCU + 1 ], # 1,
                'teslainstrument' => self::validateInstrument(self::row($row, self::CSV_OUCU + 2), $count, $row[ self::CSV_OUCU ]),
                'notes'     => self::row($row, self::CSV_OUCU + 3),  # 3,
                'is_team'   => self::row($row, self::CSV_TEAM),      # 4,
                'firstname' => self::row($row, self::CSV_TEAM + 1),  # 5,
                'lastname'  => self::row($row, self::CSV_TEAM + 2),  # 6,
                'email'     => self::row($row, self::CSV_TEAM + 3),  # 7,
                'timecreated' => time(),
                'batch'     => self::row($row, self::CSV_BATCH, 0),
            ];
            $user_id = $DB->insert_record(self::USER_TABLE, $user_record, $returnid = true);

            if ($callback && is_callable($callback)) {
                $callback ($count, $user_id);
            }
        });

        $lexer->parse($filename, $interpreter);

        return $count;
    }

    /** Return the number of new-lines in a text file (including CSV files).
     * @return int Line count.
     * @link https://stackoverflow.com/questions/2162497/efficiently-counting-the-number-of-lines-of-a-text-file-200mb
     */
    public static function countFileLines($filename = '../example.csv')
    {
        $linecount = 0;
        $handle = fopen($filename, 'r');
        while (! feof($handle)) {
            $line = fgets($handle);
            $linecount++;
        }
        fclose($handle);
        return $linecount;
    }

    protected static function validateOucu($oucu, $row)
    {
        if (!preg_match(self::OUCU_REGEX, $oucu)) {
            self::$warnings[] = [ 'row' => $row, 'msg' => 'Unexpected OUCU', 'oucu' => $oucu ];
            echo 'W';
            //throw new Exception('Unexpected OUCU format: ' . $oucu);
        }
        return $oucu;
    }

    protected static function validateInstrument($instrument, $row, $ref)
    {
        if (!preg_match(self::INSTRUMENT_REGEX, $instrument)) {
            throw new Exception(sprintf('Unexpected TeSLA instrument code, %d, %s: "%s"', $row, $ref, $instrument));
        }
        return $instrument;
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

    protected static function getUserDummy($username)
    {
         return (object) [
             'oucu' => preg_replace(self::USERNAME_REPLACE, '', $username),
             'course_presentation' => null,
             'teslainstrument' => self::UNDEF_INSTRUMENT,
             'notes'    => null,
             'is_team'  => false,
             'firstname'=> null,
             'lastname' => null,
             'email'    => null,
             'timecreated'=> time(),
             'batch'    => 0,
             'x_is_dummy' => true,
         ];
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
                'survey_urls' => null,
            ];
        }

        $profile = [];
        $mdl_profile = isset($mdl_user->profile) ? $mdl_user->profile : [];

        if (isset($mdl_user->username)) {
            $mdl_profile = self::getUser($mdl_user->username);
        }

        $mdl_profile = $mdl_profile ? $mdl_profile : self::getUserDummy($mdl_user->username);

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
            'survey_urls' => self::getSurveyUrls($mdl_profile),
        ];
    }

    public static function getSurveyUrls($profile)
    {
        global $CFG;  // Moodle global;

        $survey_urls = $CFG->auth_ouopenid_survey_urls;

        $batch = isset($profile->batch) ? $profile->batch : 0;
        $batch = isset($CFG->auth_ouopenid_batch_override) ? $CFG->auth_ouopenid_batch_override : $batch;

        if (! isset($survey_urls[ $batch ])) {
            self::debug([ __FUNCTION__, 'error', $batch ]);
        }
        return isset($survey_urls[ $batch ]) ? $survey_urls[ $batch ] : $survey_urls[ 0 ];
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

    public static function getWarnings()
    {
        return self::$warnings;
    }

    // ====================================================================

    /** Get URL relating to the TeSLA instrument assigned to the user (in the DB) [ MOVE ] ?
     * @return string
     */
    public static function getRedirectUrl($profile, $action = null)
    {
        global $CFG;  // Moodle global.

        $redirects = $CFG->auth_ouopenid_redirects;
        $instrument = isset($profile->teslainstrument) ? $profile->teslainstrument : self::UNDEF_INSTRUMENT;

        if (! isset($profile->teslainstrument)) {
            self::debug([ __FUNCTION__, 'Undefined instrument', $profile ]);
        }

        $url = $redirects[ $instrument ]->url;

        return $CFG->wwwroot . str_replace('%s', $action, $url);
        // Was: return $CFG->wwwroot . sprintf($url, $action);
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
     * @param bool $longTexts
     * @return object
     */
    public static function getStringsAjax($longTexts = false)
    {
        $string_ids = [ 'continuelink', 'form_warning', 'form_redirect_msg', 'wordcount', 'wordcount_title',
            'continuebutton', 'question_progress', 'return_msg', 'newenrol_msg', 'testmail', 'post_survey_msg', 'no_ua_compat_msg', 'no_ua_compat_url' ];

        $string_ids = $longTexts ? array_merge($string_ids, [ 'lngtxt_1', 'lngtxt_2', 'lngtxt_3', 'lngtxt_4', ]) : $string_ids;

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

    protected static function row($row, $offset, $default = null)
    {
        return isset($row[ $offset ]) ? $row[ $offset ] : $default;
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

// End.
