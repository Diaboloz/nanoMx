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

/* Anzahl der User im Block */
$numusers = 20;

/* User, die nicht angezeigt werden sollen
 * Namen mit Komma trennen!
 */
$excludedusers = 'Knalxler, Dertzui';

/* maximale Hoehe des Blocks in Pixel. 0 um die Hoehe nicht zu beschränken */
$blockheight = 100;

/* --------- Ende der Konfiguration ----------------------------------------- */

extract($block['settings'], EXTR_OVERWRITE);

/* der passende Modulname */
$module_name = basename(dirname(dirname(__FILE__)));

/* Variablen initialisieren */
global $prefix, $user_prefix;

/* Block kann gecached werden? */
$mxblockcache = false;

if (!defined('_LASTSEENTITLE')) {
    mxGetLangfile($module_name, 'whoonline/lang-*.php');
}

$blockheight = (int)$blockheight;

/* SQL-bedingung fuer excluded Users erstellen */
$exusers = explode(',', $excludedusers);

if (!MX_IS_ADMIN) {
    while (list($key, $val) = each($exusers)) {
        $xexusers[] = trim($val);
    }
}
$xexusers[] = $GLOBALS['anonymous'];

if (MX_IS_USER) {
    $usersession = mxGetUserSession();
    $xexusers[] = $usersession[1];
}

$excludedusers = "'" . implode("','", mxAddSlashesForSQL($xexusers)) . "'";

$result = sql_query("SELECT uname, user_lastvisit FROM ${user_prefix}_users where (uname not in(" . $excludedusers . ")) AND (user_stat=1) ORDER BY user_lastvisit DESC limit 0,$numusers");

$list = '';
while (list($uname, $date) = sql_fetch_row($result)) {
    $days = 0;
    $hours = 0;
    $mins = 0;
    $realtime = time() - $date;
    $dont = false;
    /* how many days ago? */
    if ($realtime >= (60 * 60 * 24 * 2)) { // if it's been more than 2 days
        $days = mxValueToString(floor($realtime / (60 * 60 * 24)), 0);
        $dont = true;
    } else if ($realtime >= (60 * 60 * 24)) { // if it's been less than 2 days
        $days = 1;
        $realtime -= (60 * 60 * 24);
    }
    if (!$dont) {
        /* how many hours ago? */
        if ($realtime >= (60 * 60)) {
            if ($days > 0) {
                $hours = round($realtime / (60 * 60), 0);
                $realtime = 0;
            } else {
                $hours = floor($realtime / (60 * 60));
                $realtime -= (60 * 60 * $hours);
            }
        }
        // how many minutes ago?
        if ($realtime >= 60) {
            $mins = floor($realtime / 60);
            $realtime -= (60 * $mins);
        }
        /* just a little precation, although I don't *think* mins will ever be 60... */
        if ($mins == 60) {
            $mins = 0;
            $hours += 1;
        }
    }
    $list .= '<li>' . mxCreateUserprofileLink($uname) . ': <span class="tiny">';
    if ($dont) {
        $list .= $days . '&nbsp;' . _LASTSEENDAYS;
    } else {
        $parts = array();
        if ($days > 0) {
            $parts[] = $days . '&nbsp;' . _LASTSEENDAY;
        }
        if ($hours > 0) {
            $parts[] = $hours . '&nbsp;' . (($hours > 1)?(_LASTSEENHOURS):(_LASTSEENHOUR));
        }
        if ($mins > 0) {
            $parts[] = $mins . '&nbsp;' . (($mins > 1)?(_LASTSEENMINUTES):(_LASTSEENMINUTE));
        }
        $list .= implode(',&nbsp;', $parts);
    }
    $list .= ' ' . _LASTSEENAGO . '</span></li>';
}

if ($list) {
    $content = ($blockheight == 0) ? $list : '<ul class="list" style="height: ' . $blockheight . 'px; overflow: auto;">' . $list . '</ul>';
}

/* Blocktitel aus Sprachdatei auslesen */
$blockfiletitle = _LASTSEENTITLE;

?>