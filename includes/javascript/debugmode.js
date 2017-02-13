/**
 * pragmaMx - Web Content Management System.
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 * $Id: debugmode.js 6 2015-07-08 07:07:06Z PragmaMx $
 */

$(document).ready(function() {

    $(".dbgmod").show();
    $(".debugservice, .message-debug").hide();

    $(".dbgmod").click(function() {
        $(this).toggleClass("dbgmod-min");
        $(this).toggleClass("dbgmod-big");
    });

    $(".dbg-error").dblclick(function() {
        $(this).hide();
    });

    $(".dbg-frame").click(function() {
        $(".dbg-frame").hide();
    });

});
