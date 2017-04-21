<?php
/**
 * CLI. Commandline script to write example student data to a CSV file, regular size or BIG!
 *
 * @package auth_ouopenid
 * @author  Nick Freear, 09-March-2017.
 * @copyright (c) 2017 The Open University.
 */
define( 'CLI_SCRIPT', true );

require_once( __DIR__ . '/bootstrap.php' );

auth_ouopenid\bootstrap::vendor_autoload();
auth_ouopenid\bootstrap::dotenv_load();

define( 'FLAG_BIG', '--big' );
define( 'CSV_HEADINGS', 'oucu,course_present,tesla_instrument,notes,is_team,firstname,lastname,email' );
define( 'CSV_BIG_MULTIPLE', 800 );  // 4000 x 5 = 20,000.
define( 'CSV_DIR', getenv( 'OUOP_DATA_DIR' ));
define( 'CSV_TEAM_FILE', CSV_DIR . '/team.csv' );
define( 'CSV_FILENAME', CSV_DIR . '/example.csv' );
define( 'CSV_BIG_FILENAME', CSV_FILENAME );
// Was: define( 'CSV_BIG_FILENAME', __DIR__ . '/../example-big.csv' );

    $csvexamples = <<<CSV
jb123,K101-J,kd,(Joe Bloggs)
ae789,S100-J,kd,(Albert Einstein)
cd321,AA101-J,tpt,(Charles Dickens)
jl987,A322-J,tpt,(Jack London)
wc111,AA101-J,kd,(Wilkie Collins)

CSV;


    $bytes = 0;
    $limit = 1;
    $filename = CSV_FILENAME;

    if ($argc > 1 && $argv[ $argc - 1 ] === FLAG_BIG) {
        $limit = CSV_BIG_MULTIPLE;
        $filename = CSV_BIG_FILENAME;
    }

    $team = file_get_contents( CSV_TEAM_FILE );
    $bytes += file_put_contents( $filename, $team . "\n" );

    // Was: $bytes += file_put_contents( $filename, CSV_HEADINGS . "\n" );

    for ($idx = 0; $idx < $limit; $idx++) {
        $bytes += file_put_contents( $filename, $csvexamples, FILE_APPEND );
    }

    echo 'Outputting CSV file: ' . $filename;
    echo "\nBytes:  $bytes\n";

    // End.
