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

/* Anzahl der User, die angezeigt werden sollen */
$numusers = 10;

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

/* Block kann gecached werden? */
$mxblockcache = false;
// $blockfiletitle = _USERPUNKTE1;
global $user_prefix, $prefix;
$content = '';
$show = '';

$userconfig = load_class('Userconfig');

if (!$userconfig->useuserpoints && !MX_IS_ADMIN) {
    return;
}

if (!defined("mxYALoaded")) define("mxYALoaded", 1);
// SQL-bedingung fuer excluded Users erstellen
$exusers = explode (',', $userconfig->excludedusers);
if (!MX_IS_ADMIN) {
    foreach ($exusers as $key => $value) {
        $xexusers[] = trim($value);
    }
}
$xexusers[] = $GLOBALS['anonymous'];
$xexusers[] = 'Anonymous';
$excludedusers = "'" . implode("','", mxAddSlashesForSQL($xexusers)) . "'";

$qry = "SELECT ${prefix}_userpoints.punkte, {$user_prefix}_users.uname
    FROM ${prefix}_userpoints LEFT JOIN {$user_prefix}_users ON ${prefix}_userpoints.uid = {$user_prefix}_users.uid
    WHERE {$user_prefix}_users.user_stat=1 AND ${prefix}_userpoints.punkte>0 AND ({$user_prefix}_users.uname not in($excludedusers))
    ORDER BY ${prefix}_userpoints.punkte DESC, ${prefix}_userpoints.uid
    LIMIT " . intval($numusers);
$result = sql_query($qry);

if ($result) {
    while (list($punkte, $uname) = sql_fetch_row($result)) {
        $show .= '<li>' . mxCreateUserprofileLink($uname) . ': ' . mxValueToString($punkte, 0) . '&nbsp;' . _POINTS . '</li>';
    }
}
if (!empty($show)) {
    $show = '<ul class="list">' . $show . '</ul>';
    if ($scrolling) {
        $content = mxScrollContent($show, $scrolldirection, $scrollspeed, $hoehe);
    } else {
        $content = $show;
    }
}
$content .= "<div class=\"tiny\"><a href=\"modules.php?name=Top#POINT\"><br />" . _ALLMEMBERS . "</a></div>";

?>
