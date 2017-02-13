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
 * $Revision: 175 $
 * $Author: PragmaMx $
 * $Date: 2016-06-30 14:38:26 +0200 (Do, 30. Jun 2016) $
 */

defined('mxMainFileLoaded') or die('access denied');
langdefine("_ADMINBANCONFIG", "Sperr Verwaltung");
langdefine("_AUTOBAN", "Vorhandene Benutzerkonten automatisch deaktivieren");
langdefine("_AUTOBAN_HEAD","Userkonten sperren");
langdefine("_CUTWITHCOMMATA", "Die Worte mit Komma trennen!");
langdefine("_INFOHOWBAN", "IP Nummern - Sperren");
langdefine("_INFOHOWBANHELP", "<ul>\n  <li>\n    Hier können Sie Störenfriede sperren\n    solange die IP gültig ist - diese ändert sich im Normalfall mit jedem\n    neuen Verbindungsaufbau des Benutzers.</li>\n  <li>\n    Wir weisen darauf hin, dass Sperren mit\n    Vorsicht zu behandeln sind und nur ein Mittel nach erfolglosen Mahnungen ist.</li>\n  <li>\n    Zu sperrende IPs müssen mit einem\n    Komma &quot;,&quot; getrennt werden (Beispiel: 127.0.0.1,192.168.0.1)</li>\n</ul>");
langdefine("_INFOHOWBANMAIL", "eMailadressen - Sperren");
langdefine("_INFOHOWBANMAILHELP", "<ul>\n  <li>\n    Hier können Sie nicht erlaubte eMailadressen sperren. Mit diesen\n    eMailadressen ist dann keine Anmeldung möglich.\n  </li>\n  <li>\n    Sie können die eMailadresse auf zwei Arten sperren:<br />\n    1. Eingabe der vollen Adresse (Beispiel: adresse@url.tld)<br />\n    2. Wildcard-Sperre: (Beispiel: @url.tld)<br />\n    Bei der Eingabe der vollen Adresse wird nur diese eine Adresse nicht\n    zugelassen, bei Eingabe einer Wildcardsperre wird jede Adresse die von\n    dieser URL kommt abgelehnt.\n  </li>\n  <li>\n    Zu sperrende eMailadressen müssen mit einem\n    Komma &quot;,&quot; getrennt werden (Beispiel: adresse@url.tld,@url.tld)</li>\n</ul>");
langdefine("_INFOHOWBANNAME", "Benutzernamen - Sperren");
langdefine("_INFOHOWBANNAMEHELP", "<ul>\n  <li>\n    Hier können Sie nicht erlaubte\n      Benutzernamen sperren. Mit diesen Benutzernamen ist dann keine Anmeldung mehr möglich.\n  </li>\n  <li>\n    <strong>Achtung:</strong> Wenn Sie die Option &quot;<em>Userkonten sperren</em>&quot; verwenden, werden evtl. vorhandene\n    Benutzerkonten automatisch deaktiviert, d.h. der Benutzer kann sich in\n    dieses Konto nicht mehr einlogen!</li>\n  <li>\n    Zu sperrende Benutzernamen müssen mit einem\n    Komma &quot;,&quot; getrennt werden (Beispiel: hans,fritz,sarah)</li>\n</ul>");
langdefine("_IPADDED", "Sperrdatei wurde aktualisiert.");

?>