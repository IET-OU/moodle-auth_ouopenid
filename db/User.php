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
                'teslainstrument' => $row[ self::CSV_OUCU + 2 ],
                //'notes' => $row[ self::CSV_OUCU + 3 ],    # 3,
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

    protected static row($row, $offset) {
        return isset($row[ $offset ]) ? $row[ $offset ] : null;
    }
}

//End.
