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
 * $Revision: 6 $
 * $Author: PragmaMx $
 * $Date: 2015-07-08 09:07:06 +0200 (mer., 08 juil. 2015) $
 */

defined('mxMainFileLoaded') or die('access denied');

define("_ADMINBANCONFIG", "Sperr Verwaltung");
define("_AUTOBAN", "Vorhandene Benutzerkonten automatisch deaktivieren");
define("_CUTWITHCOMMATA", "Die Worte mit Komma trennen!");
define("_INFOHOWBAN", "IP Nummern - Sperren");
define("_INFOHOWBANHELP", "<ul>\n  <li>\n    Hier können Sie Störenfriede sperren\n    solange die IP gültig ist - diese ändert sich im Normalfall mit jedem\n    neuen Verbindungsaufbau des Benutzers.</li>\n  <li>\n    Wir weisen darauf hin, dass Sperren mit\n    Vorsicht zu behandeln sind und nur ein Mittel nach erfolglosen Mahnungen ist.</li>\n  <li>\n    Zu sperrende IPs müssen mit einem\n    Komma &quot;,&quot; getrennt werden (Beispiel: 127.0.0.1,192.168.0.1)</li>\n</ul>");
define("_INFOHOWBANMAIL", "eMailadressen - Sperren");
define("_INFOHOWBANMAILHELP", "<ul>\n  <li>\n    Hier können Sie nicht erlaubte eMailadressen sperren. Mit diesen\n    eMailadressen ist dann keine Anmeldung möglich.\n  </li>\n  <li>\n    Sie können die eMailadresse auf zwei Arten sperren:<br />\n    1. Eingabe der vollen Adresse (Beispiel: adresse@url.tld)<br />\n    2. Wildcard-Sperre: (Beispiel: @url.tld)<br />\n    Bei der Eingabe der vollen Adresse wird nur diese eine Adresse nicht\n    zugelassen, bei Eingabe einer Wildcardsperre wird jede Adresse die von\n    dieser URL kommt abgelehnt.\n  </li>\n  <li>\n    Zu sperrende eMailadressen müssen mit einem\n    Komma &quot;,&quot; getrennt werden (Beispiel: adresse@url.tld,@url.tld)</li>\n</ul>");
define("_INFOHOWBANNAME", "Benutzernamen - Sperren");
define("_INFOHOWBANNAMEHELP", "<ul>\n  <li>\n    Hier können Sie nicht erlaubte\n      Benutzernamen sperren. Mit diesen Benutzernamen ist dann keine Anmeldung mehr möglich.\n  </li>\n  <li>\n    <strong>Achtung:</strong> Wenn Sie die Option &quot;<em>Vorhandene Benutzerkonten\n    automatisch deaktivieren</em>&quot; verwenden, werden evtl. vorhandene\n    Benutzerkonten automatisch deaktiviert, d.h. der Benutzer kann sich in\n    dieses Konto nicht mehr einlogen!</li>\n  <li>\n    Zu sperrende Benutzernamen müssen mit einem\n    Komma &quot;,&quot; getrennt werden (Beispiel: hans,fritz,sarah)</li>\n</ul>");
define("_IPADDED", "Sperrdatei wurde aktualisiert.");

?>