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

/* --------- Konfiguration fuer den Block ----------------------------------- */
$numcomment = 5; // Anzahl der Kommentare im Block
$length = 20; // Laenge des Kommentars, der ausgegeben werden soll

/* Ausgabe scrollen? false = nein, true = ja */
$scrolling = true;

/* Hoehe des Ausgabebereichs beim Scrollen */
$hoehe = 100;

/* Geschwindigkeit fuer das Scrollen
 * Wertebereich 1 - 100 */
$scrollspeed = 4;

/* Scrollrichtung
 * up = aufwaerts
 * down = abwaerts
 * left = von rechts nach links
 * right = von links nach rechts */
$scrolldirection = 'up';

/* --------- Ende der Konfiguration ----------------------------------------- */

extract($block['settings'], EXTR_OVERWRITE);

global $prefix;

/* Block kann gecached werden? */
$mxblockcache = true; // Kann der Block zwischengespeichert werden?

/* der passende Modulname */
$module_name = basename(dirname(dirname(__FILE__)));

if (!mxModuleAllowed($module_name)) {
    /* Block darf nicht gecached werden und weg... */
    $mxblockcache = false;
    return;
}

$items = array();
$result = sql_query('SELECT sid, comment FROM ' . $prefix . '_comments ORDER BY reply_date DESC LIMIT 0, ' . $numcomment);
while (list($sid, $comment) = sql_fetch_row($result)) {
    $comment = strip_tags(trim($comment));
    if (strlen($comment) > $length) {
        $comment = substr($comment, 0, $length) . ' ...';
    }
    $items[$sid] = $comment;
}

/* Templateausgabe erstellen */
$tpl = load_class('Template');
$tpl->init_path(__FILE__);
$tpl->init_template(__FILE__);
$tpl->assign(compact('items', 'module_name', 'scrolling'));

$content = $tpl->fetch();

if ($scrolling && $content) {
    $content = mxScrollContent($content, $scrolldirection, $scrollspeed, $hoehe);
}

?>