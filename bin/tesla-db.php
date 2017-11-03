#!/usr/bin/env php
<?php

/**
 * CLI. Display or delete data for an OUCU in the Moodle-TeSLA DB tables.
 *
 * @package auth_ouopenid
 * @author  Nick Freear, 10-October-2017.
 * @copyright (c) 2017 The Open University.
 */
define( 'CLI_SCRIPT', true );

require_once( __DIR__ . '/bootstrap.php' );

// auth_ouopenid\bootstrap::vendor_autoload();
// auth_ouopenid\bootstrap::dotenv_load();
auth_ouopenid\bootstrap::moodle_config();
// auth_ouopenid\bootstrap::moodle_lib( 'clilib' );

require_once($CFG->libdir . '/clilib.php');

global $DB;  // Moodle global.

define( 'FLAG_DELETE', $argc === 3 && $argv[ 1 ] === '--delete' );
define( 'UNAME_PREFIX', 'httpopenidopenacukoucu' );
define( 'DB_PREFIX', 'local_tesla_' );

// 6 X 'mdl_telsa_local_*' tables in TOTAL!
$tables = [ 'agreement', 'enrollment', 'tep_history', 'user' ]; // NOT: '_conf', _conf_instrument.

$oucu = $argc >= 2 ? $argv[ $argc - 1 ] : null; // 'ndf42';

cli_heading( 'OU-OpenID TeSLA DB show/delete' );

if ( ! $oucu ) {
    cli_error( 'Error. Missing expected argument <OUCU>', 1 );
}

$user = $DB->get_record( 'user', [ 'username' => UNAME_PREFIX . $oucu ]);

if ( ! $user ) {
    cli_error( 'Warning. User not found or Invalid <OUCU> argument: ' . $oucu, 1 );
}

print_r([ $user->id, $user->username, FLAG_DELETE ]);

foreach ( $tables as $table ) {
    $tesla_data = $DB->get_records( DB_PREFIX . $table, [ 'userid' => $user->id ]);

    cli_writeln( 'Table: ' . DB_PREFIX . $table );

    echo process_tesla_data( $tesla_data );
}

if ( ! FLAG_DELETE ) {
    cli_writeln( 'Get data only (no delete).' );
    exit;
}

// FLAG_DELETE

$inp_delete = cli_input( 'Do you want to delete Moodle-TeSLA data for user? Y/n ' . $oucu );

if ( $inp_delete === 'Y' ) {

    foreach ( $tables as $table ) {
        $tesla_data = $DB->delete_records( DB_PREFIX . $table, [ 'userid' => $user->id ]);

        cli_writeln( 'Table, delete: ' . DB_PREFIX . $table );
        print_r( $tesla_data );
    }

    cli_writeln( 'Moodle-TeSLA data deleted for user: '. $oucu );
} else {
    cli_error( 'Cancel delete.', 1 );
}

function process_tesla_data( $tesla_data ) {

    foreach ( $tesla_data as $row ) {
        if ( isset( $row->timecreated ) ) {
            $row->timecreated_iso = date( 'c', $row->timecreated );
        }
        if ( isset( $row->timesubmitted ) ) {
            $row->timesubmitted_iso = date( 'c', $row->timesubmitted );
        }
    }
    $output = print_r( $tesla_data, $return = true );

    return preg_replace('/;base64,[^=]+/', ';base64, [...] ', $output);
}

// End.
