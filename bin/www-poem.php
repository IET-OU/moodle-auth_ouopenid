<?php
if ( php_sapi_name() === 'cli' ) die('Direct access not allowed. Exiting');

/**
 * A quick & dirty "poem server".
 *
 *     php -S localhost:8000 bin/www-poem.php
 *
 * @package auth_ouopenid
 * @author  Nick Freear, 16-August-2017.
 * @copyright (c) 2017 The Open University.
 */

// Direct include - don't use Moodle "get_string" !
require_once __DIR__ . '/../lang/en/local_oupilot_poem.php';

define( 'STRING_ID', filter_input( INPUT_GET, 'id', FILTER_SANITIZE_STRING ));
define( 'SERIF', filter_input( INPUT_GET, 'serif', FILTER_VALIDATE_BOOLEAN ));

$poem = STRING_ID && isset($string[ STRING_ID ]) ? $string[ STRING_ID ] : null;

if (! $poem) {
    header( 'HTTP/1.1 404 Not Found', 404 );
    ?> Poem / text not found. <?php
    exit;
}

?><!doctype html> <meta charset="utf-8"> <title> Poem (<?php echo STRING_ID ?>) </title>

<style>
  body { margin: 4px; color: #333; font: 1.1em <?php echo SERIF ? 'Georgia,' : 'sans-' ?>serif; }
  p { margin: 0; line-height: 1.2em; letter-spacing: .01em; }
  .verse { margin-top: .7em; }
  .v1 { margin: 0; }
  .in { text-indent: 1em; }
  h1  { font: 1.5em sans-serif; margin: .5em 0; }
</style>

<?php echo $poem ?>
