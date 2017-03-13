<?php
/**
 * Simple CSV to database import script.
 *
 * @author Nick Freear, 13-March-2017.
 */
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../vendor/autoload.php';
//require_once __DIR__ . '/../vendor/autoload.php';

use IET_OU\Moodle\Auth\Ouopenid\Db\User as OuUser;

define( 'CSV_FILE', __DIR__ . '/../example.csv' );


$csv_file = CSV_FILE;

echo "OU-OpenID importer. Filename:  $csv_file\n";

if (argc > 1 && $argv[ $argc - 1 ] === '--delete') {
    OuUser::delete();
    echo "User table emptied.\n";
}

$count = OuUser::insertFromCsv($csv_file, true, function ($idx, $user_id) {
    echo '.';
});

echo "\nUsers inserted:  $count\n";


//End.
