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

define("_ACTBANNERS", "aktiver banner på sitet?");
define("_ACTMULTILINGUAL", "Aktiver multilinguale features?");
define("_ACTUSEFLAGS", "vis flag istedet for dropdown menu?");
define("_ADMINEMAIL", "administrator-email");
define("_ADMIN_CANTCHANGE", "måske kunne skrivebeskyttelsen ikke ophæves.");
define("_ADMIN_FOOTCONSTMSG", "Du kan også tilføje i sprogfilene _Z1 til _Z4 bruge:<br /><br /><b>_Z1= <br /></b>" . _Z1 . "<br /><b>_Z2= <br /></b>" . _Z2 . "<br /><b>_Z3= </b><br />" . _Z3 . "<br /><b>_Z4= </b><br />" . _Z4 . "");
define("_ADMIN_LASTCHANGE", "Sidste ændringer");
define("_ADMIN_NOREFS", "for at deaktivere denne funkion, '<b>0</b>' vælg.");
define("_ADMIN_NUKECOMPATIBLE", "Deaktiver kompabilitet til phpNuke-Moduler?");
define("_ADMIN_NUKECOOKIE", "Set cookies for phpNuke-Moduler?");
define("_ADMIN_POPTIME1", "alle");
define("_ALLOWANONPOST", "Må ikke registrerede skrive?");
define("_ANONYMOUSNAME", "navn for ikke tilmeldte bruger");
// define("_BACKENDACTIVE", "aktiver Backend/RSS-Feed");
// define("_BACKENDCONF", "Backend-/RSS-Feed- indstillinger");
// define("_BACKENDITEMDESCRLEN", "maks. viste længde af enkelte Item-beskrivelser");
// define("_BACKENDLANG", "backendsprog");
// define("_BACKENDLIMIT1", "hvor mange points skal vises på Feeds");
// define("_BACKENDLOGOURL1", "URL til Logo-billedefil");
// define("_BACKENDLOGOURL2", "uden slash til sidst, og uden domæne");
// define("_BACKENDOVERMSG", "følgende indstillinger kan gennem URL-Parameter bliver overskrevet");
// define("_BACKENDTITLE", "backendnavn");
define("_BLOCKSHOW_RIGHT", "vis blokke til højre");
define("_CENSORMODE", "censurmodus");
define("_CENSORMODEWORDS", "ikke ønskede ord");
define("_CENSOROPTIONS", "Censuroption");
define("_CENSORREPLACE", "skifte censerede ord med");
define("_CENSORUSERNAMES", "ikke tilladt brugernavn");
define("_CLCKHERE", "klik venligst her");
define("_COMMENTSARTICLES", "Aktiver kommentarer for News artikler?");
define("_COMMENTSLIMIT", "længdelimit i Bytes");
define("_COMMENTSOPT", "Kommentar- indstillinger");
// define("_COMMENTSPOLLS", "aktiver komentarer i rundspørgelser?");
define("_COMMSCHOWONLY", "Kun visning (skrivning kun for admins)");
define("_CUTWITHCOMMATA", "Separer med et komma!");
define("_DBNAME", "database navn");
define("_DBPASS", "database kodeord");
define("_DBSERVER", "database server");
define("_DBSETTINGS", "Database indstillinger");
define("_DBUSERNAME", "database bruger");
define("_DEACTHEMECACHE", "Deaktiver design-cache");
define("_DEBUG_ENHANCED", "få vist avancerede Debug-Informationer");
define("_DEBUG_ERRORS", "Fejl og advarsler");
define("_DEBUG_ERRORS_1", "skriv i log fil (kritiske fejl logføres altid)");
define("_DEBUG_ERRORS_2", "visning på skærm; forsigtighed! Mulig sikkerhedsrisiko!");
define("_DEFAULTTHEME", "standardtema af sitet");
// define("_DEMOMODE", "Demomode");
// define("_DEMOMODEADMINS", "admins for DemoMode");
define("_EMAIL2SENDMSG", "email til");
define("_EMAILFROM", "email fra (afsender)");
define("_EMAILMSG", "email- meddelelse");
define("_EMAILSUBJECT", "email emne");
define("_FOOTERLINE1", "bundlinje 1");
define("_FOOTERLINE2", "bundlinje 2");
define("_FOOTERLINE3", "bundlinje 3");
define("_FOOTERLINE4", "bundlinje 4");
define("_FOOTERMSG", "Bottom- meddelelser");
define("_GENSITEINFO", "Grundlæggende siteinformationer");
define("_GRAPHICOPT", "Grafiske indstillinger");
define("_GZIPCOMPRESS", "brug Gzip-sidekompression");
define("_HTMLALLOWED", "tiladt HTML-kode eller ej?");
define("_HTMLOPT", "HTML optioner");
define("_HTMLOPTRESET", "Ignorer alt og set HTML koder tilbage til systemstandard.");
define("_HTMLTAGALLOWED", "tillade");
define("_HTMLTAGALLOWEDWITHPARAMS", "tillade med parmetre");
define("_HTMLTAGNAME", "HTML-Tag");
define("_HTMLTAGNOTALLOWED", "forbyde");
define("_HTMLWARNING", "Benyttelse af følgende HTML-koder kan gøre det muligt for brugere, at udnytte sikkerhedshuller. Vi anbefaler ikke at tillade disse HTML-koder.<br/>");
define("_INTRANETOPT", "intranet-omgivelse");
define("_INTRANETWARNING", "Intranet optionen skal kun vælges, hvis sitet ikke kan kaldes op af et internetnavn. Ved Intranet-anvendelse bliver der slukket for en række af sikkerhedsoptionen. Denne option kan kun anbefales, når websitet er beskyttet af en firewall eller computeren ikke har forbindelse til internettet.");
define("_ITEMSTOP", "antal anmærkninger i top blok");
define("_JPCACHEUSE1A", "aktiver HTML-sitecache");
define("_JPCACHEUSE1B", "Funktionerer kun ved anonyme besøgende.");
define("_JPCACHEUSE2A", "cache-tid");
define("_JPCACHEUSE2B", "sekunder");
// define("_KEYWORD_TXT", "meta text - keywords");
define("_LOGGINWARNING", "Efter du har ændret ved disse optioner, må du evtl. igen logge ind.");
define("_MAILAUTH", "sende mail per");
define("_MAILAUTH_0", "PHP funktion - mail()");
define("_MAILAUTH_1", "SMTP med tilmelding ved serveren");
define("_MAILHOST", "mailserver");
define("_MAILHOST_2", "(smtp.domain.com / localhost)");
define("_MAILPASS", "kodeord");
define("_MAILPOP3AUTH", "&quot;SMTP-After-POP&quot; Hostnavn");
define("_MAILPORT", "SMTP port");
define("_MAILPORT_2", "(standard 25)");
define("_MAILSETTINGS", "SMTP - Mail-Server-Indstillinger");
define("_MAILUNAME", "brugernavn");
define("_MATCHANY", "Hint for ordet i teksten");
define("_MAXREF", "maks. antal af referer?");
define("_MISCOPT", "Yderligere indstillinger");
// define("_MODREWRITE", "Søgemaskinevenlige URLs");
define("_MVADMIN", "kun administratorer");
define("_MVALL", "alle besøgende");
define("_NEWSMODULE", "Nyhedsmodul konfiguration");
define("_NOFILTERING", "ingen filtring");
define("_NOTIFYCOMMENT", "Vil du blive underrettet om nye kommentarer?");
define("_NOTIFYSUBMISSION", "informer om nye indstillinger per email?");
define("_OLDSTORIES", "antal artikler i ældere artikel blok");
define("_PREFIX", "prefix af tabeller");
// define("_PROMODREWADMIN", "for administratorer");
// define("_PROMODREWANON", "for anonyme bruger (og søgemaskiner)");
// define("_PROMODREWERROR", "Bemærk");
// define("_PROMODREWERROR1", "I mx-Root er ingen .htaccess fil.");
// define("_PROMODREWERROR2", ".htaccess filen i mx-Root indeholder ikke en for mod_rewrite nødvendige oplysninger.");
// define("_PROMODREWUSERS", "for tilmeldte bruger");
define("_REQHTMLFILTER", "Ikke tilladte HTML-kode fra overgaveparameter filtre");
define("_SAFECOOKIE1", "Brug sikkerhedscookie for bruger?");
define("_SAFECOOKIE2", "Brug sikkerhedscookie for administratorer?");
define("_SECDAYS", "dage");
define("_SECDAYS2", "Ved <b>0</b> skal brugere nytilmelde sig ved browserstart.");
define("_SECINACTIVELENGTH", "vis aktualiseringsinterval for &quot;hvem er online&quot;");
define("_SECMEDLENGTH", "gyldighed af session og session-cookies");
define("_SECMINUTES", "minutter");
define("_SECOPT", "Sikkerhedsoptioner");
define("_SECSQLINJECT1", "formindske SQL-Injection risiko?");
define("_SECSQLINJECT3", "Flere indstillingsmuligheder findes i filen <i>includes/detection/config.php</i>.");
define("_SEC_LOGGING", "secure-logging slået til?");
define("_SELLANGUAGE", "sprog af sitet");
define("_SETSERVICE", "Service- og debug-indstillinger");
define("_SITECONFIG", "Websted konfiguration");
define("_SITEDOCTYPE", "Site validering (DOCTYPE - Indstilling)");
define("_SITELOGO", "sitelogo til print");
define("_SITENAME", "sitets navn");
define("_SITESERVICE", "site-service aktiv?");
define("_SITESERVICETEXT", "site-service tekst");
define("_SITESLOGAN", "sitemotto");
define("_STARTDATE", "site- startdato");
define("_STORIESHOME", "antal artikler på news siden");
define("_STORIESHOMECOLS", "antal kolonner på news-side");
define("_THEME4ADMIN", "Theme für Adminbereich");
define("_THEME4JQUI", "jQuery-UI <a href=\"http://jqueryui.com/themeroller/\" target=\"_blank\">Theme</a>");
define("_TIMEZONEDEFAULT", "Standard Timezone");
define("_TRACK_ACTIVATEIT", "Aktiver Website Tracking");
define("_USEHTMLTIDY", "Korrigere HTML-fejl");
// define("_USEMODREWRITE", "Aktiver mod_rewrite understøttelse?");
// define("_USEMODREWRITEEXTEND", "individuel / aktiver udvidet mod_rewrite regeler?");
define("_USERPREFIX", "prefix af bruger-tabel");
define("_HTMLDEFAULTS", "Sæt alle HTML-Tags på standardindstilling tilbage.");
define("_REQHTMLFILTER_0", "ikke filtrere");
define("_REQHTMLFILTER_1", "omsæt til læselige tegn");
define("_REQHTMLFILTER_2", "slet komplet");
define("_DBTYP", "database typ:");
define("_MODREWRITENOTE", "Til søgemaskinevenlige URLs skal Apache modulen &quot;mod_rewrite&quot; være aktiveret. Hvis PHP i CGI-tilstand kører, skal optionen 'cgi.fix_pathinfo = 0' i php.ini være aktiveret, så at søgemaskinevenlige URLs korrekt funktionerer.");
define("_DEACTHEMECACHEDES", "Forskellige design-områder bliver intern mellem lagret. Under Designarbejde skal cachen være slukket og ændringer vises med det samme.");
define("_BLOCKSHOW_0", "modul defineret");
define("_BLOCKSHOW_1", "altid");
define("_BLOCKSHOW_2", "aldrig");
define("_BLOCKSHOW_3", "kun på startside");
define("_BLOCKSHOW_LEFT", "vis blokke til venstre");

define("_DB_TYPE","databaseforbindelse");
define("_DB_TYPE_TEXT","Vælg databaseforbindelse.");
define("_DATABASE","Database");
define("_FILE","Fil");
define("_SESS_LOC","Gem session i");

define ("_COOKIECHOICE1","Vis note til cookies?");
define ("_COOKIECHOICE2","Link til Privatlivspolitik");
define("_COOKIECHOICE3","position note");
define("_CCTOP","top");
define("_CCBOTTOM","bund");

/* FTP  since 2.3*/
define("_FTPOPT","Serverindstillinger");
define("_FTPOPT_TEXT","");
define("_FTP_ON","activere FTP ?");
define("_FTP_ON_TEXT","activere FTP");
define("_FTP_PORT","Port");
define("_FTP_HOST","Server");
define("_FTP_SSL","tilslutning SSL");
define("_FTP_DIR","rodmappen");
define("_THEME_INFO","Standard tema kan indstilles <a href='admin.php?op=themes'>her</a> .");

?>