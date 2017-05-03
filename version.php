<?php
/**
 * OU-OpenID module version information.
 *
 * @package auth_ouopenid
 * @author  Nick Freear, 07-March-2017.
 * @copyright (c) 2017 The Open University.
 * @license proprietary
 */

// Was: defined('MOODLE_INTERNAL') || die(); !

$plugin->version  = 2017042800;  // The current module version (Date: YYYYMMDDXX)
$plugin->requires = 2015101600;  // Requires this Moodle version
$plugin->component = 'auth_ouopenid';

$plugin->dependencies = [
    'auth_openid' => '2017030600'  // TODO: check!
];
