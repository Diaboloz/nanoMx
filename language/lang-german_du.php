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
 * german language file by:
 * pragmaMx Developer Team
 * corrections by Joerg Fiedler, http://www.vatersein.de/
 */

defined('mxMainFileLoaded') or die('access denied');

/* Datestrings und locale Einstellungen: */
define("_CHARSET", "utf-8"); // Test:  äöüß
define("_LOCALE", "de_DE");
$old_setlocale = setlocale(LC_TIME, 0);
$locale = array("de_DE.UTF-8", "de_DE.UTF8", "de_DE", "ge", "deu", "german", "DE", "DEU", "276", "CTRY_GERMANY", "de_DE.ISO-8859-15");
define("_SETLOCALE", setlocale(LC_TIME, $locale));
setlocale(LC_TIME, $old_setlocale);
define("_SETTIMEZONE", "Europe/Berlin");
define("_DECIMAL_SEPARATOR", ",");
define("_THOUSANDS_SEPARATOR", ".");
define("_SPECIALCHARS", "äÄöÖüÜß");
define("_SPECIALCHARS_ONLY", false); // Schrift besteht nur aus Nicht-ASCII Zeichen
define("_DOC_LANGUAGE", "de");
define("_DOC_DIRECTION", "ltr");
define("_DATESTRING", "%A, %d. %B %Y");
define("_DATESTRING2", "%A, %d. %B");
define("_XDATESTRING", "am %d.%m.%Y um %H:%M");
define("_SHORTDATESTRING", "%d.%m.%Y");
define("_XDATESTRING2", "%A, %B %d");
define("_DATEPICKER", _SHORTDATESTRING);
define("_TIMEFORMAT", "%H:%Mh");
define("_DATETIME_FORMAT","%d.%m.%Y %H:%M");
define("_SYS_INTERNATIONALDATES", 1); //0 = mm/dd/yyyy, 1 = dd/mm/yyyy
define("_SYS_TIME24HOUR", 1); // 1 = 24 hour time... 0 = AM/PM time
define("_SYS_WEEKBEGINN", 1); # the First Day in the Week: 0 = Sunday, 1 = Monday
define("_Z1", "Alle Logos und Warenzeichen auf dieser Seite sind Eigentum der jeweiligen Besitzer und Lizenzhalter.<br />Im übrigen gilt Haftungsausschluss. Weitere Details findest Du im <a href=\"modules.php?name=Impressum\">Impressum</a>.");
define("_Z2", "Die Artikel sind geistiges Eigentum des/der jeweiligen Autoren,<br />alles andere © by <a href=\"" . PMX_HOME_URL . "\">" . $GLOBALS['sitename'] . "</a>");
define("_Z3", "Diese Webseite basiert auf pragmaMx " . PMX_VERSION . ".");
define("_Z4", "Die Inhalte dieser Seite sind als <a href=\"modules.php?name=rss\">RSS/RDF-Quelle</a> verfügbar.");
define("_YES", "Ja");
define("_NO", "Nein");
define("_EMAIL", "E-Mail");
define("_SEND", "Senden");
define("_SEARCH", "Suchen");
define("_LOGIN", "Login");
define("_WRITES", "schreibt");
define("_POSTEDON", "Geschrieben am");
define("_NICKNAME", "Benutzername");
define("_PASSWORD", "Passwort");
define("_WELCOMETO", "Willkommen bei");
define("_EDIT", "Ändern");
define("_DELETE", "Löschen");
define("_POSTEDBY", "Veröffentlicht von");
define("_GOBACK", "[&nbsp;<a href=\"javascript:history.go(-1)\">Zurück</a>&nbsp;]");
define("_COMMENTS", "Kommentare");
define("_BY", "von");
define("_ON", "am");
define("_LOGOUT", "ausloggen");
define("_HREADMORE", "mehr...");
define("_YOUAREANON", "Du bist ein anonymer Benutzer. Du kannst Dich <a href=\"modules.php?name=Your_Account\">hier anmelden.</a>");
define("_NOTE", "Notiz:");
define("_ADMIN", "Administrator:");
define("_TOPIC", "Thema");
define("_MVIEWADMIN", "Ansicht: Nur Administratoren");
define("_MVIEWUSERS", "Ansicht: Nur angemeldete Benutzer");
define("_MVIEWANON", "Ansicht: Nur anonyme Benutzer");
define("_MVIEWALL", "Ansicht: Alle Besucher");
define("_EXPIRELESSHOUR", "Verfall: weniger als 1 Stunde");
define("_EXPIREIN", "Verfällt in");
define("_UNLIMITED", "Unbegrenzt");
define("_HOURS", "Stunden");
define("_RSSPROBLEM", "Die RSS/RDF Inhalte dieser Webseite können z.Zt. nicht gelesen werden.");
define("_SELECTLANGUAGE", "Sprache wählen");
define("_SELECTGUILANG", "Sprache für das Interface auswählen");
define("_BLOCKPROBLEM", "Es besteht ein Problem mit diesem Block.");
define("_BLOCKPROBLEM2", "Dieser Block hat derzeit keinen Inhalt.");
define("_MODULENOTACTIVE", "Dieses Modul ist nicht aktiv!");
define("_NOACTIVEMODULES", "Inaktive Module");
define("_NOVIEWEDMODULES", "versteckte Module");
define("_FORADMINTESTS", "(für Admin zum testen)");
define("_ACCESSDENIED", "Zugriff verweigert");
define("_RESTRICTEDAREA", "Du bist im Begriff einen geschützten Bereich zu betreten.");
define("_MODULEUSERS", "Es tut uns leid, aber dieser Bereich ist nur unseren <i>Registrierten Benutzern</i> zugänglich.<br /><br />Du kannst Dich kostenfrei registrieren, indem Du <a href=\"modules.php?name=User_Registration\">hier</a> klickst,<br /> anschliessend hast Du uneingeschränkten Zugriff auf diesen Bereich.<br /> Danke.");
define("_MODULESADMINS", "Es tut uns leid, aber dieser Bereich ist nur unseren <i>Administratoren</i> vorbehalten");
define("_HOME", "Home");
define("_HOMEPROBLEM", "Es besteht ein Problem, es wurde keine Startseite konfiguriert!");
define("_ADDAHOME", "Stelle Dir ein Modul als Startseite ein.");
define("_HOMEPROBLEMUSER", "Wir haben zur Zeit Probleme mit unserer Startseite. Bitte versuche es später nochmal.");
define("_DATE", "Datum");
define("_HOUR", "Stunde");
define("_UMONTH", "Monat");
define("_YEAR", "Jahr");
define("_YEARS", "Jahre");
define("_JANUARY", "Januar");
define("_FEBRUARY", "Februar");
define("_MARCH", "März");
define("_APRIL", "April");
define("_MAY", "Mai");
define("_JUNE", "Juni");
define("_JULY", "Juli");
define("_AUGUST", "August");
define("_SEPTEMBER", "September");
define("_OCTOBER", "Oktober");
define("_NOVEMBER", "November");
define("_DECEMBER", "Dezember");
define("_WEEKFIRSTDAY", "Sonntag");
define("_WEEKSECONDDAY", "Montag");
define("_WEEKTHIRDDAY", "Dienstag");
define("_WEEKFOURTHDAY", "Mittwoch");
define("_WEEKFIFTHDAY", "Donnerstag");
define("_WEEKSIXTHDAY", "Freitag");
define("_WEEKSEVENTHDAY", "Samstag");
define("_MAIN", "Start");
define("_TERMS", "Bezeichnungen");
define("_TOP", "nach oben");
define("_SITECHANGE", "Nach oben zur Nummer:");
define("_BANNED", "Du wurdest vom AdminTeam gesperrt!<br /><br />Wenn Du Genaueres wissen möchtest, setze Dich bitte, mit dem Team in Verbindung.");
define("_VKPBENCH1", "Seitenerstellung in ");
define("_VKPBENCH2", " Sekunden, mit ");
define("_VKPBENCH3", " Datenbank-Abfragen");
define("_ERRNOTOPIC", "Du mußt ein Thema auswählen.");
define("_ERRNOTITLE", "Du mußt einen Titel, für den Artikel, angeben.");
define("_ERRNOTEXT", "Du mußt einen Text schreiben.");
define("_ERRNOSAVED", "Sorry, die Daten konnten nicht gespeichert werden.");
define("_RETURNACCOUNT", "Zurück zu der Seite 'Dein Account'");
define("_FORADMINGROUPS", "(group can not see)");
define("_GROUPRESTRICTEDAREA", "Sorry, Du hast keinen Zugriff auf diesen Teil der Website.");
define("_NOGROUPMODULES", "Non-Group Modules");
define("_AB_LOGOUT", "ausloggen");
define("_AB_SETTINGS", "Einstellungen");
define("_AB_MESSAGE", "Mitteilungen");
define("_AB_TITLEBAR", "Admin Menü");
define("_AB_NOWAITINGCONT", "keine neuen Inhalte");
define("_AB_RESETBCACHE", "Reset Blockcache");
define("_ERR_YOUBAD", "Du hast versucht, eine ungültige Operation durchzuführen!");
define("_REMEMBERLOGIN", "Anmeldung merken");
define("_ADMINMENUEBL", "Administration");
define("_MXSITEBASEDON", "Diese Webseite basiert auf");
define("_WEBMAIL", "E-Mail senden");
define("_CONTRIBUTEDBY", "Erstellt von");
define("_BBFORUMS", "Foren");
define("_BLK_MINIMIZE", "minimieren");
define("_BLK_MAXIMIZE", "komplett anzeigen");
define("_BLK_HIDE", "ausblenden");
define("_BLK_MESSAGE", "Mitteilung");
define("_BLK_MYBLOCKS", "Blöcke konfigurieren");
define("_BLK_EDITADMIN", "ändern (Admin)");
define("_BLK_OPTIONS", "Blockoptionen");
define("_BLK_OPTIONSCLICK", "Hier anklicken, um Blockoptionen einzustellen.");
define("_ADM_MESS_DATEEXPIRE", "Datum");
define("_ADM_MESS_TIMES", "Zeitraum");
define("_ADM_MESS_DATESTART", "Start-Datum");
define("_ADM_MESS_TODAY", "Heute");
define("_DEFAULTGROUP", "Standardgruppe");
define("_YOURELOGGEDIN", 'Danke, dass Du Dich einloggst');
define("_YOUARELOGGEDOUT", "Du bist jetzt abgemeldet.");
define('_CHANGESAREOK', 'Die Änderungen wurden gespeichert.');
define('_CHANGESNOTOK', 'Die Änderungen konnten nicht gespeichert werden.');
define('_DELETEAREOK', 'Die Daten wurden gelöscht.');
define('_DELETENOTOK', 'Die Daten konnten nicht gelöscht werden.');
define("_RETYPEPASSWD", "Passwort wiederholen");
define('_USERNAMENOTALLOWED', 'Den Benutzernamen &quot;%s&quot; darfst Du nicht verwenden.'); // %s = sprintf()
define('_SYSINFOMODULES', 'Information über die installierten Module');
define('_SYSINFOTHEMES', 'Information über die installierten Designs');
define("_ACCOUNT", "Dein Account");
define('_MAXIMALCHAR', 'max.');
define("_SELECTPART", "Auswahl");
define("_CAPTCHAWRONG", "Kontrollwert nicht eingegeben oder ungültig!");
define("_CAPTCHARELOAD", "Kontrollbild neu laden");
define("_CAPTCHAINSERT", "Kontrollwert aus dem angezeigten Bild eingeben:");
define("_ERROROCCURS", "Es sind folgende Fehler aufgetreten:");
define("_VISIT", "Besuche");
define("_NEWMEMBERON", "Neue Benutzer Anmeldung bei");
define("_NEWMEMBERINFO", "Benutzerinfo");
define("_SUBMIT", "Abschicken");
define("_GONEXT", "nächste");
define("_GOPREV", "vorige");
define("_USERSADMINS", "Administratoren");
define("_USERSGROUPS", "Benutzergruppen");
define("_USERSMEMBERS", "angem.&nbsp;Benutzer"); // angemeldete Benutzer
define("_USERSOTHERS", "alle Anderen");
define("_FILES", "Dateien");
define("_ACCOUNTACTIVATIONLINK", "Benutzeraccount Aktivierungs Link");
define("_YSACCOUNT", "Account");
define("_NEWSSHORT", "News");
define("_RESETPMXCACHE", "Cache zurücksetzen");
define("_MSGDEBUGMODE", "Debug-Modus ist eingeschaltet!");
define("_ATTENTION", "Achtung");
define("_SETUPWARNING1", "Bitte benenne den Setup-Ordner um, oder lösche ihn!");
define("_SETUPWARNING2", "Um zumindest die setup/index.php umzubenennen, bitte <a href='index.php?%s'>hier klicken</a>");
define("_AB_EVENT", "neuer Termin");
define("_EXPAND2COLLAPSE_TITLE", "Ein- oder Ausklappen");
define("_EXPAND2COLLAPSE_TITLE_E", "Ausklappen");
define("_EXPAND2COLLAPSE_TITLE_C", "Einklappen");
define("_TEXTQUOTE", "Zitat");
define('_BBBOLD', 'Fettschrift');
define('_BBITALIC', 'Kursivschrift');
define('_BBUNDERLINE', 'Unterstrichen');
define('_BBXCODE', 'Quelltextbereich einfügen');
define('_BBEMAIL', 'Emailadresse einfügen');
define('_BBQUOTE', 'Zitat einfügen');
define('_BBURL', 'Verweis einfügen');
define('_BBIMG', 'Bild einfügen');
define('_BBLIST', 'Liste');
define('_BBLINE', 'Trennlinie');
define('_BBNUMLIST', 'nummerierte Liste');
define('_BBCHARLIST', 'Buchstabenliste');
define('_BBCENTER', 'Zentriert');
define('_BBXPHPCODE', 'PHP Codebereich einfügen');
define("_ALLOWEDHTML", "erlaubter HTML Code:");
define("_EXTRANS", "HTML Code zu Text umwandeln");
define("_HTMLFORMATED", "HTML formatiert");
define("_PLAINTEXT", "einfacher Text");
define("_OK", "Ok!");
define("_SAVE", "Speichern");
define("_FORMCANCEL", "Senden abbrechen");
define("_FORMRESET", "Zurücksetzen");
define("_FORMSUBMIT", "Abschicken");
define("_PREVIEW", "Vorschau");
define("_NEWUSER", "neuer Benutzer");
define("_PRINTER", "Druckbare Version");
define("_FRIEND", "Diesen Artikel an einen Freund senden");
define("_YOURNAME", "Dein Name");
define("_HITS", "Hits");
define("_LANGUAGE", "Sprache");
define("_SCORE", "Punkte");
define("_NOSUBJECT", "Kein Betreff");
define("_SUBJECT", "Betreff");
define("_LANGDANISH", "dänisch");
define("_LANGENGLISH", "englisch");
define("_LANGFRENCH", "französisch");
define("_LANGGERMAN", "deutsch");
define("_LANGSPANISH", "spanisch");
define("_LANGTURKISH", "türkisch");
define("_LANGUAGES", "verfügbare Sprachen");
define("_PREFEREDLANG", "bevorzugte Sprache");
define("_LEGAL", "Allgemeine Nutzungsbedingungen");
// page
define("_PAGE", "Seite");
define("_PAGES", "Seiten");
define("_OFPAGES", "von");
define("_PAGEOFPAGES", "Seite %d von %d");
define("_GOTOPAGEPREVIOUS", 'Vorherige Seite');
define("_GOTOPAGENEXT", 'Nächste Seite');
define("_GOTOPAGE", "zu Seite");
define("_GOTOPAGEFIRST", "zur ersten Seite");
define("_GOTOPAGELAST", "zur letzten Seite");
define("_BLK_NOYETCONTENT", "Noch keine Inhalte");
define("_BLK_ADMINLINK", "Administrations Modul");
define("_BLK_MODULENOTACTIVE", "Modul '<i>%s</i>' für diesen Block ist nicht aktiv!");
define("_MODULEFILENOTFOUND", "Sorry, die Datei gibt es nicht...");
define("_DEBUG_DIE_1", "Es sind Probleme bei der Seitenerstellung aufgetreten.");
define("_DEBUG_DIE_2", "Bitte teilen Sie den folgenden Fehler dem Besitzer der Webseite mit.");
define("_DEBUG_INFO", "Debug-Info");
define("_DEBUG_QUERIES", "sql-Anfragen");
define("_DEBUG_REQUEST", "Übergabeparameter (Request)");
define("_DEBUG_NOTICES", "Fehler und Warnungen");
define("_COMMENTSNOTIFY", "Es gibt einen neuen Kommentar auf \"%s\"."); // %s = sprintf $sitename
define("_REDIRECTMESS1", "Einen Moment, Du wirst in %d Sekunden weitergeleitet."); // %d = sprintf()
define("_REDIRECTMESS1A", "{Einen Moment, Du wirst  }s{ Sekunden weitergeleitet.}"); // {xx}s{xx} formated: http://eric.garside.name/docs.html?p=epiclock#ec-formatting-options
define("_REDIRECTMESS2", "Oder klicke hier, wenn Du nicht warten möchtest.");
define("_REDIRECTMESS3", "Bitte warten...");
define("_DEACTIVATE", "Deaktivieren");
define("_INACTIVE", "Inaktiv");
define("_ACTIVATE", "Aktivieren");
define("_XMLERROROCCURED", "XML Fehler in Zeile");
// define("_ERRDEMOMODE", "Sorry, not in DemoMode!");
define("_JSSHOULDBEACTIVE", "Um diese Funktion zu nutzen, muss Javascript aktiviert sein.");
define("_CLICKFORFULLSIZE", "Klicken für Bild in voller Größe...");
define("_REQUIRED", "(erforderlich)");
define("_SAVECHANGES", "Änderungen speichern");
define("_MODULESSYSADMINS", "Es tut uns leid, aber dieser Bereich ist nur unseren <i>System-Administratoren</i> vorbehalten");
define("_DATEREGISTERED", "Registrierungsdatum");
define("_RESET", "Zurücksetzen");
define("_PAGEBREAK", "Wenn Du mehrere Seiten möchtest, kann mit <strong class=\"nowrap\">" . htmlspecialchars(PMX_PAGE_DELIMITER) . "</strong> ein Seitenumbruch eingefügt werden.");
define("_READMORE", "mehr...");
define("_AND", "und");
define("_HELLO", "Hallo ");
define("_FUNCTIONS", "Funktionen");
define("_DAY", "Tag");
define("_TITLE", "Titel");
define("_FROM", "Von");
define("_TO", "zu");
define("_WEEK", "KW");
define("_WEEKS", "Wochen");
define("_MONTH", "Monat");
define("_MONTHS", "Monate");
define("_HELP", "Hilfe");
define("_COPY", "Kopieren");
define("_CLONE", "Klonen");
define("_MOVE", "Verschieben");
define("_DAYS", "Tagen");
define("_IN", "in");
define("_DESCRIPTION", "Beschreibung");
define("_HOMEPAGE", "Homepage");
define("_TOPICNAME", "Name für das Thema");
define("_GOTOADMIN", "Gehe zum Administrationsbereich");
define("_SCROLLTOTHETOP", "Zum Seitenanfang scrollen");
define("_NOTIFYSUBJECT", "Benachrichtigung");
define("_NOTIFYMESSAGE", "Hallo, es gibt neue Einsendungen auf Deiner Seite.");
define("_NOTITLE", "ohne Titel");
define("_ALL", "Alle");
define("_NONE", "keine");
define("_BROWSE", "durchsuchen");
define("_FILESECURERISK1", "GROSSES SICHERHEITSRISIKO:");
define("_FILESECURERISK2", "Du hast folgende Datei(en) nicht gelöscht:");
define("_CANCEL", "Abbrechen");
// Konstanten zur Passwortstärke
define("_PWD_STRENGTH", "Passwortstärke:");
define("_PWD_TOOSHORT", "Zu kurz");
define("_PWD_VERYWEAK", "Sehr schwach");
define("_PWD_WEAK", "Schwach");
define("_PWD_GOOD", "Gut");
define("_PWD_STRONG", "Ausgezeichnet");

define("_LEGALPP", "Erklärung zum Datenschutz (Privacy Policy)");


define("_MAILISBLOCKED", "Diese E-Mail-Adresse (oder Teile davon) ist nicht zugelassen.");
/* since 2.2.5*/
define("_COOKIEINFO","Cookies erleichtern die Bereitstellung unserer Dienste. Mit der Nutzung unserer Dienste erklären Sie sich damit einverstanden, dass wir Cookies verwenden.");
define("_MOREINFO","mehr Informationen");
?>