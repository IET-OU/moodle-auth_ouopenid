<?php namespace IET_OU\Moodle\Auth\Ouopenid\Db;

/**
 * DB model for an OU-OpenID 'User' or potential pilot participant.
 *
 * (Note: follows PSR-2, not Moodle coding style.)
 *
 * @author Nick Freear, 13-March-2017.
 * @copyright (c) 2017 The Open University.
 *
 * @link https://docs.moodle.org/dev/Data_manipulation_API
 * @link https://github.com/goodby/csv#import-to-database-via-pdo
 * @link http://csv.thephpleague.com/8.0/examples/#importing-a-csv-into-a-database-table
 */

//require_once __DIR__ . '/../vendor/autoload.php';

use Goodby\CSV\Import\Standard\Lexer;
use Goodby\CSV\Import\Standard\Interpreter;
use Goodby\CSV\Import\Standard\LexerConfig;

class User
{
    const USER_TABLE = 'auth_ouopenid_users';

    const CSV_OUCU = 0;  // CSV file column offsets.
    const CSV_TEAM = 4;

    const PREFIX = 'ouop_';

    const OPENID_URL_REGEX = '@^http:\/\/openid\.open\.ac\.uk\/oucu\/(?P<oucu>\w+)$@';
    const USERNAME_REGEX = '@^httpopenidopenacukoucu(?P<oucu>\w+)$@';

    public static function getUser($oucu)
    {
        global $DB;

        $user = $DB->get_record(self::USER_TABLE, [ 'oucu' => $oucu ], $fields = '*', $strictness = IGNORE_MISSING);
        return $user;
    }

    public static function delete()
    {
        global $DB;
        return $DB->delete_records(self::USER_TABLE);
    }

    public static function insertUser($user)
    {
        global $DB;

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
            global $DB;

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

    public static function getMoodleProfile($mdl_user)
    {
        $profile = [];
        $mdl_profile = isset($mdl_user->profile) ? $mdl_user_profile : [];

        foreach ($mdl_user->profile as $key => $value) {
            if (0 === strpos($key, self::PREFIX)) {
                $profile[ $key ] = $value;
            }
        }

        return (object) [
            'profile' => (object) $profile,
            'body_class' => self::bodyClasses($profile),
        ];
    }

    public static function debug($obj)
    {
        static $count = 0;
        header(sprintf('X-auth-ou-openid-%02d: %s', $count, json_encode($obj)));
        $count++;
    }

    protected static function row($row, $offset)
    {
        return isset($row[ $offset ]) ? $row[ $offset ] : null;
    }

    protected static function bodyClasses($fields)
    {
        $body_classes = [];
        foreach ($fields as $key => $value) {
            $body_classes[] = str_replace([ ' ', '_' ], '-', ($key . '-' . htmlentities($value, ENT_QUOTES)));
        }
        return implode(' ', $body_classes);
    }
}

//End.
