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
 * $Date: 2015-07-08 09:07:06 +0200 (Mi, 08. Jul 2015) $
 */

defined('mxMainFileLoaded') or die('access denied');

define("_CAPTCHASAMPLE", "Beispiel");
define("_CAPTCHATITLE", "Captcha Verwaltung");
define("_CAPTCHAIMAGEWIDTH", "Bildbreite");
define("_CAPTCHAIMAGEHEIGHT", "Bildhöhe");
define("_CAPTCHAFONTSIZE", "Fontgrösse (Vordergrund)");
define("_CAPTCHABGINTENSITY", "Hintergrundintensität");
define("_CAPTCHABGFONTTYPE", "Fontgrösse (Hintergrund)");
define("_CAPTCHASCRATCHAMOUNT", "Scratches Anzahl");
define("_CAPTCHAPASSPHRASELENGHT", "Textlänge");
define("_CAPTCHAFILTER", "Verformungsfilter verwenden");
define("_CAPTCHASCRATCHES", "Scratches verwenden");
define("_CAPTCHASAVESETTINGS", "Einstellungen speichern");
define("_CAPTCHAFILTERTYPE", "Verformungsfilter Type");
define("_CAPTCHAADDHORLINES", "Farbige Linien hinzufügen");
define("_CAPTCHAADDAGRID", "Gitter hinzufügen");
define("_CAPTCHARANDOMCOLOR", "Zufallsfarben verwenden");
define("_CAPTCHAANGLE", "Lagewinkel der Schriften");
define("_CAPTCHAMINSIZE", "minimaler Gitternetzabstand");
// define("_CAPTCHAFEEDBACKON", "Aktivieren im Feedbackmodul");
// define("_CAPTCHAFAQON", "Aktivieren im FAQ-Modul");
// define("_CAPTCHAWEBLINKSON", "Aktivieren im Weblinksmodul");
// define("_CAPTCHADOWNLOADSON", "Aktivieren im Downloadsmodul");
// define("_CAPTCHADOCUMENTSON", "Aktivieren in den Documents");
// define("_CAPTCHANEWSON", "Aktivieren im Newsmodul");
// define("_CAPTCHANEWSLETTERON", "Aktivieren im Newslettermodul");
// define("_CAPTCHAGUESTBOOKON", "Aktivieren im Gästebuchmodul");
// define("_CAPTCHAREVIEWSON", "Aktivieren in den Testberichten");
define("_CAPTCHAUSERON", "Captcha auch für registrierte Benutzer aktivieren");
define("_CAPTCHAREGISTRATIONON", "Userregistrierung");
// define("_CAPTCHARECOMMENDON", "Aktivieren bei '" . _RECOMMEND . "'");
define("_CAPTCHACOMMENTSON", "Kommentare");
define("_CAPTCHAANSWERSUSE", "Antworten vorgeben");
define("_CAPTCHAANSWERSCOUNT", "Anzahl der vorgegebenen Antworten");
define("_CAPTCHADIGITSRANGE1", "Zahlenbereich von");
define("_CAPTCHADIGITSRANGE2", "bis");
define("_CAPTCHACALCSTEPS", "Anzahl der Rechenschritte");
define("_CAPTCHAERRORINGD", "Bild-CAPTCHAs können nicht generiert werden, weil die GD-Bibliothek Ihrer PHP-Installation keine JPEG-Unterstützung hat.");
define("_CAPTCHACHARSTOUSE", "Zeichen die im Code benutzt werden sollen");
define("_CAPTCHACHARCASESEN", "Gross- Kleinschreibung bei Eingabe beachten");
define("_CAPTCHAERR", "Das Captcha Bild kann eventuell nicht korrekt angezeigt werden, weil folgendes Problem besteht:");
define("_CAPTCHAERR_MISSINGGD", "Die GD-Bibliothek ist vermutlich nicht installiert, oder in der falschen Version, benötigt wird mindestens Version 2.0. (<a href=\"http://www.php.net/manual/ref.image.php\">info</a>)");
define("_CAPTCHAERR_FALSEFT", "Die GD-Bibliothek ist entweder nicht installiert, oder die FreeType Unterstützung ist nicht konfiguriert.");
define("_CAPTCHAERR_FALSEGD", "Es ist vermutlich eine inkompatible Version der GD-Bibliothek, benötigt wird mindestens Version 2.0. (<a href=\"http://www.php.net/manual/ref.image.php\">info</a>)");
define("_CAPTCHAERR_NOJPG", "Der JPG Support der GD-Bibliothek ist vermutlich nicht verfügbar.");
define("_CAPTCHAERR_MISSINGFT", "Der FreeType Support der GD-Bibliothek ist vermutlich nicht verfügbar.");
define("_CAPTCHASETTINGS", "Einstellungen");
define("_CAPTCHASESSION", "Captchas nur einmal pro Sitzung abfragen?");
define("_CAPTCHAMODSET", "Aktivieren in folgenden Modulen und Bereichen:");
define("_CAPTCHAMODHAVEOWN", "Folgende Module verwenden eigene Einstellungen um Captcha's zu aktivieren:");
define("_CAPTCHASETTINGS2", "Aktivierung");
define("_CAPTCHASETRESET", "Alles ignorieren und auf Systemstandard zurücksetzen.");

?>