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

define("_CAPTCHASAMPLE", "eksempel");
define("_CAPTCHATITLE", "Captcha - administration");
define("_CAPTCHAIMAGEWIDTH", "Captcha - Billede bredde");
define("_CAPTCHAIMAGEHEIGHT", "Captcha - Billedhøjde");
define("_CAPTCHAFONTSIZE", "Captcha - Skrift størrelse (forgrund)");
define("_CAPTCHABGINTENSITY", "Captcha - Baggrundstyrke");
define("_CAPTCHABGFONTTYPE", "Captcha - Skrifttype (baggrund)");
define("_CAPTCHASCRATCHAMOUNT", "Captcha - Scratches mengde");
define("_CAPTCHAPASSPHRASELENGHT", "Captcha - tekstlængde");
define("_CAPTCHAFILTER", "Captcha - Brug forvrængningsfilter");
define("_CAPTCHASCRATCHES", "Captcha - Brug scratches");
define("_CAPTCHASAVESETTINGS", "Captcha - Gem indstillinger");
define("_CAPTCHAFILTERTYPE", "Captcha - forvrængningsfiltertype");
define("_CAPTCHAADDHORLINES", "Tilføj colorlinjer");
define("_CAPTCHAADDAGRID", "Tilføj raster");
define("_CAPTCHARANDOMCOLOR", "Brug tilfældige farver");
define("_CAPTCHAANGLE", "Skriftvinkel (30-45)");
define("_CAPTCHAMINSIZE", "Rasterafstand (10-20)");
// define("_CAPTCHAFEEDBACKON", "Aktiver i feedbackmodul");
// define("_CAPTCHAFAQON", "Aktiver i FAQ/OSS-Modul");
// define("_CAPTCHAWEBLINKSON", "Aktiver i weblinksmodul");
// define("_CAPTCHADOWNLOADSON", "Aktive i downloadmodul");
// define("_CAPTCHADOCUMENTSON", "Aktivieren in den Documents");
// define("_CAPTCHANEWSON", "Aktiver i newsmodul");
// define("_CAPTCHANEWSLETTERON", "Aktiver i nyhedsbrevsmodul");
// define("_CAPTCHAGUESTBOOKON", "Aktiver i gæstebogmodul");
// define("_CAPTCHAREVIEWSON", "Aktiver i testmeddelelser");
define("_CAPTCHAUSERON", "Brug Captcha også for registrerede bruger");
define("_CAPTCHAREGISTRATIONON", "brugerregistrering");
// define("_CAPTCHARECOMMENDON", "Aktiver i '" . _RECOMMEND . "'");
define("_CAPTCHACOMMENTSON", "kommentarer");
define("_CAPTCHAANSWERSUSE", "Foreslå svar");
define("_CAPTCHAANSWERSCOUNT", "Antal af foreslået svar");
define("_CAPTCHADIGITSRANGE1", "Tallene fra");
define("_CAPTCHADIGITSRANGE2", "til");
define("_CAPTCHACALCSTEPS", "Antal af regnestykker");
define("_CAPTCHAERRORINGD", "Bild-CAPTCHAs kan ikke blive genereret, GD-Bbiblioteket af din PHP-Installation understøtter ikke JPEG.");
define("_CAPTCHACHARSTOUSE", "Følgende tegn skal benyttes  i koden");
define("_CAPTCHACHARCASESEN", "Kontroller store/lille bogstaver ved indtastning");
define("_CAPTCHAERR", "Captcha billedet kan evt. ikke vises korrekt, der er følgende problemer:");
define("_CAPTCHAERR_MISSINGGD", "GD-Biblioteket er formodentlig ikke installeret eller er en forkert version, mindste version er 2.0. (<a href=\"http://www.php.net/manual/ref.image.php\">info</a>)");
define("_CAPTCHAERR_FALSEFT", "GD-Biblioteket er enten kke installeret eller FreeType understøttelsen er ikke konfigureret.");
define("_CAPTCHAERR_FALSEGD", "Det er formodentlig en ikke kompatibele version af GD-Biblioteket, mindste version er 2.0. (<a href=\"http://www.php.net/manual/ref.image.php\">info</a>)");
define("_CAPTCHAERR_NOJPG", "JPG Supporten af GD-Biblioteket er formodentlig ikke disnibel.");
define("_CAPTCHAERR_MISSINGFT", "FreeType Supporten af GD-Biblioteket er formodentlig ikke disponibel.");
define("_CAPTCHASETTINGS", "Indstillinger");
define("_CAPTCHASESSION", "Captcha forespørgsel kun én gang per session?");

define("_CAPTCHAMODSET", "Aktiver følgende moduler og områder:");
define("_CAPTCHAMODHAVEOWN", "Følgende moduler bruger deres egne indstillinger for at aktivere Captcha s:");
define("_CAPTCHASETTINGS2", "Aktivering");
define("_CAPTCHASETRESET", "Ignorer alt og set tilbage til systemstandard.");

?>