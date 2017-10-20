#!/usr/bin/env node

/**
 * CLI. Fix the "counter" integer in local/{TESLA}/templates/enrollment_activity_audio.mustache .
 *
 * Run me POST-build !!
 *
 *   grep count ../../local/moodle-local-tesla/templates/*.mustache
 *
 * @copyright © Nick Freear, 19-October-2017.
 * @copyright © The Open University.
 */

const replace = require('replace');
const ENV = require('./../../../.env.json');

const TEMPLATE = path('/../../../local/moodle-local-tesla/templates/enrollment_activity_audio.mustache');
const TEMPLATE_DEV = path('/../../moodle_local_tesla/templates/enrollment_activity_audio.mustache');

const AUDIO_COUNTER = ENV.audio_counter || 60;

console.warn('fix-tesla-tpl. Audio counter :', AUDIO_COUNTER);

if (ENV.dev) {

  replace({
    paths: [ TEMPLATE_DEV ],
    regex: /var count\s?=\s?(6|\d+)0;(.+Auto.)?/,
    replacement: 'var count = ' + AUDIO_COUNTER + '; // <Auto>',
    count: true,
    recursive: false
  });

} else {

  replace({
    paths: [ TEMPLATE ],
    regex: /var count\s?=\s?(6|\d+)0;(.+Auto.)?/,
    replacement: 'var count = ' + AUDIO_COUNTER + '; // <Auto>',
    count: true,
    recursive: false
  });

}

function path (file) {
  return require('path').join(__dirname + file);
}
