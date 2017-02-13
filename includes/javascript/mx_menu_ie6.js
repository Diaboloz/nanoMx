/**
 * pragmaMx - Web Content Management System.
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 * $Id: mx_menu_ie6.js 6 2015-07-08 07:07:06Z PragmaMx $
 */

var men_titles = new Array;

/* hover-effekt fuer div im ie per js nachbilden */
$('.mx-menu li div').hover(function() {
    $(this).addClass('hover');
}, function() {
    $(this).removeClass('hover');
});

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
    $('.mx-menu .ul-' + id).toggle();
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
