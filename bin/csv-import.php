#!/usr/bin/env php
<?php

/**
 * CLI. Simple CSV to database import commandline script.
 *
 * @package auth_ouopenid
 * @author  Nick Freear, 13-March-2017, 06-Dec-2017.
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
define( 'FANCY_PROGRESS', $argc > 1 && $argv[ $argc - 1 ] === '--fancy' );
define( 'PROGRESS_COLOR', 'magenta' );

use IET_OU\Moodle\Auth\Ouopenid\Db\User as OuUser;
// Was: use Dariuszp\CliProgressBar;

cli_heading('OU-OpenID CSV importer');
cli_writeln(date('c') . ' --> Start csv import');

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
    $csvfile = CSV_DIR . '/' . clean_param($argv[ $argc - 1 ], PARAM_PATH);
} else {
    $csvfile = CSV_DIR . CSV_FILENAME;
}

$lineCount = OuUser::countFileLines($csvfile);

cli_writeln("Filename:  $csvfile");
cli_writeln("Records:   $lineCount");

if (FANCY_PROGRESS) {
    $bar = new \Dariuszp\CliProgressBar( $lineCount );
    $bar->{ 'setColorTo' . ucfirst(PROGRESS_COLOR) }();  // E.g. ->setColorToMagenta();
    $bar->display();
}

$count = OuUser::insertFromCsv($csvfile, CSV_HEADING, CSV_UNSTRICT, function ($idx, $userid) {
    if (FANCY_PROGRESS) {
        $bar->progress();
    } else {
        fwrite(STDERR, '.');  // Was: cli_write('.');
    }
});
fwrite(STDERR, "\n");
if (FANCY_PROGRESS) {
    $bar->end();
}

cli_write(sprintf( "\nWarnings (%d): ", count(OuUser::getWarnings()) ));
cli_writeln(json_encode( OuUser::getWarnings(), JSON_PRETTY_PRINT ));

cli_writeln("\nUsers inserted:  $count");
cli_writeln(date('c') . ' --> End csv import');

// End.
