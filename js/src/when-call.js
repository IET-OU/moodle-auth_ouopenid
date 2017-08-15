// https://gist.github.com/nfreear/f40470e1aec63f442a8a

module.exports = function (whenTrueFN, callbackFN, interval) {
  'use strict';

  var intId = window.setInterval(function () {
    var result = whenTrueFN();
    if (result) {
      window.clearInterval(intId);
      callbackFN(result);
    }
  }, interval || 300); // Milliseconds.
};
