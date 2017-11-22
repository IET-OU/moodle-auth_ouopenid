<?php
/**
 * CLI. Run database queries to check data integrity.
 *
 * @package auth_ouopenid
 * @author  Nick Freear, 10-May-2017.
 * @copyright (c) 2017 The Open University.
 */
define( 'CLI_SCRIPT', true );

require_once(__DIR__ . '/bootstrap.php');

auth_ouopenid\bootstrap::vendor_autoload();
auth_ouopenid\bootstrap::moodle_config();

require_once($CFG->libdir . '/clilib.php');

use IET_OU\Moodle\Auth\Ouopenid\Db\User as OuUser;

cli_heading('OU-OpenID DB queries');

$counts = [
    'Total users' => OuUser::count(),
    'Tranche 1' => OuUser::count([ 'batch' => 0 ]),
    'Tranche 2' => OuUser::count([ 'batch' => 2 ]),
    'Tranche 3' => OuUser::count([ 'batch' => 3 ]),
    'Tranche 4' => OuUser::count([ 'batch' => 4 ]),
    'Tranche 5' => OuUser::count([ 'batch' => 5 ]),
    'Keystroke preset' => OuUser::count([ 'teslainstrument' => 'kd' ]),
    'Plagiarism preset' => OuUser::count([ 'teslainstrument' => 'tpt' ]),
    'Voice recog preset' => OuUser::count([ 'teslainstrument' => 'vr' ]),
    'No preset' => OuUser::count([ 'teslainstrument' => null ]),  // Should always be '0'!
    'Is team' => OuUser::count([ 'is_team' => 1 ]),
];

cli_write('Counts: ');
cli_writeln(json_encode( $counts , JSON_PRETTY_PRINT ));

exit;  // Work-in-progress!


$conditions = [ 'oucu' => '{ EDIT ME }' ];

$results = [
  OuUser::query($conditions),
];

cli_write('Results: ');
print_r( $results );

// End.
