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
$string[ 'auth_ouopeniddescription' ] = 'This plugin is a wrapper around the OpenID authentication plugin.';

// A hack!
require_once __DIR__ . '/local_oupilot_poem.php';

// Used via Javascript / Ajax.
$string[ 'testmail' ] = 'tesla.ouuk+{$a}@gmail.com';
// Warning: no block-elements (e.g. <P>) in 'continuelink';
$string[ 'continuelink' ] = 'Continue to your pilot course &rarr;';
$string[ 'form_warning' ] = 'Please don\'t edit your user profile!';
$string[ 'form_redirect_msg' ] = 'Redirecting ... <p> If nothing happens just click "Continue" below.';
$string[ 'newenrol_msg' ] = 'You\'ve been enrolled in the {$a} course. Welcome!';
$string[ 'return_msg' ] = 'Welcome back to the {$a}!';
$string[ 'wordcount' ] = 'Words: {$a}';
$string[ 'wordcount_title' ] = 'Word count';
$string[ 'continuebutton' ] = 'Continue';
$string[ 'question_progress' ] = 'Question <i>{$a}</i><span> of {$a2}</span>';
$string[ 'post_survey_msg' ] =
  '<p>Thank you for completing the pilot activity.</p>
<p>Please <a href="{$a}">complete the short and important post-activity questionnaire</a>.</p>
<p>Thank you! Your participation is very much appreciated.</p>';

// Used in the login form (index.php)
$string[ 'login_title' ] = 'Sign in — TeSLA study (The Open University)'; // Page <title> element.
$string[ 'login_heading' ] = 'TeSLA study';
$string[ 'login_intro' ] = <<<EOT
<p>Thank you for agreeing to participate in The Open University’s TeSLA study.</p>
<p>The OU takes your Internet security very seriously. This means, to get started, there are three steps.</p>
EOT;
$string[ 'login_steps' ] = <<<EOT
<li> Please sign in below with your Open University username (OUCU).
  This will redirect you to the standard Open University login page.</li>
<li> Please sign in to the Open University as usual.
  No personal details are shared with TeSLA.
  This will redirect you to the OU OpenID page.</li>
<li> Please complete the OU OpenID page.
  This will redirect you to the TeSLA pilot.</li>
EOT;
$string[ 'login_label' ] = 'Open University username (<abbr title="{$a}">OUCU</abbr>)';
$string[ 'login_label_abbr' ] = 'Open University computer username';
// Form validation - title attribute - 'login_field_help'.
$string[ 'login_field_help' ] = 'Your OUCU — 2 to 4 letters, followed by 1 to 7 numbers.';
$string[ 'login_submit' ] = 'Sign in';
$string[ 'login_footer' ] = '© 2017 The Open University';
$string[ 'login_footer_link' ] = 'https://www.open.ac.uk/';

$string[ 'survey_end_title' ] =
$string[ 'survey_end_title_pre' ] = 'Redirecting to activity …';
$string[ 'survey_end_title_post' ] = 'The end';
$string[ 'survey_end_msg' ] =
$string[ 'survey_end_msg_pre' ] =
  '<p>Thank you for completing the questionnaire.</p>
<p>Redirecting to activity …</p>';
$string[ 'survey_end_msg_post' ] =
  '<p>Thank you for completing the questionnaire.</p>
<p>The end.</p>
<p>You can close your browser window or tab.</p>';

// End.
