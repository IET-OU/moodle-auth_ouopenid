// OUOP.tesla_results_statistics

var C = window.console;

module.exports = function ($) {
  'use strict';

  var $page = $('#page-local-tesla-views-tesla_results');
  var $rows = $page.find('#page-content table tbody tr');
  var counts = {
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
  return JSON.stringify(obj, null, 2).replace(/:/g, ',').replace(/_/g, ' ');
}
