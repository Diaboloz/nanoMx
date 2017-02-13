/**
 * pragmaMx - Web Content Management System.
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 * $Id: reload.js 6 2015-07-08 07:07:06Z PragmaMx $
 */

var captcha_id;
var input_id;

function captcha_reload(id) {
    captcha_id = 'captcha' + id;
    input_id = 'captchainput' + id;

    if(window.XMLHttpRequest) {
        req = new XMLHttpRequest();
    } else if(window.ActiveXObject) {
        req = new ActiveXObject("Microsoft.XMLHTTP");
    }
    try {
        var currentTimeX = new Date();
        req.open("GET", "includes/classes/Captcha/serverside.php?x=" + currentTimeX.getMilliseconds(), true);
        req.onreadystatechange = captcha_triggered;
        req.send(null);
    } catch(e) {
        //nothing;
    }
}

function captcha_triggered() {
    if ((req.readyState == 4) && (req.status == 200)) {
        var currentTime = new Date();
        document.getElementById(captcha_id).src = req.responseText + currentTime.getMilliseconds();
    }
    var inputfield = document.getElementById(input_id);
    if(inputfield) {
        inputfield.focus();
    }
}
