<?php
/**
 * This file is part of
 * pragmaMx - Web Content Management System.
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 *
 * pragmaMx is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * $Revision: 194 $
 * $Author: PragmaMx $
 * $Date: 2016-07-25 15:10:19 +0200 (Mo, 25. Jul 2016) $
 *
 * english language file by:
 * pragmaMx Developer Team
 * thanks for corrections to: Prakash Shetty, http://cruxware.host.sk/
 */

defined('mxMainFileLoaded') or die('access denied');

/* Datestrings und locale Einstellungen: */
define("_CHARSET", "utf-8"); // Test: äöüß
define("_LOCALE", "en_GB");
$old_setlocale = setlocale(LC_TIME, 0);
$locale = array('en_GB.UTF-8', 'en_GB.UTF8', 'en_GB', 'en', 'eng', 'english-uk', 'uk', 'GB', 'GBR', '826', 'CTRY_UNITED_KINGDOM', 'en_GB.ISO-8859-1');
define('_SETLOCALE', setlocale(LC_TIME, $locale));
setlocale(LC_TIME, $old_setlocale);
define('_SETTIMEZONE', 'Europe/London');
define('_DECIMAL_SEPARATOR', '.');
define('_THOUSANDS_SEPARATOR', ' ');
define('_SPECIALCHARS', '');
define("_SPECIALCHARS_ONLY", false); // Schrift besteht nur aus Nicht-ASCII Zeichen
define("_DOC_LANGUAGE", "en");
define("_DOC_DIRECTION", "ltr");
define("_DATESTRING", "%A, %B %d %Y");
define("_DATESTRING2", "%A, %B %d");
define("_XDATESTRING", "on %d/%m/%Y at %H:%M");
define("_SHORTDATESTRING", "%d/%m/%Y");
define("_XDATESTRING2", "%A, %B %d");
define("_DATEPICKER", _SHORTDATESTRING);
define("_TIMEFORMAT", "%I:%M %p");
define("_DATETIME_FORMAT","%d/%m/%Y %I:%M %p");
define("_SYS_INTERNATIONALDATES", 0); //0 = mm/dd/yyyy, 1 = dd/mm/yyyy
define("_SYS_TIME24HOUR", 0); // 1 = 24 hour time... 0 = AM/PM time
define("_SYS_WEEKBEGINN", 0); # the First Day in the Week: 0 = Sunday, 1 = Monday
define("_Z1", "All logos and trademarks in this site are property of their respective owner.<br />For more Details, take a look in our <a href=\"modules.php?name=Impressum\">Imprint</a>");
define("_Z2", "The comments are property of their posters,<br />all the rest © by <a href=\"" . PMX_HOME_URL . "\">" . $GLOBALS['sitename'] . "</a>");
define("_Z3", "This Website based on pragmaMx " . PMX_VERSION . ".");
define("_Z4", "You can syndicate our news using the file <a href=\"modules.php?name=rss\">backend.php</a>.");
define("_YES", "Yes");
define("_NO", "No");
define("_EMAIL", "Email");
define("_SEND", "Submit");
define("_SEARCH", "Search");
define("_LOGIN", "Login");
define("_WRITES", "writes");
define("_POSTEDON", "Posted on");
define("_NICKNAME", "Nickname");
define("_PASSWORD", "Password");
define("_WELCOMETO", "Welcome to");
define("_EDIT", "Edit");
define("_DELETE", "Delete");
define("_POSTEDBY", "Posted by");
define("_GOBACK", "[&nbsp;<a href=\"javascript:history.go(-1)\">Go Back</a>&nbsp;]");
define("_COMMENTS", "comments");
define("_BY", "by");
define("_ON", "on");
define("_LOGOUT", "Logout");
define("_HREADMORE", "read more...");
define("_YOUAREANON", "You are anonymous user. You can register for free by clicking <a href=\"modules.php?name=Your_Account\">here</a>");
define("_NOTE", "Note:");
define("_ADMIN", "Admin:");
define("_TOPIC", "Topic");
define("_MVIEWADMIN", "View: Administrators Only");
define("_MVIEWUSERS", "View: Registered Users Only");
define("_MVIEWANON", "View: Anonymous Users Only");
define("_MVIEWALL", "View: All Visitors");
define("_EXPIRELESSHOUR", "Expiration: Less than 1 hour");
define("_EXPIREIN", "Expiration in");
define("_UNLIMITED", "Unlimited");
define("_HOURS", "Hours");
define("_RSSPROBLEM", "Currently there is a problem with headlines from this site");
define("_SELECTLANGUAGE", "Select Language");
define("_SELECTGUILANG", "Select Interface Language:");
define("_BLOCKPROBLEM", "There is a problem right now with this block.");
define("_BLOCKPROBLEM2", "There isn't content right now for this block.");
define("_MODULENOTACTIVE", "This Module isn't active!");
define("_NOACTIVEMODULES", "Inactive Modules");
define("_NOVIEWEDMODULES", "hidden Modules");
define("_FORADMINTESTS", "(for Admin tests)");
define("_ACCESSDENIED", "Access Denied");
define("_RESTRICTEDAREA", "You are trying to access to a restricted area.");
define("_MODULEUSERS", "We are Sorry but this section of our site is for <i>Registered Users Only</i><br /><br />You can register for free by clicking <a href=\"modules.php?name=User_Registration\">here</a>, then you can<br />access this section without restrictions. Thanks.");
define("_MODULESADMINS", "We are Sorry but this section of our site is for <i>Administrators Only</i>");
define("_HOME", "Home");
define("_HOMEPROBLEM", "There is a big problem here: we do not have a Homepage!");
define("_ADDAHOME", "Add a Module in your Home");
define("_HOMEPROBLEMUSER", "There is a problem right now on the Homepage. Please check it back later.");
define("_DATE", "Date");
define("_HOUR", "Hour");
define("_UMONTH", "Month");
define("_YEAR", "Year");
define("_YEARS", "Years");
define("_JANUARY", "January");
define("_FEBRUARY", "February");
define("_MARCH", "March");
define("_APRIL", "April");
define("_MAY", "May");
define("_JUNE", "June");
define("_JULY", "July");
define("_AUGUST", "August");
define("_SEPTEMBER", "September");
define("_OCTOBER", "October");
define("_NOVEMBER", "November");
define("_DECEMBER", "December");
define("_WEEKFIRSTDAY", "Sunday");
define("_WEEKSECONDDAY", "Monday");
define("_WEEKTHIRDDAY", "Tuesday");
define("_WEEKFOURTHDAY", "Wednesday");
define("_WEEKFIFTHDAY", "Thursday");
define("_WEEKSIXTHDAY", "Friday");
define("_WEEKSEVENTHDAY", "Saturday");
define("_MAIN", "Main");
define("_TERMS", "Terms");
define("_TOP", "go top");
define("_SITECHANGE", "Change the Site");
define("_BANNED", "You have been banned from this website.<br />Please contact the webmaster or administrator for more information.");
define("_VKPBENCH1", "Page Generation in ");
define("_VKPBENCH2", " Seconds, with ");
define("_VKPBENCH3", " Database-Queries");
define("_ERRNOTOPIC", "Please select an topic.");
define("_ERRNOTITLE", "There is no title for this article.");
define("_ERRNOTEXT", "There is no content for this article.");
define("_ERRNOSAVED", "Sorry, the data couldn't be saved.");
define("_RETURNACCOUNT", "Return to Your Account Page");
define("_FORADMINGROUPS", "(group can not see)");
define("_GROUPRESTRICTEDAREA", "Sorry, you have no access to this part of our website.");
define("_NOGROUPMODULES", "Non-Group Modules");
define("_AB_LOGOUT", "logout");
define("_AB_SETTINGS", "settings");
define("_AB_MESSAGE", "Admin message");
define("_AB_TITLEBAR", "Admin menu");
define("_AB_NOWAITINGCONT", "no waiting content");
define("_AB_RESETBCACHE", "Reset blockcache");
define("_ERR_YOUBAD", "You have attempted to perform an illegal operation!");
define("_REMEMBERLOGIN", "Remember login");
define("_ADMINMENUEBL", "Administration");
define("_MXSITEBASEDON", "Site based on");
define("_WEBMAIL", "send mail");
define("_CONTRIBUTEDBY", "Contributed by");
define("_BBFORUMS", "Forums");
define("_BLK_MINIMIZE", "Minimize");
define("_BLK_MAXIMIZE", "Maximize");
define("_BLK_HIDE", "Hide");
define("_BLK_MESSAGE", "Message");
define("_BLK_MYBLOCKS", "Blocks configuration");
define("_BLK_EDITADMIN", "Change (Admin)");
define("_BLK_OPTIONS", "Block options");
define("_BLK_OPTIONSCLICK", "Click here to set blockoptions.");
define("_ADM_MESS_DATEEXPIRE", "Date");
define("_ADM_MESS_TIMES", "Time");
define("_ADM_MESS_DATESTART", "Startdate");
define("_ADM_MESS_TODAY", "Today");
define("_DEFAULTGROUP", "Defaultgroup");
define("_YOURELOGGEDIN", 'Thank you for logging in');
define("_YOUARELOGGEDOUT", "You are now logged out!");
define('_CHANGESAREOK', 'The changes were saved');
define('_CHANGESNOTOK', 'The changes could not be saved.');
define('_DELETEAREOK', 'The data were deleted.');
define('_DELETENOTOK', 'The data could not be deleted.');
define("_RETYPEPASSWD", "Retype password");
define('_USERNAMENOTALLOWED', 'The user name &quot;%s&quot; is reserved.'); // %s = sprintf()
define('_SYSINFOMODULES', 'Information about the installed modules');
define('_SYSINFOTHEMES', 'Information about the installed designs');
define("_ACCOUNT", "Your Account");
define('_MAXIMALCHAR', 'max.');
define("_SELECTPART", "Selection");
define("_CAPTCHAWRONG", "Wrong Captcha value");
define("_CAPTCHARELOAD", "Reload captcha");
define("_CAPTCHAINSERT", "Insert passphrase from image above:");
define("_ERROROCCURS", "Sorry, the following error occurs:");
define("_VISIT", "Visit");
define("_NEWMEMBERON", "New User Registration on");
define("_NEWMEMBERINFO", "Userinfo");
define("_SUBMIT", "Submit");
define("_GONEXT", "next");
define("_GOPREV", "previous");
define("_USERSADMINS", "Administrators");
define("_USERSGROUPS", "User groups");
define("_USERSMEMBERS", "Registered&nbsp;members"); // angemeldete Benutzer
define("_USERSOTHERS", "All others");
define("_FILES", "Files");
define("_ACCOUNTACTIVATIONLINK", "Account Activation Link");
define("_YSACCOUNT", "Account");
define("_NEWSSHORT", "News");
define("_RESETPMXCACHE", "Reset Cache");
define("_MSGDEBUGMODE", "Debug-Mode enabled!");
define("_ATTENTION", "Attention");
define("_SETUPWARNING1", "Please rename or delete your setup-folder!");
define("_SETUPWARNING2", "To rename the file 'setup/index.php', please <a href='index.php?%s'>click here</a>.");
define("_AB_EVENT", "new event");
define("_EXPAND2COLLAPSE_TITLE", "open or close");
define("_EXPAND2COLLAPSE_TITLE_E", "open");
define("_EXPAND2COLLAPSE_TITLE_C", "close");
define("_TEXTQUOTE", "Quote");
define('_BBBOLD', 'Bold');
define('_BBITALIC', 'Italic');
define('_BBUNDERLINE', 'Underline');
define('_BBXCODE', 'Code');
define('_BBEMAIL', 'Email');
define('_BBQUOTE', 'Insert a quote');
define('_BBURL', 'Insert a link');
define('_BBIMG', 'Image');
define('_BBLIST', 'list');
define('_BBLINE', 'line');
define('_BBNUMLIST', 'numbered list');
define('_BBCHARLIST', 'abc list');
define('_BBCENTER', 'center');
define('_BBXPHPCODE', 'PHP Code');
define("_ALLOWEDHTML", "Allowed HTML:");
define("_EXTRANS", "Extrans (html tags to text)");
define("_HTMLFORMATED", "HTML Formated");
define("_PLAINTEXT", "Plain Old Text");
define("_OK", "Ok!");
define("_SAVE", "Save");
define("_FORMCANCEL", "Cancel Send");
define("_FORMRESET", "Clear");
define("_FORMSUBMIT", "Submit");
define("_PREVIEW", "Preview");
define("_NEWUSER", "New User");
define("_PRINTER", "Printer Friendly Page");
define("_FRIEND", "Send to a Friend");
define("_YOURNAME", "Your Name");
define("_HITS", "Hits");
define("_LANGUAGE", "Language");
define("_SCORE", "Score");
define("_NOSUBJECT", "No Subject");
define("_SUBJECT", "Subject");
define("_LANGDANISH", "danish");
define("_LANGENGLISH", "english");
define("_LANGFRENCH", "french");
define("_LANGGERMAN", "german");
define("_LANGSPANISH", "spanish");
define("_LANGTURKISH", "turkish");
define("_LANGUAGES", "available languages");
define("_PREFEREDLANG", "prefered language");
define("_LEGAL", "Conditions of use");
// page
define("_PAGE", "Page");
define("_PAGES", "pages");
define("_OFPAGES", "of");
define("_PAGEOFPAGES", "Page %d of %d");
define("_GOTOPAGEPREVIOUS", 'previous page');
define("_GOTOPAGENEXT", 'next page');
define("_GOTOPAGE", "to page");
define("_GOTOPAGEFIRST", "to the first page");
define("_GOTOPAGELAST", "to the last page");
define("_BLK_NOYETCONTENT", "No yet content");
define("_BLK_ADMINLINK", "Administration module");
define("_BLK_MODULENOTACTIVE", "Module '<i>%s</i>' for this bloc is not active !");
define("_MODULEFILENOTFOUND", "Sorry, the file you requested doesn\'t exists!");
define("_DEBUG_DIE_1", "A error occured while processing this page.");
define("_DEBUG_DIE_2", "Please report the following error to the owner of this website.");
define("_DEBUG_INFO", "Debug Info");
define("_DEBUG_QUERIES", "sql-Queries");
define("_DEBUG_REQUEST", "Request");
define("_DEBUG_NOTICES", "Errors and Warnings");
define("_COMMENTSNOTIFY", "There is a new comment on \"%s\"."); // %s = sprintf $sitename
define("_REDIRECTMESS1", "One moment, you are passed on in %d seconds."); // %d = sprintf()
define("_REDIRECTMESS1A", "{One moment, you are passed on in }s{ seconds.}"); // {xx}s{xx} formated: http://eric.garside.name/docs.html?p=epiclock#ec-formatting-options
define("_REDIRECTMESS2", "Or click here if you do not want to wait!");
define("_REDIRECTMESS3", "please wait...");
define("_DEACTIVATE", "Deactivate");
define("_INACTIVE", "Inactive");
define("_ACTIVATE", "Activate");
define("_XMLERROROCCURED", "An XML error occurred in line");
// define("_ERRDEMOMODE", "Sorry, not in DemoMode!");
define("_JSSHOULDBEACTIVE", "Um diese Funktion zu nutzen, muss Javascript aktiviert sein.");
define("_CLICKFORFULLSIZE", "Click for full size picture...");
define("_REQUIRED", "(required)");
define("_SAVECHANGES", "Save Changes");
define("_MODULESSYSADMINS", "We are sorry, but this section of our site is for <i>System-Administrators only</i>");
define("_DATEREGISTERED", "Date Registered");
define("_RESET", "Reset");
define("_PAGEBREAK", "If you want multiple pages you can write <strong class=\"nowrap\">" . htmlspecialchars(PMX_PAGE_DELIMITER) . "</strong> where you want to cut.");
define("_READMORE", "Read More...");
define("_AND", "and");
define("_HELLO", "Hallo ");
define("_FUNCTIONS", "Functions");
define("_DAY", "Day");
define("_TITLE", "Title");
define("_FROM", "From");
define("_TO", "To");
define("_WEEK", "Week");
define("_WEEKS", "weeks");
define("_MONTH", "Month");
define("_MONTHS", "months");
define("_HELP", "Help");
define("_COPY", "Copy");
define("_CLONE", "Klonen");
define("_MOVE", "Move");
define("_DAYS", "days");
define("_IN", "in");
define("_DESCRIPTION", "Description");
define("_HOMEPAGE", "Homepage");
define("_TOPICNAME", "Topic Name");
define("_GOTOADMIN", "Go to Admin Section");
define("_SCROLLTOTHETOP", "To the top of the page");
define("_NOTIFYSUBJECT", "Notification");
define("_NOTIFYMESSAGE", "Hello, there are new entries on your site.");
define("_NOTITLE", "untitled");
define("_ALL", "All");
define("_NONE", "none");
define("_BROWSE", "browse");
define("_FILESECURERISK1", "MAJOR SECURITY RISK:");
define("_FILESECURERISK2", "You have not removed");
define("_CANCEL", "Cancel");
// Konstanten zur Passwortstärke
define("_PWD_STRENGTH", "Password strength:");
define("_PWD_TOOSHORT", "Too short");
define("_PWD_VERYWEAK", "Weak");
define("_PWD_WEAK", "Normal");
define("_PWD_GOOD", "Strong");
define("_PWD_STRONG", "Very Strong");

define("_LEGALPP", "Privacy Policy");


define("_MAILISBLOCKED", "This Emailadress (or Parts of it) is not allowed.");
/* since 2.2.5*/
define("_COOKIEINFO","By using our website, you agree to use cookies to enhance your experience. ");
define("_MOREINFO","more information");
?>