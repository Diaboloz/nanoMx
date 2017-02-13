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
 * $Revision: 216 $
 * $Author: PragmaMx $
 * $Date: 2016-09-20 15:29:30 +0200 (Di, 20. Sep 2016) $
 */

defined('mxMainFileLoaded') or die('access denied');

define("_ACTBANNERS", "Banner aktivieren?");
define("_ACTMULTILINGUAL", "Aktiviere Multilinguale Features?");
define("_ACTUSEFLAGS", "Zeige Flaggen anstatt dropdown-Menü?");
define("_ADMINEMAIL", "Administrator- E-Mail");
define("_ADMIN_CANTCHANGE", "Wahrscheinlich konnte der Schreibschutz nicht aufgehoben werden.");
define("_ADMIN_FOOTCONSTMSG", "Sie können auch die, in den Sprachdateien definierten, Konstanten _Z1 bis _Z4 als Feldwerte verwenden:<br /><br /><strong>_Z1= <br /></strong>" . _Z1 . "<br /><strong>_Z2= <br /></strong>" . _Z2 . "<br /><strong>_Z3= </strong><br />" . _Z3 . "<br /><strong>_Z4= </strong><br />" . _Z4 . "");
define("_ADMIN_LASTCHANGE", "letzte Änderung am");
define("_ADMIN_NOREFS", "Um diese Funktion zu deaktivieren, '<strong>0</strong>' auswählen.");
define("_ADMIN_NUKECOMPATIBLE", "Kompatibilität zu phpNuke-Modulen deaktivieren?");
define("_ADMIN_NUKECOOKIE", "Cookies für phpNuke-Module erstellen?");
define("_ADMIN_POPTIME1", "alle");
define("_ALLOWANONPOST", "Dürfen Unangemeldete schreiben?");
define("_ANONYMOUSNAME", "Name für unangemeldete Benutzer");
// define("_BACKENDACTIVE", "Backend/RSS-Feed aktivieren");
// define("_BACKENDCONF", "Backend-/RSS-Feed- Einstellungen");
// define("_BACKENDITEMDESCRLEN", "max. angezeigte Länge der einzelnen Item-Beschreibungen");
// define("_BACKENDLANG", "Backendsprache");
// define("_BACKENDLIMIT1", "wieviele Punkte in den jeweiligen Feeds anzeigen");
// define("_BACKENDLOGOURL1", "Pfad zur Logo-Bilddatei");
// define("_BACKENDLOGOURL2", "ohne führenden Slash und ohne Domainangabe");
// define("_BACKENDOVERMSG", "die folgenden Einstellungen können durch URL-Parameter überschrieben werden");
// define("_BACKENDTITLE", "Backendbeschreibung");
define("_BLOCKSHOW_RIGHT", "Rechte Blöcke anzeigen");
define("_CENSORMODE", "Zensurmodus");
define("_CENSORMODEWORDS", "unerwünschte Worte");
define("_CENSOROPTIONS", "Zensuroption");
define("_CENSORREPLACE", "Ersetze zensierte Wörter durch");
define("_CENSORUSERNAMES", "nicht erlaubte Benutzernamen");
define("_CLCKHERE", "Bitte hier klicken");
define("_COMMENTSARTICLES", "Kommentare für News-Artikel aktivieren?");
define("_COMMENTSLIMIT", "Längenlimit in Bytes");
define("_COMMENTSOPT", "Kommentar- Einstellungen");
// define("_COMMENTSPOLLS", "Kommentare in Umfragen aktivieren?");
define("_COMMSCHOWONLY", "Nur Anzeigen (schreiben nur für Admins)");
define("_CUTWITHCOMMATA", "Die Worte mit Komma trennen!");
define("_DBNAME", "Name der Datenbank");
define("_DBPASS", "Passwort der Datenbank");
define("_DBSERVER", "Datenbank Server");
define("_DBSETTINGS", "Datenbank Einstellungen");
define("_DBUSERNAME", "Benutzer der Datenbank");
define("_DEACTHEMECACHE", "Design-Cache deaktivieren");
define("_DEBUG_ENHANCED", "erweiterte Debug-Informationen anzeigen");
define("_DEBUG_ERRORS", "Fehler und Warnungen");
define("_DEBUG_ERRORS_1", "ins Logfile schreiben (kritische Fehler werden immer geloggt)");
define("_DEBUG_ERRORS_2", "auf Bildschirm anzeigen; Achtung!! Evtl. Sicherheitsrisiko!");
define("_DEFAULTTHEME", "Standardtheme");
// define("_DEMOMODE", "Demomode");
// define("_DEMOMODEADMINS", "Administratoren für DemoMode");
define("_EMAIL2SENDMSG", "E-Mail an wen senden");
define("_EMAILFROM", "E-Mail von wem (Absender)");
define("_EMAILMSG", "E-Mail- Nachricht");
define("_EMAILSUBJECT", "Betreff der E-Mail");
define("_FOOTERLINE1", "Fußzeile 1");
define("_FOOTERLINE2", "Fußzeile 2");
define("_FOOTERLINE3", "Fußzeile 3");
define("_FOOTERLINE4", "Fußzeile 4");
define("_FOOTERMSG", "Fuß- Nachrichten");
define("_GENSITEINFO", "Generelle Seitenangaben");
define("_GRAPHICOPT", "Grafische Einstellungen");
define("_GZIPCOMPRESS", "Gzip-Seitenkompression benutzen");
define("_HTMLALLOWED", "HTML-Tag in den Übergabewerten untersagen oder zulassen?");
define("_HTMLOPT", "HTML Optionen");
define("_HTMLOPTRESET", "Alles ignorieren und HTML-Tags auf Systemstandard zurücksetzen.");
define("_HTMLTAGALLOWED", "Zulassen");
define("_HTMLTAGALLOWEDWITHPARAMS", "mit Parametern zulassen");
define("_HTMLTAGNAME", "HTML-Tag");
define("_HTMLTAGNOTALLOWED", "Untersagen");
define("_HTMLWARNING", "Die Zulassung der nachfolgenden HTML-Tags kann es Besuchern ermöglichen, Sicherheitslöcher auszunutzen. Deshalb wird empfohlen, diese HTML-Tags nicht zuzulassen, ausser die Sicherheitsrisiken sind abschätzbar.<br/>");
define("_INTRANETOPT", "Intranet-Umgebung");
define("_INTRANETWARNING", "Die Intranet Option sollte nur dann gewählt werden, wenn die Website nicht durch einen vollqualifizierten Internetnamen aufgerufen werden kann. Für eine Intranet-Anwendung werden eine ganze Reihe von Sicherheitsoptionen ausgeschaltet. Die Option ist nur dann zu empfehlen, wenn die Website sich hinter einer Firewall befindet oder der Computer nicht mit den Internet verbunden ist.");
define("_ITEMSTOP", "Zahl der Einträge im 'Top-Block'");
define("_JPCACHEUSE1A", "HTML-Seitencache aktivieren");
define("_JPCACHEUSE1B", "Funktioniert nur bei anonymen Besuchern.");
define("_JPCACHEUSE2A", "Cachezeit");
define("_JPCACHEUSE2B", "Sekunden");
// define("_KEYWORD_TXT", "Meta Text - Keywords");
define("_LOGGINWARNING", "Nach ändern dieser Option müssen Sie evtl. neu einloggen.");
define("_MAILAUTH", "Mailversand per");
define("_MAILAUTH_0", "PHP Funktion - mail()");
define("_MAILAUTH_1", "SMTP mit Anmeldung am Server");
define("_MAILHOST", "Mailserver");
define("_MAILHOST_2", "(smtp.domain.com / localhost)");
define("_MAILPASS", "Passwort");
define("_MAILPOP3AUTH", "&quot;SMTP-After-POP&quot; Hostname");
define("_MAILPORT", "SMTP Port");
define("_MAILPORT_2", "(Standard 25)");
define("_MAILSETTINGS", "SMTP - Mail-Server-Einstellungen");
define("_MAILUNAME", "Benutzername");
define("_MATCHANY", "Treffer für das Wort irgendwo im Text");
define("_MAXREF", "Maximal wie viele Referer-Angaben?");
define("_MISCOPT", "Sonstige Einstellungen");
// define("_MODREWRITE", "Suchmaschinenfreundliche URLs");
define("_MVADMIN", "Nur Administratoren");
define("_MVALL", "Alle Besucher");
define("_NEWSMODULE", "News-Modul Konfiguration");
define("_NOFILTERING", "keine Filterung");
define("_NOTIFYCOMMENT", "Über neue Kommentare informiert werden?");
define("_NOTIFYSUBMISSION", "Über neue Einsendungen per E-Mail informiert werden?");
define("_OLDSTORIES", "Zahl der Artikel im 'ältere Artikel-Block'");
define("_PREFIX", "Prefix der Tabellen");
// define("_PROMODREWADMIN", "für Administratoren");
// define("_PROMODREWANON", "für anonyme Benutzer (auch Suchmaschinen)");
// define("_PROMODREWERROR", "Hinweis");
// define("_PROMODREWERROR1", "Im mx-Root befindet sich keine .htaccess Datei.");
// define("_PROMODREWERROR2", "Die .htaccess Datei im mx-Root enthält nicht die, für mod_rewrite benötigten, Einträge.");
// define("_PROMODREWUSERS", "für angemeldete Benutzer");
define("_REQHTMLFILTER", "Nicht zugelassene HTML-Tag aus Übergabeparametern filtern");
define("_SAFECOOKIE1", "Sicherheitscookie für Benutzer verwenden?");
define("_SAFECOOKIE2", "Sicherheitscookie für Administratoren verwenden?");
define("_SECDAYS", "Tage");
define("_SECDAYS2", "Bei <strong>0</strong> müssen sich die Benutzer bei jedem Browserstart neu anmelden.");
define("_SECINACTIVELENGTH", "Aktualisierungsintervall für die &quot;wer ist Online&quot; Anzeige");
define("_SECMEDLENGTH", "Gültigkeit der Session und des Session-Cookies");
define("_SECMINUTES", "Minuten");
define("_SECOPT", "Sicherheitsoptionen");
define("_SECSQLINJECT1", "SQL-Injection Gefahr verringern?");
define("_SECSQLINJECT3", "Weitere Einstellmöglichkeiten hierzu, finden Sie in der Datei <i>includes/detection/config.php</i>.");
define("_SEC_LOGGING", "Secure Logging einschalten?");
define("_SELLANGUAGE", "Standardsprache");
define("_SETSERVICE", "Service- und Debug-Einstellungen");
define("_SITECONFIG", "Webseite konfigurieren");
define("_SITEDOCTYPE", "HTML-Darstellungsmodus (DOCTYPE)");
define("_SITELOGO", "Seitenlogo für Druckausgabe");
define("_SITENAME", "Name der Site");
define("_SITESERVICE", "Site-Service aktiv?");
define("_SITESERVICETEXT", "Site-Service Text");
define("_SITESLOGAN", "Seitenmotto");
define("_STARTDATE", "Seiten- Startdatum");
define("_STORIESHOME", "Zahl der Artikel auf der News-Seite");
define("_STORIESHOMECOLS", "Spaltenzahl auf der News-Seite");
define("_THEME4ADMIN", "Theme für Adminbereich");
define("_THEME4JQUI", "jQuery-UI <a href=\"http://jqueryui.com/themeroller/\" target=\"_blank\">Theme</a>");
define("_TIMEZONEDEFAULT", "Standard-Zeitzone");
define("_TRACK_ACTIVATEIT", "Website Tracking aktivieren");
define("_USEHTMLTIDY", "HTML-Ausgabefehler korrigieren");
// define("_USEMODREWRITE", "mod_rewrite Unterstützung aktivieren?");
// define("_USEMODREWRITEEXTEND", "individuelle / erweiterte mod_rewrite Regeln aktivieren?");
define("_USERPREFIX", "Prefix der User-Tabelle");
define("_HTMLDEFAULTS", "Alle HTML-Tags auf Standardeinstellung zurücksetzen.");
define("_REQHTMLFILTER_0", "nicht filtern");
define("_REQHTMLFILTER_1", "in lesbare Zeichen umsetzen");
define("_REQHTMLFILTER_2", "komplett entfernen");
define("_DBTYP", "Typ der Datenbank:");
define("_MODREWRITENOTE", "Für Suchmaschinenfreundliche URLs muß das Apache Modul &quot;mod_rewrite&quot; aktiv sein. Falls PHP im CGI-Modus läuft, sollte die Option 'cgi.fix_pathinfo = 0' in der php.ini aktiv sein, damit die Suchmaschinenfreundlichen URLs einwandfrei funktionieren.");
define("_DEACTHEMECACHEDES", "Verschiedene Design-Bereiche werden intern zwischengespeichert. Während der Designgestaltung sollte dieser Cache abgeschaltet sein, um die Änderungen sofort sichtbar zu machen.");
define("_BLOCKSHOW_0", "Modul definiert");
define("_BLOCKSHOW_1", "Immer");
define("_BLOCKSHOW_2", "Nie");
define("_BLOCKSHOW_3", "Nur auf Startseite");
define("_BLOCKSHOW_LEFT", "Linke Blöcke anzeigen");

define("_DB_TYPE","Datenbankanbindung");
define("_DB_TYPE_TEXT","Bitte Datenbankanbindung auswählen.");
define("_DATABASE","Datenbank");
define("_FILE","Datei");
define("_SESS_LOC","Speichern der Session in");

define("_COOKIECHOICE1","Cookiehinweis anzeigen?");
define("_COOKIECHOICE2","Link zur Datenschutzrichtlinie");
define("_COOKIECHOICE3","Cookiehinweis Position");
define("_CCTOP","oben");
define("_CCBOTTOM","unten");

/* FTP  since 2.3*/
define("_FTPOPT","Servereinstellungen");
define("_FTPOPT_TEXT","");
define("_FTP_ON","FTP aktivieren?");
define("_FTP_ON_TEXT","FTP aktivieren?");
define("_FTP_PORT","Port");
define("_FTP_HOST","Server");
define("_FTP_SSL","SSL Verbindung");
define("_FTP_DIR","Stammverzeichnis");

define("_THEME_INFO","Das Standardtheme kann <a href='admin.php?op=themes'>hier</a> eingestellt werden.");

?>