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
 * $Revision: 320 $
 * $Author: pragmamx $
 * $Date: 2017-02-08 22:14:48 +0100 (Mi, 08. Feb 2017) $
 */

defined('mxMainFileLoaded') or die('access denied');
// /////////////////////////////////////////////////////////////////////////////
// / ab hier beginnt die Konfiguration /////////////////////////////////////////
// Typ der Logfunktion: file, sql oder both für beide Typen
// bei sql werden die Daten im normalen mxSecurelogging gespeichert
// bei file, in der nachfolgend angegebenen Datei.
$conf['logtype'] = 'file'; # file, sql, both
// Pfad zur Logdatei
// Diese Datei sollte in einem geschützten Verzeichnis liegen!!
$conf['logfile'] = PMX_DYNADATA_DIR . '/logfiles/detect.' . date('Y-m-d') . '.log';
// Weiterleiten auf andere Seite oder leerlassen um mit Fehlermeldung direkt abzubrechen
$conf['redirect'] = './'; #
// $conf['redirect'] = 'http://www.google.de/'; #
// mailadressen, wo die Detection hinversendet werden soll
// weitere Adressen können nach dem gleichen Schema zugefügt werden
// um kein Mail zu versenden, einfach diese Zeilen auskommentieren oder löschen
$conf['sendmail'][] = $GLOBALS['adminmail']; // Standardadminmailadresse, kann auch mit anderem Wert belegt werden :-))
$conf['sendmail'][] = 'ids@pragmamx.org'; // Falls das Entwicklerteam ebenfalls benachrichtigt werden soll
// Minimale Länge des RequestStrings, ab wann SQL-Abfrage überhaupt geprüft werden soll
// Der RequestString beinhaltet alle Variablen die per _GET, _POST und _COOKIE übergeben werden, ausser den Sessioncookies
$conf['minlen_req'] = 12; #
// Minimale Länge der SQL-Abfrage, ab wann überhaupt geprüft werden soll
$conf['minlen_sql'] = 15; #
// Session zerstören = logout falls User oder Admin
$conf['killsession'] = 1;
// IP-Adressen bannen
// - 0 = kein Banning
// - 1 = nur bereits gebannte IP-Adressen werden von der Seite ferngehalten
// - 2 = wenn Angriffe festgestellt werden, wird die entsprechende IP automatisch gebannt
$conf['ipbanning'] = 2;
// Listendatei der gebannten IP's
$conf['ipfile'] = PMX_DYNADATA_DIR . '/logfiles/banned.log';
// Nachricht, die erscheint, wenn eine IP-Adresse gebannt ist
// dies sollte eine komplette HTML-Seite sein
$conf['ipbanmsg'] = '<html><head><title>banned</title></head><body bgcolor="#DDDDDD">
        <table width="99%" height="99%" border="10" cellspacing="30" cellpadding="100">
        <tr align="center" valign="middle"><td>
        <h2><font color="#800000">sorry, ip ' . MX_REMOTE_ADDR . ' is banned from our site</font></h2>
        
        </td></tr></table>
        </body></html>';
// logfile der gebannten IP's, die die Seite besuchen
$conf['visitsfile'] = PMX_DYNADATA_DIR . '/logfiles/banned_visitors.' . date('Y-m-d') . '.log';

/**
 * restrictor von http://www.bot-trap.de/ verwenden, falls vorhanden
 */
$conf['restrictor'] = 1;

?>