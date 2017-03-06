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
define("_INFOHOWBANHELP", "<ul>\n  <li>\n    Du kannst hier Störenfriede sperren\n    solange die IP gültig ist - diese ändert sich im Normalfall mit jedem\n    neuen Verbindungsaufbau des Benutzers.</li>\n  <li>\n    Wir weisen darauf hin, dass Sperren mit\n    Vorsicht zu behandeln sind und nur ein Mittel nach erfolglosen Mahnungen ist.</li>\n  <li>\n    Du musst die zu sperrenden IPs mit einem\n    Komma &quot;,&quot; trennen (Beispiel: 127.0.0.1,192.168.0.1)</li>\n</ul>");
define("_INFOHOWBANMAIL", "eMailadressen - Sperren");
define("_INFOHOWBANMAILHELP", "<ul>\n  <li>\n    Du kannst hier nicht erlaubte eMailadressen sperren. Mit diesen\n    eMailadressen ist dann keine Anmeldung möglich.\n  </li>\n  <li>\n    Du kannst die eMailadresse auf zwei Arten sperren:<br />\n    1. Eingabe der vollen Adresse (Beispiel: adresse@url.tld)<br />\n    2. Wildcard-Sperre: (Beispiel: @url.tld)<br />\n    Bei der Eingabe der vollen Adresse wird nur diese eine Adresse nicht\n    zugelassen, bei Eingabe einer Wildcardsperre wird jede Adresse die von\n    dieser URL kommt abgelehnt.\n  </li>\n  <li>\n    Du musst die zu sperrenden eMailadressen mit einem\n    Komma &quot;,&quot; trennen (Beispiel: adresse@url.tld,@url.tld)</li>\n</ul>");
define("_INFOHOWBANNAME", "Benutzernamen - Sperren");
define("_INFOHOWBANNAMEHELP", "<ul>\n  <li>\n    Hier kannst Du nicht erlaubte\n      Benutzernamen sperren. Mit diesem Benutzernamen ist dann keine Anmeldung möglich.\n  </li>\n  <li>\n    <strong>Achtung:</strong> Wenn Du die Option &quot;<em>Vorhandene Benutzerkonten\n    automatisch deaktivieren</em>&quot; aktivierst, werden evtl. vorhandene\n    Benutzerkonten automatisch deaktiviert, d.h. der Benutzer kann sich in\n    dieses Konto nicht mehr einlogen!</li>\n  <li>\n    Die zu sperrenden Benutzernamen musst Du mit einem\n    Komma &quot;,&quot; trennen (Beispiel: hans,fritz,sarah)</li>\n</ul>");
define("_IPADDED", "Sperrdatei wurde aktualisiert.");

?>