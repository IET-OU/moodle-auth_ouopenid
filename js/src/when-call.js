// https://gist.github.com/nfreear/f40470e1aec63f442a8a

module.exports = function (whenTrueFN, callbackFN, interval) {
  'use strict';

  var WIN = window;

  var intId = WIN.setInterval(function () {
    var result = whenTrueFN();
    if (result) {
      WIN.clearInterval(intId);
      callbackFN(result, WIN);
    }
  }, interval || 300); // Milliseconds.
};
