/**
 * pragmaMx - Web Content Management System.
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 * $Id: addbid.js 6 2015-07-08 07:07:06Z PragmaMx $
 * based on: Centerblock mxTabs 1.1 for pragmaMx 0.1.10
 * written by (c) 2008 Siggi Braunert, http://www.sb-websoft.com
 */

Array.prototype.contains = function (elem) {
    var i;
    for (i = 0; i < this.length; i++) {
        if (this[i] == elem) {
            return true;
        }
    }
    return false;
};

function addbid(identifier) {
    var data = document.getElementById(identifier).firstChild.data;
    var inpbids = document.getElementById("inpbids");
    var arr = inpbids.value.split(",");
    if (arr.contains(data)) {
        alert(lang_bidselected);
    } else {
        if (inpbids.value.length > 0) kommata = "," ; else kommata = "";
        document.getElementById("inpbids").value = inpbids.value + kommata + data;
    }
    return false;
}
