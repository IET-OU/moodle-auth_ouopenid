<?php
/**
 * Write example student data to a CSV file, regular size or BIG!
 *
 * @author NDF, 09-March-2017.
 */

define( 'FLAG_BIG', '--big' );
define( 'CSV_HEADINGS', 'oucu,course_present,tesla_instrument,notes' );
define( 'CSV_BIG_TIMES', 100 );  // 4000 x 5 = 20,000.
define( 'CSV_FILENAME', './example.csv' );
define( 'CSV_BIG_FILENAME', './example-big.csv' );

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
        $limit = CSV_BIG_TIMES;
        $filename = CSV_BIG_FILENAME;

        /*for ( $idx = 0; $idx < CSV_BIG_TIMES; $idx++ ) {
            $bytes += file_put_contents( CSV_BIG_FILENAME, $csv_examples, FILE_APPEND );
        }
        echo 'Outputting big CSV file! ' . CSV_BIG_FILENAME;*/

    } /*else {
        $bytes = file_put_contents( CSV_FILENAME, $csv_examples );
        echo 'Outputting CSV file! ' . CSV_FILENAME;
    }*/

    $bytes += file_put_contents( $filename, CSV_HEADINGS . "\n" );

    for ( $idx = 0; $idx < $limit; $idx++ ) {
        $bytes += file_put_contents( $filename, $csv_examples, FILE_APPEND );
    }

    echo 'Outputting CSV file: ' . $filename;
    echo "\nBytes:  $bytes\n";
