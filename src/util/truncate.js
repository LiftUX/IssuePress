angular.module('ui.truncate', [])

.filter('truncate', function(){
  return function( text, length, ending ) {
    if (isNaN(length))
      length = 120;

    if (ending === undefined)
      ending = "...";

    if (text.length <= length || text.length <= length) {
      return text;
    }
    else {

      var t = text.trim().split('\n');
      text = t[0];

      return String(text).substring(0, length) + ending;
    }
  };
});
