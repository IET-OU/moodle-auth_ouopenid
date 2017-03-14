<?php
/**
 * Write example student data to a CSV file, regular size or BIG!
 *
 * @author NDF, 09-March-2017.
 */
define( 'CLI_SCRIPT', true );

define( 'FLAG_BIG', '--big' );
define( 'CSV_HEADINGS', 'oucu,course_present,tesla_instrument,notes,is_team,firstname,lastname,email' );
define( 'CSV_BIG_MULTIPLE', 100 );  // 4000 x 5 = 20,000.
define( 'CSV_TEAM_FILE', __DIR__ . '/../team.csv' );
define( 'CSV_FILENAME', __DIR__ . '/../example.csv' );
define( 'CSV_BIG_FILENAME', CSV_FILENAME );
#Was: define( 'CSV_BIG_FILENAME', __DIR__ . '/../example-big.csv' );

    $csv_examples = <<<CSV
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

    //$bytes += file_put_contents( $filename, CSV_HEADINGS . "\n" );

    for ( $idx = 0; $idx < $limit; $idx++ ) {
        $bytes += file_put_contents( $filename, $csv_examples, FILE_APPEND );
    }

    echo 'Outputting CSV file: ' . $filename;
    echo "\nBytes:  $bytes\n";
