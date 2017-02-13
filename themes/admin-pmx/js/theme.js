/**
 * pragmaMx - Web Content Management System.
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 * $Id: jquery.theme.js,v 1.4 2013-03-14 21:48:44 dia_bolo Exp $.theme.js,v 1.1 2013-02-18 22:30:42 tora60 Exp $
 */

if (isIE == 'undefined') var isIE = false;
if (isIE7 == 'undefined') var isIE7 = false;

// init
$(document).ready(function ($) {
  if (isIE7) {
    $('#main-content').prepend("<div class='warning crap-browser-warning'>You're using a old and buggy browser. Switch to a <a href='http://www.mozilla.com/firefox/'>normal browser</a> or consider <a href='http://www.microsoft.com/windows/internet-explorer'>upgrading your Internet Explorer</a> to the latest version</div>");
  }

  var switchcols = (function () {
    $('body').toggleClass('col2');
    $('body').toggleClass('mini-menu');
  });
  $('#switcher').click(function () {
    switchcols();
    var cols = ($('body').hasClass('col2')) ? 'col2' : 'mini-menu';
    $.cookie('adminpmxcols', cols, {
      expires: 7,
      path: cookiepath
    });
    return false;
  });
  var thecols = $.cookie('adminpmxcols');
  if (thecols == 'mini-menu' && $('body').hasClass('col2')) {
    switchcols();
  }

  /* widget-tools */
          $('.widget .widget-tools .fa-chevron-up').click(function () {
            var el = $(this).parents(".widget").children(".widget-body");
            if ($(this).hasClass("fa-chevron-up")) {
                $(this).removeClass("fa-chevron-up").addClass("fa-chevron-down");
                el.slideUp(300);
            } else {
                $(this).removeClass("fa-chevron-down").addClass("fa-chevron-up");
                el.slideDown(300);
            }
        });
        $('.widget .widget-tools .fa-times').click(function () {
            $(this).parents(".widget").parent().remove();
        });
    /* // widget-tools */


});



/**
 * Metis menu
 */
 $(function() {

    $('#admin-menu').metisMenu();

});


;(function ($, window, document, undefined) {

    var pluginName = "metisMenu",
        defaults = {
            toggle: true
        };
        
    function Plugin(element, options) {
        this.element = element;
        this.settings = $.extend({}, defaults, options);
        this._defaults = defaults;
        this._name = pluginName;
        this.init();
    }

    Plugin.prototype = {
        init: function () {

            var $this = $(this.element),
                $toggle = this.settings.toggle;

            $this.find('li.active').has('ul').children('ul').addClass('collapse in');
            $this.find('li').not('.active').has('ul').children('ul').addClass('collapse');

            $this.find('li').has('ul').children('a').on('click', function (e) {
                e.preventDefault();

                $(this).parent('li').toggleClass('active').children('ul').collapse('toggle');

                if ($toggle) {
                    $(this).parent('li').siblings().removeClass('active').children('ul.in').collapse('hide');
                }
            });
        }
    };

    $.fn[ pluginName ] = function (options) {
        return this.each(function () {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName, new Plugin(this, options));
            }
        });
    };

})(jQuery, window, document);
