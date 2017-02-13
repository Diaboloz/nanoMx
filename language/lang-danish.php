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
 * danish language file, translated by:
 * Wilhelm Moellering
 */

defined('mxMainFileLoaded') or die('access denied');

/* Datestrings und locale Einstellungen: */
define("_CHARSET", "utf-8"); // Test:  äöüß
define("_LOCALE", "da_DK");
$old_setlocale = setlocale(LC_TIME, 0);
$locale = array('dk_DK.UTF-8', 'da_DK.UTF-8', 'dk_DK.UTF8', 'da_DK.UTF8', 'da_DK', 'da_DA', 'dk_DK', 'dan', 'danish', 'DK', 'DNK', '208', 'CTRY_DENMARK', 'da_DK.ISO-8859-15', 'dk_DK.ISO-8859-15');
define('_SETLOCALE', setlocale(LC_TIME, $locale));
setlocale(LC_TIME, $old_setlocale);
define('_SETTIMEZONE', 'Europe/Copenhagen');
define('_DECIMAL_SEPARATOR', ',');
define('_THOUSANDS_SEPARATOR', '.');
define('_SPECIALCHARS', 'ÆæØøÅå'); // hier alle dänischen Sonderzeichen eintragen
define("_SPECIALCHARS_ONLY", false); // Schrift besteht nur aus Nicht-ASCII Zeichen
define("_DOC_LANGUAGE", "da");
define("_DOC_DIRECTION", "ltr");
define("_DATESTRING", "%A, %d. %B %Y");
define("_DATESTRING2", "%A, %d. %B");
define("_XDATESTRING", "på %d.%m.%Y ved %H:%M");
define("_SHORTDATESTRING", "%d.%m.%Y");
define("_XDATESTRING2", "%A, %B %d");
define("_DATEPICKER", "%d-%m-%Y");
define("_TIMEFORMAT", "%H:%Mh");
define("_DATETIME_FORMAT","%d.%m.%Y %H:%M");
define("_SYS_INTERNATIONALDATES", 1); //0 = mm/dd/yyyy, 1 = dd/mm/yyyy
define("_SYS_TIME24HOUR", 1); // 1 = 24 hour time... 0 = AM/PM time
define("_SYS_WEEKBEGINN", 1); # the First Day in the Week: 0 = Sunday, 1 = Monday

define("_Z1", "Alle logoer og varemærker på denne side tilhører deres respektive ejere og indehavere.<br />Derudover gælder Fraskrivelse af ansvar. Flere detaljer kan findes på <a href=\"modules.php?name=Impressum\">Impressum</a>.");
define("_Z2", "Artiklerne er ejendom af deres respektive forfattere,<br />alle andre © by <a href=\"" . PMX_HOME_URL . "\">" . $GLOBALS['sitename'] . "</a>");
define("_Z3", "Denne hjemmeside er baseret på pragmaMx " . PMX_VERSION . ".");
define("_Z4", "Indholdet af denne hjemmeside er som <a href=\"modules.php?name=rss\">RSS/RDF-Quelle</a> tilgængelig.");
define("_YES", "Ja");
define("_NO", "Nej");
define("_EMAIL", "email");
define("_SEND", "send");
define("_SEARCH", "Søg");
define("_LOGIN", "Logind");
define("_WRITES", "skriver");
define("_POSTEDON", "Skrevet");
define("_NICKNAME", "Brugernavn");
define("_PASSWORD", "Kodeord");
define("_WELCOMETO", "Velkommen på");
define("_EDIT", "ændre");
define("_DELETE", "slet");
define("_POSTEDBY", "publiceret af");
define("_GOBACK", "[&nbsp;<a href=\"javascript:history.go(-1)\">Tilbage</a>&nbsp;]");
define("_COMMENTS", "kommentarer");
define("_BY", "af");
define("_ON", "den");
define("_LOGOUT", "afmelde");
define("_HREADMORE", "mere...");
define("_YOUAREANON", "Du er anonym bruger. Du kan tilmelde <a href=\"modules.php?name=Your_Account\">dig her.</a>");
define("_NOTE", "note:");
define("_ADMIN", "administrator:");
define("_TOPIC", "tema");
define("_MVIEWADMIN", "Synlig for: kun administrator");
define("_MVIEWUSERS", "Synlig for: kun registrerede bruger");
define("_MVIEWANON", "Synlig for: kun anonyme besøgende");
define("_MVIEWALL", "Synlig for: alle besøgende");
define("_EXPIRELESSHOUR", "Forfald: mindre end 1 time");
define("_EXPIREIN", "Forfalder om");
define("_UNLIMITED", "ubegrenset");
define("_HOURS", "timer");
define("_RSSPROBLEM", "RSS/RDF indhold af denne hjemmeside kan i øjeblikket ikke læses.");
define("_SELECTLANGUAGE", "Vælg et sprog");
define("_SELECTGUILANG", "Vælg et sprog.");
define("_BLOCKPROBLEM", "Der er et problem med denne blok.");
define("_BLOCKPROBLEM2", "Denne blok har for tiden ingen indhold.");
define("_MODULENOTACTIVE", "Denne modul er ikke aktiveret!");
define("_NOACTIVEMODULES", "inaktiv modul");
define("_NOVIEWEDMODULES", "gemte moduler");
define("_FORADMINTESTS", "(til test for admin)");
define("_ACCESSDENIED", "adgang ikke tilladt");
define("_RESTRICTEDAREA", "Ingen adgang! Beskyttet område.");
define("_MODULEUSERS", "Desværre, men dette område er forbeholdt til <i>registrerede bruger</i>.<br /><br />Du kan registrere dig gratis, <a href=\"modules.php?name=User_Registration\">klik her</a>,<br /> og du få ubegrænset adgang til dette område.<br /> Tak");
define("_MODULESADMINS", "Desværre, men dette område er forbeholdt til <i>administratorer</i>.");
define("_HOME", "Home");
define("_HOMEPROBLEM", "Der er et problem, ingen startside konfigureret!"); # translate
define("_ADDAHOME", "Instil en modul som startside."); # translate
define("_HOMEPROBLEMUSER", "Vi har for tiden problemer med vores startside. Prøv venligst igen senere."); # translate
define("_DATE", "Dato");
define("_HOUR", "Time");
define("_UMONTH", "Måned");
define("_YEAR", "År");
define("_YEARS", "år");
define("_JANUARY", "januar");
define("_FEBRUARY", "februar");
define("_MARCH", "marts");
define("_APRIL", "april");
define("_MAY", "maj");
define("_JUNE", "juni");
define("_JULY", "juli");
define("_AUGUST", "august");
define("_SEPTEMBER", "september");
define("_OCTOBER", "oktober");
define("_NOVEMBER", "november");
define("_DECEMBER", "december");
define("_WEEKFIRSTDAY", "søndag");
define("_WEEKSECONDDAY", "mandag");
define("_WEEKTHIRDDAY", "tirsdag");
define("_WEEKFOURTHDAY", "onsdag");
define("_WEEKFIFTHDAY", "torsdag");
define("_WEEKSIXTHDAY", "fredag");
define("_WEEKSEVENTHDAY", "lørdag");
define("_MAIN", "start");
define("_TERMS", "betegnelser");
define("_TOP", "op ad");
define("_SITECHANGE", "opad til nummer:");
define("_BANNED", "Du blev blokeret fra administratoren!<br /><br />Vil du vide mere om det, kontakt administratoren.");
define("_VKPBENCH1", "Siden blev lavet på ");
define("_VKPBENCH2", " sekunder, ved ");
define("_VKPBENCH3", " database forespørgelser");
define("_ERRNOTOPIC", "Du skal vælge en tema.");
define("_ERRNOTITLE", "Du skal skrive en emne til denne artikel.");
define("_ERRNOTEXT", "Du skal skrive tekst.");
define("_ERRNOSAVED", "Sorry, dine data kun ikke gemmes.");
define("_RETURNACCOUNT", "Tilbage til siden 'Min konto'");
define("_FORADMINGROUPS", "(group can not see)");
define("_GROUPRESTRICTEDAREA", "Sorry, du har ikke adgang til denne del af siden.");
define("_NOGROUPMODULES", "Non-Group Modules");
define("_AB_LOGOUT", "logaf");
define("_AB_SETTINGS", "Indstillinger");
define("_AB_MESSAGE", "Adminmeddelelse");
define("_AB_WEBRING", "nye webringe");
define("_AB_TITLEBAR", "Admin Menu");
define("_AB_NOWAITINGCONT", "Intet nyt indhold");
define("_AB_RESETBCACHE", "Reset Blockcache");
define("_ERR_YOUBAD", "Du har prøvet at gennemføre en ugyldig operation!");
define("_REMEMBERLOGIN", "Husk tilmeldelsen");
define("_ADMINMENUEBL", "Administration");
define("_MXSITEBASEDON", "Dette website baserer på");
define("_WEBMAIL", "send email");
define("_CONTRIBUTEDBY", "Forfattet af");
define("_BBFORUMS", "Foren");
define("_BLK_MINIMIZE", "minimier");
define("_BLK_MAXIMIZE", "vis alt");
define("_BLK_HIDE", "vis ikke");
define("_BLK_MESSAGE", "meddelelse");
define("_BLK_MYBLOCKS", "Konfiguration af blokke");
define("_BLK_EDITADMIN", "ændre (Admin)");
define("_BLK_OPTIONS", "Blokoptioner");
define("_BLK_OPTIONSCLICK", "Klik her, for at indstille blokoptionerne.");
define("_ADM_MESS_DATEEXPIRE", "dato");
define("_ADM_MESS_TIMES", "tidsrum");
define("_ADM_MESS_DATESTART", "startdato");
define("_ADM_MESS_TODAY", "i dag");
define("_DEFAULTGROUP", "Standardgruppe");
define("_YOURELOGGEDIN", 'Tak fordi du har logget dig ind');
define("_YOUARELOGGEDOUT", "Du er nu logget af.");
define('_CHANGESAREOK', 'Dine ændringer blev sikret.');
define('_CHANGESNOTOK', 'Dine ændringer kunne ikke sikres.');
define('_DELETEAREOK', 'Dine data blev slettet.');
define('_DELETENOTOK', 'Dine data kunne ikke slettes.');
define("_RETYPEPASSWD", "Gentag kodeord");
define('_USERNAMENOTALLOWED', 'Dette brugernavn &quot;%s&quot; må du ikke vælge.'); // %s = sprintf()
define('_SYSINFOMODULES', 'Information om installerede moduler');
define('_SYSINFOTHEMES', 'Information om installerede designs');
define("_ACCOUNT", "Din konto");
define('_MAXIMALCHAR', 'maks.');
define("_SELECTPART", "udvalg");
define("_CAPTCHAWRONG", "Forkert Captchaværdi");
define("_CAPTCHARELOAD", "nyt kontrolbillede");
define("_CAPTCHAINSERT", "Skriv kontrolværdien:");
define("_ERROROCCURS", "Der er følgende fejl:");
define("_VISIT", "besøge");
define("_NEWMEMBERON", "Ny bruger tilmelding ved");
define("_NEWMEMBERINFO", "brugerinfo");
define("_SUBMIT", "Sende");
define("_GONEXT", "næste");
define("_GOPREV", "forrige");
define("_USERSADMINS", "Administratorer");
define("_USERSGROUPS", "Brugergrupper");
define("_USERSMEMBERS", "tilmeldte brugere"); // angemeldete Benutzer
define("_USERSOTHERS", "alle andre");
define("_FILES", "filer");
define("_ACCOUNTACTIVATIONLINK", "Brugerkonto aktiverings link");
define("_YSACCOUNT", "Konto");
define("_NEWSSHORT", "Nyheder");
define("_RESETPMXCACHE", "Tøm cachen");
define("_MSGDEBUGMODE", "Debug-Modus er slået til!");
define("_ATTENTION", "Bemærk");
define("_SETUPWARNING1", "Omdøb venligst mappen *setup*, eller slet den!");
define("_SETUPWARNING2", "For at mindst omdøbe filen *setup/index.php*, <a href='index.php?%s'>klik her</a>");
define("_AB_EVENT", "ny termin");
define("_EXPAND2COLLAPSE_TITLE", "Rul ind eller ud");
define("_EXPAND2COLLAPSE_TITLE_E", "Rul ud");
define("_EXPAND2COLLAPSE_TITLE_C", "Rul ind");
define("_TEXTQUOTE", "citerer");
define('_BBBOLD', 'fede');
define('_BBITALIC', 'kursiv');
define('_BBUNDERLINE', 'Understreg');
define('_BBXCODE', 'source kode for at indsætte feltet');
define('_BBEMAIL', 'Email Address Indsæt');
define('_BBQUOTE', 'Indsæt citat');
define('_BBURL', 'indsæt henvisning');
define('_BBIMG', 'indsæt billede');
define('_BBLIST', 'liste');
define('_BBLINE', 'separator');
define('_BBNUMLIST', 'nummereret liste');
define('_BBCHARLIST', 'Letter-liste');
define('_BBCENTER', 'Center');
define('_BBXPHPCODE', 'PHP kode for at indsætte feltet');
define("_ALLOWEDHTML", "tilladt HTML-kode:");
define("_EXTRANS", "skift HTML-kode til tekst");
define("_HTMLFORMATED", "HTML formateret");
define("_PLAINTEXT", "enkelt tekst");
define("_OK", "ok!");
define("_SAVE", "gem");
define("_FORMCANCEL", "afbryd overførelsen");
define("_FORMRESET", "set tilbage");
define("_FORMSUBMIT", "send");
define("_PREVIEW", "versigt");
define("_NEWUSER", "ny bruger");
define("_PRINTER", "print version");
define("_FRIEND", "send denne artikel til en ven");
define("_YOURNAME", "dit navn");
define("_HITS", "hints");
define("_LANGUAGE", "Sprog");
define("_SCORE", "points");
define("_NOSUBJECT", "ingen emne");
define("_SUBJECT", "emne");
define("_LANGDANISH", "dansk");
define("_LANGENGLISH", "engelsk");
define("_LANGFRENCH", "fransk");
define("_LANGGERMAN", "tysk");
define("_LANGSPANISH", "spansk");
define("_LANGTURKISH", "tyrkisk");
define("_LANGUAGES", "disponibele sprog");
define("_PREFEREDLANG", "foretrukne sprog");
define("_LEGAL", "Vilkår for brug");
// page
define("_PAGE", "side");
define("_PAGES", "sider");
define("_OFPAGES", "af");
define("_PAGEOFPAGES", "side %d af %d");
define("_GOTOPAGEPREVIOUS", 'Forrige Side');
define("_GOTOPAGENEXT", 'Næste side');
define("_GOTOPAGE", "til side");
define("_GOTOPAGEFIRST", "til første side");
define("_GOTOPAGELAST", "til sidste side");
define("_BLK_NOYETCONTENT", "Ingen endnu indhold");
define("_BLK_ADMINLINK", "Administration module");
define("_BLK_MODULENOTACTIVE", "Modul '<i>%s</i>' til denne blok er ikke aktiv!");
define("_MODULEFILENOTFOUND", "Sorry, filen findes ikke...");
define("_DEBUG_DIE_1", "Der er problemer med den side oprettelsen fandt sted.");
define("_DEBUG_DIE_2", "Giv følgende fejl med ejeren af grunden.");
define("_DEBUG_INFO", "Fejlfindings info");
define("_DEBUG_QUERIES", "sql-Queries");
define("_DEBUG_REQUEST", "Request");
define("_DEBUG_NOTICES", "Bem&#230;rk");
define("_COMMENTSNOTIFY", "Der er en ny kommentar om \"%s\"."); // %s = sprintf $sitename
define("_REDIRECTMESS1", "Et øjeblik du bliver i %d sekunder omdirigerede."); // %d = sprintf()
define("_REDIRECTMESS1A", "{Et øjeblik du bliver i }s{ sekunder omdirigerede.}"); // {xx}s{xx} formated: http://eric.garside.name/docs.html?p=epiclock#ec-formatting-options
define("_REDIRECTMESS2", "Eller klik her, hvis du ikke ønsker at vente.");
define("_REDIRECTMESS3", "Vent venligst...");
define("_DEACTIVATE", "Deaktivere");
define("_INACTIVE", "Inaktiv");
define("_ACTIVATE", "Aktivere");
define("_XMLERROROCCURED", "XML fejl på linje");
// define("_ERRDEMOMODE", "Desværre, ikke i demo tilstand!");
define("_JSSHOULDBEACTIVE", "JavaScript skal være aktiveret, hvis du vil bruge denne funktion,.");
define("_CLICKFORFULLSIZE", "Klik for billede i fuld størrelse...");
define("_REQUIRED", "(nødvendig)");
define("_SAVECHANGES", "GEM ÆNDRINGER");
define("_MODULESSYSADMINS", "Vi beklager, men denne sektion er reserveret for vores <i>System-Administratorer</i>");
define("_DATEREGISTERED", "Registereringsdato");
define("_RESET", "nulstil");
define("_PAGEBREAK", "Hvis du har brug for flere sider, tilføj <strong class=\"nowrap\">" . htmlspecialchars(PMX_PAGE_DELIMITER) . "</strong> som sideskift.");
define("_READMORE", "mere...");
define("_AND", "og");
define("_HELLO", "Hallo");
define("_FUNCTIONS", "Funktioner");
define("_DAY", "dag");
define("_TITLE", "Bloktitel");
define("_FROM", "fra");
define("_TO", "til");
define("_WEEK", "uge");
define("_WEEKS", "uger");
define("_MONTH", "måned");
define("_MONTHS", "måneder");
define("_HELP", "Hjælp");
define("_COPY", "Kopi");
define("_CLONE", "Klon");
define("_MOVE", "Flytte");
define("_DAYS", "dage");
define("_IN", "i");
define("_DESCRIPTION", "beskrivelse");
define("_HOMEPAGE", "hjemmeside");
define("_TOPICNAME", "temaets navn");
define("_GOTOADMIN", "gå til administrationsområde");
define("_SCROLLTOTHETOP", "Til toppen af ​​siden");
define("_NOTIFYSUBJECT", "Anmeldelse");
define("_NOTIFYMESSAGE", "Hej, er der nye indsendelser på dit websted.");
define("_NOTITLE", "uden titel");
define("_ALL", "alle");
define("_NONE", "ingen");


define("_BROWSE", "søg");
define("_FILESECURERISK1", "STOR SIKKERHED RISIKO:");
define("_FILESECURERISK2", "Du har følgende fil (er) bliver ikke slettet:");
define("_CANCEL", "Afbryd");
// Konstanten zur Passwortstärke
define("_PWD_STRENGTH", "Password styrke:");
define("_PWD_TOOSHORT", "for kort");
define("_PWD_VERYWEAK", "meget svag");
define("_PWD_WEAK", "svage");
define("_PWD_GOOD", "Good");
define("_PWD_STRONG", "Excellent");

define("_LEGALPP", "Erklæring om beskyttelse af personlige oplysninger (Privacy Policy)");

define("_MAILISBLOCKED", "Denne emailadresse (eller dele af den) er ikke tilladt.");
/* since 2.2.5*/
define("_COOKIEINFO","Ved at bruge vores hjemmeside, accepterer du at bruge cookies til at forbedre din oplevelse.");
define("_MOREINFO","mere information");
?>