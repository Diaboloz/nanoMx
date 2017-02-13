/**
 * pragmaMx - Web Content Management System.
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 * $Id: jquery.theme.js,v 1.4 2013-04-07 16:33:09 tora60 Exp $
 */

if (isIE == 'undefined') var isIE = false;
if (isIE6 == 'undefined') var isIE6 = false;

  // init
$(document).ready(function ($) {

  if (isIE6) {
    $('#main-wrapper').prepend("<div class='crap-browser-warning'>You're using a old and buggy browser. Switch to a <a href='http://www.mozilla.com/firefox/'>normal browser</a> or consider <a href='http://www.microsoft.com/windows/internet-explorer'>upgrading your Internet Explorer</a> to the latest version</div>");
  }

    
    // scroll to top
    var offset = 320;
    var duration = 500;
    $(window).scroll(function() {
        if ($(this).scrollTop() > offset) {
            $('.back-to-top').fadeIn(duration);
        } else {
            $('.back-to-top').fadeOut(duration);
        }
    });   
    $('.back-to-top').click(function(event) {
        event.preventDefault();
        $('html, body').animate({scrollTop: 0}, duration);
        return false;
    })

  // Login & Logout
  $("#theme-login-trigger").click(function(){
    $(this).next("#theme-login-content").slideToggle();
    $(this).toggleClass("active");          
    
    if ($(this).hasClass("active")) $(this).find("span").html("&#x25B2;")
      else $(this).find("span").html("&#x25BC;")
    });

  $(".account input").focus(function () {
    var el = $(this);
    if (this.name == 'password' && el.attr("type") != 'password') {
      var el2 = $('<input/>').attr("type", "password").attr("name", "pass").attr("title", this.title);
      el.after(el2);
      el.remove();
      el2.focus();
    } else if (this.name == 'uname' && this.value == this.title) {
      this.value = '';
    }
    this.select();
  }).blur(function () {
    if (!this.value) {
      this.value = this.title;
    }
  });
  $(".account button[type='reset']").click(function () {
    closeAcc();
    return false;
  });

  // set accessibility roles on some elements trough js (to not break the xhtml markup)
  $("#navbar").attr("role", "navigation");
  $("#mainbar").attr("role", "main");
  $("#sidebar-right").attr("role", "complementary");
  $("#searchform").attr("role", "search");
  // Suchformular einblenden
  $("#searchform").show();

});