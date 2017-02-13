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

/* Ausgabe scrollen? false = nein, true = ja */
$scrolling = true;

/* Hoehe des Ausgabebereichs in Pixel beim Scrollen */
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

/* der passende Modulname */
$module_name = basename(dirname(dirname(__FILE__)));

if (!mxModuleAllowed($module_name)) {
    /* Block darf nicht gecached werden und weg... */
    $mxblockcache = false;
    return;
}
// $blockfiletitle = _PASTARTICLES;
/* Block kann gecached werden? */
$mxblockcache = true;

global $prefix;

$where = "WHERE time <= now()";

switch (true) {
    case MX_MODULE != $module_name:
        break;
    case isset($_GET['catid']):
        $where .= " AND catid=" . intval($_GET['catid']);
        break;
    case isset($_GET['topic']):
        $where .= " AND topic=" . intval($_GET['topic']);
        break;
    case isset($_GET['new_topic']):
        // Abwärtskompatibilität < 1.12
        $where .= " AND topic=" . intval($_GET['new_topic']);
}

/* gleiche Berechnung wie im Newsmodul */
$pagecols = (empty($GLOBALS['storyhome_cols'])) ? 1 : intval($GLOBALS['storyhome_cols']);
if (MX_IS_USER) {
    $userinfo = mxGetUserData();
    $storynum = (empty($userinfo['storynum'])) ? $GLOBALS['storyhome'] : $userinfo['storynum'];
} else {
    $storynum = $GLOBALS['storyhome'];
}
$storynum = (empty($storynum) || $storynum > 50 || $storynum < 1) ? 10 : intval($storynum);
$storynum = ceil($storynum / $pagecols) * $pagecols;
$oldnum = (empty($GLOBALS['oldnum'])) ? $storynum : intval($GLOBALS['oldnum']);

/* 1 Satz mehr als eingestellt um gleich zu prüfen ob noch weitere Sätze vorhanden */
$result = sql_query("SELECT sid, title, UNIX_TIMESTAMP( time ) FROM ${prefix}_stories $where ORDER BY  `time`  DESC, sid DESC LIMIT " . intval($storynum) . ", " . intval($oldnum + 1));

$vari = 0;
$time2 = '';
$content = '';

while (list($sid, $title, $time) = sql_fetch_row($result)) {
    if ($vari == $oldnum) {
        /* wenn eingestellte Zahl erreicht wird, sind weitere Sätze vorhanden */
        break;
    }

    if (date('Y', $time) === date('Y')) {
        $datetime2 = mx_strftime(_DATESTRING2, $time);
    } else {
        $datetime2 = mx_strftime(_DATESTRING, $time);
    }

    if ($time2 != $datetime2) {
        $time2 = $datetime2;
        if ($vari) {
            $content .= '</ul>';
        }
        $content .= '<h5>' . $datetime2 . '</h5>';
        $content .= '<ul class="list">';
    }

    $content .= '<li><a href="modules.php?name=' . $module_name . '&amp;file=article&amp;sid=' . $sid . '">' . $title . '</a></li>';

    $vari++;
}
if ($vari) {
    $content .= '</ul>';
}

if ($content && $scrolling) {
    $content = mxScrollContent($content, $scrolldirection, $scrollspeed, $hoehe);
}

?>