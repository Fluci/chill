/*jshint
     esnext: true
 */
'use strict';

var Clock = function(callbackFunction) {
    var callback = callbackFunction;
    var timerId;
    var that = this;

    this.start = function() {
        var now = new Date();

        var hrs = now.getHours();
        var min = now.getMinutes();
        var sec = now.getSeconds();

        hrs = formatTime(hrs);
        min = formatTime(min);
        sec = formatTime(sec);

        var out = hrs + ':' + min + ':' + sec;

        if (callback !== null && callback !== undefined) {
            callback(out);
        }

        timerId = setTimeout(that.start, 500);
    };

    var formatTime = function(time) {
        if (time < 10) {
            return '0' + time;
        }
        return time;
    };
};
