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
 *
 * thanks to uwe slick! http://www.deruwe.de/captcha.html
 * - just copy/pasted his thoughts GPLed!
 * modified by (c) 2007 by jubilee
 */

/**
 * this file may be used as html image
 * just include it as <img src="captchaimg.php?{hash}">
 * and a captcha image shall be displayed
 */
// Ausgabepuffer starten
ob_start();
// Ordner wechseln
chdir('../../../');
// keine Session starten
$mxWithoutSession = true;
// gzip-Kompression verhindern
$_SERVER['HTTP_ACCEPT_ENCODING'] = '';

/**
 * pragmaMx System includen
 */
if (!include('./mainfile.php')) {
    $out = ob_get_clean();
    /* Serverpfad ausfiltern */
    $out = str_replace(dirname(dirname(dirname(__FILE__))), '**', $out);
    /* versch. verraeterische Daten ausfiltern */
    $out = preg_replace('#\([^)]+\)#', '', $out);
    $out = preg_replace('#\[[^]]+\]#', '', $out);
    die('<h3>mainfile-error</h3>' . $out);
}
// ende pragmaMx System includen
// Einfache Laufzeitfehler melden
error_reporting(E_ERROR | E_WARNING | E_PARSE);
// Captcha Dateien includen
include(dirname(__FILE__) . '/phrasefactory.php');
include(dirname(__FILE__) . '/slickcaptcha.php');
// Konfiguration abrufen
include(dirname(__FILE__) . '/settings.php');
// Header senden
header('X-Powered-By: pragmaMx-cms');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-cache');
header('Pragma: no-cache');
header('Etag: ' . uniqid());
// Bild generieren und ausgeben
$captcha = new SlickCaptcha(PhraseFactory::get(false, $passphraselenght), $useRandomColors);
$captcha->setFontsPath(realpath(dirname(__FILE__) . '/fonts'));
$captcha->setImageWidth($imagewidth);
$captcha->setImageHeight($imageheight);
$captcha->setFontSize($fontsize);
$captcha->use_filter($filter);
$captcha->set_filter_name($filtertype);
$captcha->set_background_intensity($bgintensity);
$captcha->set_font_type($bgfonttype);
$captcha->enable_scratches($scratches);
$captcha->set_scratches_amount($scratchamount);
$captcha->set_minmax_size($minsize, 30);
$captcha->set_showgrid($addagrid);
$captcha->set_angle($angle);
$captcha->set_showcoloredlines($addhorizontallines);
if (!isset($_GET['debug'])) {
    // eventuelle Script oder Fehlerausgaben verwerfen und kuenftige Meldungen abschalten
    for ($i = 1; ($i <= 10 && ob_get_contents()); $i++) {
        ob_end_clean();
    }
    /* Bildchen ausgeben */
    $captcha->display();
} else {
    echo '<pre>';
    /* Welche Header wurden gesendet? */
    if (function_exists('headers_list')) {
        echo "\nHeader: \n";
        print_r(headers_list());
    }
    /* Versionsinfo der GD-Bibliothek */
    echo "\nGD-Info: \n";
    print_r($captcha->check_type_support());
    /* Objekt ausgeben */
    echo "\nSettings: \n";
    print_r($captcha);
    echo '</pre>';
    $out = ob_get_clean();
    /* Phrase ausfiltern */
    $out = str_replace($captcha->captchaPhrase, '****', $out);
    /* Serverpfad ausfiltern */
    $out = str_replace(realpath(dirname(__FILE__) . '/../../../'), '**', $out);
    /* Prefixe ausfiltern */
    $out = str_replace(array($prefix, $user_prefix), array('{prefix}', '{user_prefix}'), $out);
    echo $out;
}

?>