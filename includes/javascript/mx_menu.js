/**
 * pragmaMx - Web Content Management System.
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 * $Id: mx_menu.js 6 2015-07-08 07:07:06Z PragmaMx $
 */

var men_titles = new Array;

function mxmenu_toggle(id, init) {
    if (init) {
        mxmenu_go(id, false);
    } else {
        var isvisible = $('.mx-menu .ul-' + id).is(':visible');
        $('.mx-menu .ul-' + id).toggle();
        mxmenu_go(id, isvisible);
    }
}

function mxmenu_slide(id) {
    var isvisible = $('.mx-menu .ul-' + id).is(':visible');
    $('.mx-menu .ul-' + id).slideToggle('fast');
    mxmenu_go(id, isvisible);
}

function mxmenu_go(id, isvisible) {
    var that = $('.mx-menu .ac-' + id);
    that.removeClass('d');

    if (isvisible) {
        that.removeClass('expanded');
        that.addClass('collapsed');
        if (men_titles[id]) {
            that.attr('title', men_titles[id]);
        } else {
            that.attr('title', lang_open);
        }
    } else {
        that.removeClass('collapsed');
        that.addClass('expanded');
        that.attr('title', lang_close);
    }
}

