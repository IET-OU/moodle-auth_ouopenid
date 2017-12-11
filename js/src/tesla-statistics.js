// OUOP.tesla_results_statistics

var C = window.console;

module.exports = function ($) {
  'use strict';

  var $page = $('#page-local-tesla-views-tesla_results');
  var $rows = $page.find('#page-content table tbody tr');
  var $heading = $page.find('#page-content table thead').find('th:nth-child( 2 )');
  var counts = {
    instrument: $heading.text(),
    date: (new Date()).toISOString().replace(/\.\d{3}/, ''),
    total_rows: $rows.length,
    with_number: 0,
    just_zero: 0,
    no_enroll: 0,
    no_consent: 0,
    no_results: 0
  };

  $rows.each(function () {
    var cell_2 = $(this).find('td:nth-child( 2 )').text();

    counts.with_number += /[\d.]+/.test(cell_2);
    counts.just_zero += /^0$/.test(cell_2);
    counts.no_enroll += /Enrollment not passed/.test(cell_2);
    counts.no_consent += /The user has not accepted the informed consent/.test(cell_2);
    counts.no_results += /No results/.test(cell_2);
  });

  if ($page.length) {
    var summary = '<pre id="ouop-stats">Summary: %s</pre>'.replace(/%s/, objToCsv(counts));

    $page.find('table').before(summary);

    C.warn('ouop: TeSLA results stats:', counts);
  }

  return counts;
};

function objToCsv (obj) {
  var res = iterateObject(obj);
  return "\n" + JSON.stringify(res.header) + "\n" + JSON.stringify(res.value);
  // Was: return JSON.stringify(obj, null, 2).replace(/:/g, ',').replace(/_/g, ' ');
}

// https://stackoverflow.com/questions/11257062/converting-json-object-to-csv-format-in-javascript
// https://jsfiddle.net/dhou6y3o/
function iterateObject(obj) {
  var value = '', header = '';
          for (name in obj) {
            if (obj.hasOwnProperty(name)) {
              if (isObject(obj[name])) {
                var out = iterateObject(obj[name]);
                value += out.value;
                header += out.header;
              } else {
                value += removeNewLine(obj[name]) + '; ';
                header += name + '; ';
              }
            }
          }
  return {
    "value":value,
    "header":header
  };
}
function isObject(obj) {
  return (typeof obj === 'object');
}
function removeNewLine(item) {
  return item.toString().replace(/(\r\n|\n|\r)/gm,"");
}
