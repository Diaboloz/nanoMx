<?php
/**
 * mxBoard, pragmaMx Module
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 *
 * $Author: PragmaMx $
 * $Revision: 6 $
 * $Date: 2015-07-08 09:07:06 +0200 (mer. 08 juil. 2015) $
 *
 * based on eBoard v1.1, rewrite and modified by
 * vkpMx-Developer-Team (http://www.maax-design.de)
 * Original source-code made by the XMB-team
 * (XMB-Forum, http://www.xmbforum.com), modified for nukestyle-systems
 * by Trollix (XForum, http://www.trollix.com).
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 */

defined('mxMainFileLoaded') or die('access denied');

define('_TITRE', 'mxBoard Forum-Modul für pragmaMx 1.x<br/> Installation');
define('_INSTFAILED', 'Installation Fehlgeschlagen');
define('_FILE_NOT_WRITEN', 'Achtung, eine wichtige Datei kann während der Installation nicht überschrieben werden.<br/>Schreibrechte erforderlich für: ');
define('_MANUAL_RIGHTS', 'Sie müssen manuell Schreibrechte für diese Datei geben, bevor sie fortfahren können.');
define('_INSTALL_PARAM', 'Ihre Installations-Einstellungen:');
define('_TXT_XF_PREFIX', 'mxBoard prefix');
define('_TXT_XF_PREFIX_EXPL', 'Das ist Ihr mxBoard-Prefix für die Datenbanktabellen. Sollten Sie ein Upgrade machen, stehen hier die Werte der bisherigen Version. Bitte nicht ändern, wenn Sie sich nicht sicher sind.');
define('_TXT_XMB_LANG', 'Standardsprache des Forums');
define('_TXT_XMB_LANG_EXPL', 'Standard-Sprache für neue Benutzer. Hinweis: sie kann später noch geändert werden');
define('_TXT_XMB_THEME', 'Standard-Theme des Forums');
define('_TXT_XMB_THEME_EXPL', 'Standard-Theme für neue Benutzer. Hinweis: es kann später noch geändert werden');
define('_TEXTDEFAULT', 'Standard');
define('_NEXT2', 'Weiter');

define('_ERRPREFIX', 'Der Präfix darf nur Kleinbuchstaben, Zahlen und den Unterstrich (_) beinhalten und muss mit einem Kleinbuchstaben beginnen.');
define('_ERRDEFAULT', 'Es ist ein undefinierter Fehler aufgetreten.');
// define('_PRERR11', 'Die beiden Präfixe müssen mit einem Kleinbuchstaben beginnen, dürfen nur Zahlen, Kleinbuchstaben und den Unterstrich (_) enthalten und sollten eine Gesamtlänge von ' . PREFIX_MAXLENGTH . ' Zeichen nicht überschreiten.');
define('_SETUPHAPPY1', 'Herzlichen Glückwunsch');
define('_SETUPHAPPY2', 'Ihr System ist jetzt komplett installiert. Mit dem nächsten Klick kommen Sie direkt in das Administrationsmenü.');
define('_SETUPHAPPY3', 'Hier sollten sie zunächst die Grundeinstellungen überprüfen und neu abspeichern.');

define('_GET_SQLHINTS', 'Hier sehen Sie die SQL-Befehle die während der Konvertierung/Erstellung ausgeführt wurden');
define('_DATABASEISCURRENT', 'Die Datenbankstruktur war bereits auf dem aktuellen Stand. Änderungen waren nicht nötig.');
define('_DB_UPDATEREADY', 'Die Tabellen-Konvertierung/Erstellung ist abgeschlossen.');
// define('_DB_UPDATEFAIL', 'Die Tabellen-Konvertierung/Erstellung konnte nicht komplett ausgeführt werden.');

?>
