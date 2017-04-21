<?php
/**
 * CLI. Simple CSV to database import commandline script.
 *
 * @package auth_ouopenid
 * @author  Nick Freear, 13-March-2017.
 * @copyright (c) 2017 The Open University.
 */
define( 'CLI_SCRIPT', true );

require_once(__DIR__ . '/bootstrap.php');

auth_ouopenid\bootstrap::vendor_autoload();
auth_ouopenid\bootstrap::dotenv_load();
auth_ouopenid\bootstrap::moodle_config();

require_once($CFG->libdir . '/clilib.php');

define( 'CSV_DIR', getenv( 'OUOP_DATA_DIR' ));
define( 'CSV_FILENAME', '/example.csv' );
define( 'CSV_HEADING', true );
define( 'CSV_UNSTRICT', true );

use IET_OU\Moodle\Auth\Ouopenid\Db\User as OuUser;


cli_heading('OU-OpenID CSV importer');

if ($argc > 1 && $argv[ $argc - 1 ] === '--delete') {
    $input = cli_input('Do you really want to delete? Type Y to delete or n to exit', 'n', [ 'n', 'Y' ], $case = true);
    if ($input === 'Y') {
        OuUser::delete();
        cli_writeln('User table emptied. Exiting.');
    } else {
        cli_writeln('Cancelled.');
    }
    exit;
}

if ($argc > 1) {
    $csvfile = CSV_DIR . '/' . clean_param($argv[ $argc - 1 ], PARAM_FILE);
} else {
    $csvfile = CSV_DIR . CSV_FILENAME;
}

cli_writeln("Filename:  $csvfile");

$count = OuUser::insertFromCsv($csvfile, CSV_HEADING, CSV_UNSTRICT, function ($idx, $userid) {
    cli_write('.');
});

cli_writeln("\nUsers inserted:  $count");


// End.
