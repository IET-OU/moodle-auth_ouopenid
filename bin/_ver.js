#!/usr/bin/env node

/**
 * CLI. Implant version / timestamp info. in index.js.
 *
 * Runs POST-build !!
 *
 * @copyright © Nick Freear, 06-Sep-2017.
 * @copyright © The Open University.
 */

const replace = require('replace');
const os = require('os');
// const INDEX_JS = path('/../js/index.js');
const DIST_JS = path('/../dist.js');
const DIST_CSS = path('/../style/ouop-styles.css');
const TS = (new Date()).toISOString()
    .replace(/\.\d+Z/, 'Z') + ' (%s)'.replace(/%s/, os.hostname()); // Strip milli-seconds!

console.warn('BUILD_TIME :', TS);

replace({
  paths: [ DIST_JS ], // Was: INDEX_JS.
  regex: /BUILD_TIME = '.+';(.+Auto.)?/,
  replacement: 'BUILD_TIME = \'' + TS + '\'; // <Auto>',
  count: true,
  recursive: false
});

replace({
  paths: [ DIST_CSS ],
  regex: /\$ BUILD_TIME \$/,
  replacement: '$ Build time: ' + TS + ' $',
  count: true,
  recursive: false
});

function path (file) {
  return require('path').join(__dirname + file);
}
