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
  var has_consented = [];

  $rows.each(function () {
    var cell_1 = $(this).find('td:nth-child( 1 )').text();
    var cell_2 = $(this).find('td:nth-child( 2 )').text();

    counts.with_number += /[\d.]+/.test(cell_2);
    counts.just_zero += /^0$/.test(cell_2);
    counts.no_enroll += /Enrollment not passed/.test(cell_2);
    counts.no_consent += /The user has not accepted the informed consent/.test(cell_2);
    counts.no_results += /No results/.test(cell_2);

    if (! /The user has not accepted the informed consent/.test(cell_2)) {
      has_consented.push(cell_1.replace(/test, /, '').replace(/,/, ';'));
    }
  });

  if ($page.length) {
    var summary = '<div id="ouop-stats"><h3>Summary</h3> <pre>%s</pre></div>'.replace(/%s/, objToCsv(counts));

    summary += '<div id="ouop-has-consented"><h3>Has consented</h3> <pre>%s</pre></div>'.replace(/%s/, objToCsv(has_consented, false));

    $page.find('table').before(summary);

    C.warn('ouop: TeSLA results stats:', counts);
    C.warn('ouop: Has consented:', has_consented);
  }

  return counts;
};

function objToCsv (obj, with_header) {
  with_header = arguments.length > 1 ? with_header : true;
  var res = iterateObject(obj);
  return (with_header ? res.header.replace(/_/g, ' ') + "\n" : '') + res.value;
  // Was: return JSON.stringify(obj, null, 2).replace(/:/g, ',').replace(/_/g, ' ');
}

// https://stackoverflow.com/questions/11257062/converting-json-object-to-csv-format-in-javascript
// https://jsfiddle.net/dhou6y3o/
function iterateObject(obj, delimiter) {
  delimiter = delimiter || ', ';  // Was: '; '
  var value = '';
  var header = '';
  var name;
    for (name in obj) {
      if (obj.hasOwnProperty(name)) {
        if (isObject(obj[ name ])) {
          var out = iterateObject(obj[ name ]);
          value += out.value;
          header += out.header;
        } else {
          value += removeNewLine(obj[ name ]) + delimiter;
          header += name + delimiter;
        }
      }
    }
  return {
    header: header,
    value: value
  };
}

function isObject(obj) {
  return (typeof obj === 'object');
}

function removeNewLine(item) {
  return item.toString().replace(/(\r\n|\n|\r)/gm, '');
}
