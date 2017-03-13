<?php
/**
 * Simple CSV to database import script.
 *
 * @author Nick Freear, 13-March-2017.
 */
define( 'CLI_SCRIPT', true );

define( 'CSV_FILENAME', __DIR__ . '/../example.csv' );
define( 'CSV_HEADING', true );
define( 'CSV_UNSTRICT', true );

require_once __DIR__ . '/../../../config.php';
require_once $CFG->libdir . '/clilib.php';
require_once __DIR__ . '/../../../vendor/autoload.php';

use IET_OU\Moodle\Auth\Ouopenid\Db\User as OuUser;

$csv_file = CSV_FILENAME;

echo "OU-OpenID importer. Filename:  $csv_file\n";

if ($argc > 1 && $argv[ $argc - 1 ] === '--delete') {
    OuUser::delete();
    echo "User table emptied.\n";
}

$count = OuUser::insertFromCsv($csv_file, CSV_HEADING, CSV_UNSTRICT, function ($idx, $user_id) {
    echo '.';
});

echo "\nUsers inserted:  $count\n";


//End.
