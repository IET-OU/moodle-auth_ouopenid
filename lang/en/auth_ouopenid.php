<?php
/**
 * Language strings. OU-OpenID authentication plugin.
 *
 * @package auth_ouopenid
 * @author  Nick Freear, 08-March-2017.
 * @copyright (c) 2017 The Open University.
 *
 * @link https://docs.moodle.org/dev/String_API
 */

$string[ 'pluginname' ] = 'OU-OpenID';
$string[ 'auth_ouopeniddescription' ] = 'This plugin is a wrapper around the OpenID authentication plugin';

// Used in Javascript / Ajax.
$string[ 'continuelink' ] = 'Continue to your pilot course';
$string[ 'form_warning' ] = 'Please don\'t edit your user profile!';
$string[ 'wordcount' ] = 'Words: {$a}';
$string[ 'wordcount_title' ] = 'Word count';
$string[ 'continuebutton' ] = 'Continue';
$string[ 'question_progress' ] = 'Question <i>{$a}</i><span> of {$a2}</span>';

// Used in the login form (index.php)
$string[ 'login_title' ] = 'Sign in — TeSLA Pilot 2 (The Open University)'; // Page <title> element.
$string[ 'login_heading' ] = 'TeSLA pilot';
$string[ 'login_intro' ] =
  'Thank you for agreeing to participate in The Open University’s TeSLA pilot.
<p> The OU takes your Internet security very seriously. This means, to get started, there are three steps.';
$string[ 'login_steps' ] =
'<li> Please sign in below with your Open University username (OUCU).
  This will redirect you to the standard Open University login page.
<li> Please sign in to the Open University as usual.
  No personal details are shared with TeSLA.
  This will redirect you to the OU OpenID page.
<li> Please complete the OU OpenID page.
  This will redirect you to the TeSLA pilot.';
$string[ 'login_label' ] = 'Open University username (<abbr title="{$a}">OUCU</abbr>)';
$string[ 'login_label_abbr' ] = 'Open University computer username';
$string[ 'login_field_help' ] = 'Your OUCU — 2 to 4 letters, followed by 1 to 7 numbers.'; // Title attribute.
$string[ 'login_submit' ] = 'Sign in';
$string[ 'login_footer' ] = '© 2017 The Open University';
$string[ 'login_footer_link' ] = 'https://www.open.ac.uk/';

// End.
