/*!
  Pa11y-ci Configuration.
  Dynamically generate a '.pa11yci' JSON config object, using an environment variable.

  © Nick Freear, 07-March-2018.

  https://gist.github.com/nfreear/cece86bf6d5d4d531bf0646417a868fb
  https://github.com/pa11y/pa11y-ci

  USAGE:
    export TEST_SRV=https://example.edu/moodle
    pa11y-ci -c .pa11yci.js
*/

// Was: const substitute = require('shellsubstitute');

var defaults = {
  screenCapture: './_pa11y-screen-capture.png',
  standard: 'WCAG2AA',
  ignore: [ 'notice' ],
  timeout: 5000,
  wait: 1500
};

var urls = [
  '${TEST_SRV}/course/?_ua=pa11y',
  '${TEST_SRV}/auth/ouopenid/?_ua=pa11y',
  '${TEST_SRV}/survey-end/?_ua=pa11y#!-Missing-param-error'
];

module.exports = (function my_pa11y_ci_config () {

  console.error('Env:', process.env.TEST_SRV, process.env.MDL_SRV);

  for (var idx = 0; idx < urls.length; idx++) {
    urls[ idx ] = urls[ idx ].replace('${TEST_SRV}', process.env.TEST_SRV);  // substitute(urls[ idx ]);
  }

  return {
    defaults: defaults,
    urls: urls
  }
})();

/* {
  "urls": [
    "${MDL_SRV}/course/"
  ]
} */
