
# Changelog #

← [README][]

## Version 1.5.0 (Beta) - Pilot 3-B

 * _Date:  ~ 19 February ~ March 2018_
 * _Tag:  v1.5.0-beta_
 * Add functionality to reveal a TeSLA activity after the pre-activity
   survey is completed — `conditional_embedded_survey` PHP class (Bug #8)
   (That is, to "Complete" a survey embedded via `<IFRAME>` in a Moodle `mod/assign` conditional activity.)
 * Add functionality to "consent" to the TeSLA student-agreement in a
   survey embedded in an `<IFRAME>`. `tesla_consent` PHP class (Bug #8)
 * Automated accessibility testing — [pa11y-ci][];

## Version 1.4.2 (Beta) - Pilot 3-A

 * _Date:  ~ 6 - 20 December 2017_
 * _Tag:  v1.4.2-beta_
 * Analysis - get list of OUCUs for students who have consented (Chris E.)
 * Fixes to `bin/csv-import` and `bin/ouop-query` commandline scripts (PHP);
 * Re-implement `objToCsv()` Javascript — for TeSLA-plugin results page;
 * Add browser no-compatibility Javascript/message — (ua-compat): MSIE/ Trident/ Edge;

## Version 1.4.0 (Beta) - Pilot 3-A

 * _Date:  ~ 22 August - 6 December 2017_ (Deployed at start of TeSLA pilot 3-A)
 * _Tag:  v1.4.0-beta_
 * _Build:  "2017-12-05T13:25:56Z (MCD267768)"_
 * Javascript now built using Browserify (Bug #7)
 * Add 'poem server' — convert long texts (poems), into images of text, (Bug #6)
 * Add `fix_enrollment_callibrate_page()` Javascript,
 * Add long texts, with Chris E.
 * Injection of "long-texts" into TeSLA enrollment pages — `inject_long_texts()`, `quiz_word_count()` Javascripts,
 * Add survey-embed functionality,
 * Add `survey-end` handler, PHP + Javascript, BOS 'piping';
 * Add `tesla-db` PHP commandline script;
 * Node JS commandline to change length of voice-recog. timer;
 * Add a random part to test-email in `mdl_auth_ouopenid_users` DB table;
 * _Integrate ...?_

### Site-wide, 6-November-2017

 * Fix ``` The website "moodle.ouuk.tesla-project.eu" requires a client certificate. ```
 * See Ale.Okada email, 06-November-2017.
    * ...
    * https://httpd.apache.org/docs/2.4/mod/mod_ssl.html#sslverifyclient
    * https://forums.iis.net/t/1170428.aspx
 * IIS7 client certificate was set to "Accept" where most browsers ignored this Safari did not. Changed the option to "Ignore" client certificate, job done.

## Version 1.2.2 (Beta)

 * _Date:  14 August 2017_ (Deployed at the end of TeSLA Pilot 2, 19 June 2017)
 * _Tag:  v1.2.2-beta_
 * Deployed at the end of TeSLA Pilot 2, 19 June 2017,
 * Maintenance mode,
 * Update CLI PHP script for tranche/batch 4, #3,
 * `ouop-query` commandline PHP script,
 * `OUOP.survey_return_redirect()` Javascript,
 * _Etc ... [iet:9702401]_

## Version 1.2 (Beta)

 * _Date:  4 May 2017_ (Deployed during TeSLA pilot 2)
 * _Tag:  v1.2-beta_
 * Database upgrade - handle batches/tranches of students (Bug #5);

## Version 1.0 (Beta)

 * _Date:  6 March - 29 April 2017_ (Deployed at start of TeSLA pilot 2, Thursday 27 April 2017)
 * _Tag:  v1.0-beta_
 * Implement initial Moodle authentication plugin functionality (extension to Moodle OpenID plugin),
 * Login page, with support text for students,
 * Moodle `print_string` internationalization - customisable text,
 * Database table to hold pilot-study specific data — `mdl_auth_ouopenid_users`,
 * Ajax functionality to expose pilot-study specific user-data,
 * Site-wide Javascript to simplify login process for research participants,
 * Javascript to fix TeSLA plugin pages,
 * Site-wide LESS/CSS styles to simplify Moodle user-interface,
 * Handle unusual OUCU formats - 'csv-import' and 'index' PHP,
 * Add `bin/csv-import` and `csv-example` PHP commandline scripts;
 * Travis-CI build and test - PHP, Javascript lint, LESS build;
 * _... more ...!_

← [README][]

---
© 2017-2018 [The Open University][ou]. ([Institute of Educational Technology][iet])

[iet]: https://iet.open.ac.uk/
[ou]: http://www.open.ac.uk/

[README]: https://github.com/IET-OU/moodle-auth_ouopenid#readme
[pa11y-ci]: https://github.com/pa11y/pa11y-ci

[End]: //.
