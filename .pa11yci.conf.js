/*!
  Pa11y-ci Configuration.
  Dynamically generate a '.pa11yci' JSON config object, using an environment variable.

  © Nick Freear, 07-March-2018.

  https://gist.github.com/nfreear/cece86bf6d5d4d531bf0646417a868fb
  https://github.com/pa11y/pa11y-ci/issues/48

  USAGE:
    export TEST_SRV=https://example.edu/moodle # Or, set via Travis-CI UI.
    pa11y-ci -c .pa11yci.conf.js
*/

var config = {
  defaults: {
    screenCapture: './_pa11y-screen-capture.png',
    standard: 'WCAG2AA', // Or, 'WCAG2AAA'
    hideElements: '.coursesearchbox', // 'Fieldset does not contain a legend element.' GitHub:squizlabs/HTML_CodeSniffer--Standards/WCAG2AAA/Sniffs/Principle1/Guideline1_3/1_3_1.js#L644
    ignore: [ 'notice' ],
    timeout: 8000,
    wait: 1500, // 2000,
    'X-verifyPage': null
  },
  urls: [
    '${TEST_SRV}/course/?_ua=pa11y',
    '${TEST_SRV}/auth/ouopenid/?_ua=pa11y',
    '${TEST_SRV}/survey-end/?_ua=pa11y#!-Missing-param-error'
  ]
};

function myPa11yCiConfiguration (urls, defaults) {

  console.error('Standard:', defaults.standard);
  // console.error('Env:', process.env.TEST_SRV);

  for (var idx = 0; idx < urls.length; idx++) {
    urls[ idx ] = urls[ idx ].replace('${TEST_SRV}', process.env.TEST_SRV);  // substitute(urls[ idx ]);
  }

  return {
    defaults: defaults,
    urls: urls
  }
};

// Important ~ call the function, don't return a reference to it!
module.exports = myPa11yCiConfiguration (config.urls, config.defaults);

// End config.
