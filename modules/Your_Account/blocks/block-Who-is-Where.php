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
 * idea by: >>www.surf4all.net<<
 */

defined('mxMainFileLoaded') or die('access denied');

/* --------- Konfiguration fuer den Block ----------------------------------- */
// User, die nicht angezeigt werden sollen
// Namen mit Komma trennen!
$excludedusers = "Texsterdsgf, Knaxllerfgfd";
// maximale Höhe des Blocks in Pixel. 0 um die Höhe nicht zu beschränken
$blockheight = 100;
/* --------- Ende der Konfiguration ----------------------------------------- */

extract($block['settings'], EXTR_OVERWRITE);

/* der passende Modulname */
$module_name = basename(dirname(dirname(__FILE__)));

global $prefix, $user_prefix;

/* Block kann gecached werden? */
$mxblockcache = true;
$content = "";

mxGetLangfile($module_name, 'whoonline/lang-*.php');

$past = time() - MX_SETINACTIVE_MINS ;
$blockheight = (int)$blockheight;

if (MX_IS_USER) {
    $usersession = mxGetUserSession();
    $xexusers[] = $usersession[1];
}
// SQL-bedingung fuer excluded Users erstellen
$exusers = explode (",", $excludedusers);
if (!MX_IS_ADMIN) {
    while (list($key, $val) = each($exusers)) {
        $xexusers[] = trim($val);
    }
}
$xexusers[] = $GLOBALS['anonymous'];
$excludedusers = "'" . implode("','", mxAddSlashesForSQL($xexusers)) . "'";

$result1 = sql_query("SELECT  uname, user_lastmod, user_lasturl, user_lastvisit, user_lastip FROM {$user_prefix}_users
WHERE ((uname NOT IN (" . $excludedusers . ")) AND (user_lastvisit >= " . $past . ") AND (user_stat=1) AND (user_lastmod<>'logout'))
ORDER BY uname");

$result2 = sql_query("SELECT v.module, v.url, v.time, v.ip
FROM ${prefix}_visitors AS v
WHERE ((v.time > " . $past . ") AND (v.uid = 0));");

$cnt_members = 0;
while (list($username, $module, $url, $time, $ip) = sql_fetch_row($result1)) {
    $cnt_members++;
    $url = mx_urltohtml(trim($url, '/. '));
    $arr_members[strtolower($username)] = '<li>' . mxCreateUserprofileLink($username) . ' -&gt;&nbsp;<a href="' . $url . '" title="' . $url . '">' . $module . '</a></li>';
}

$cnt_guests = 0;
while (list($module, $url, $time, $ip) = sql_fetch_row($result2)) {
    $cnt_guests++;
    $username = (MX_IS_ADMIN) ? $ip : $GLOBALS['anonymous'] . " " . $cnt_guests;
    $url = mx_urltohtml(trim($url, '/. '));
    $arr_guests[$time] = "<li>" . $username . " -&gt;&nbsp;<a href=\"" . $url . "\" title=\"" . $url . "\">" . $module . "</a></li>\n";
}

$list = "";
if ($cnt_members) {
    ksort($arr_members);
    $list1 = '<ul class="list">' . implode("", $arr_members) . '</ul>';
    $list .= "<b>" . _WHOWHEREMEMBERS . " (" . mxValueToString($cnt_members, 0) . "):</b><br />\n" . $list1;
}

if ($cnt_members && $cnt_guests) $list .= '<br />';

if ($cnt_guests) {
    ksort($arr_guests);
    $list2 = '<ul class="list">' . implode("", $arr_guests) . '</ul>';
    $list .= "<b>" . _WHOWHEREGUESTS . " (" . mxValueToString($cnt_guests, 0) . "):</b><br />\n" . $list2 . "";
}

if ($list) {
    $content = (($cnt_guests + $cnt_members) <= 3 || $blockheight == 0) ? $list : "<div style=\"height: " . $blockheight . "px; overflow : auto;\">" . $list . "</div>";
}
// Blocktitel aus Sprachdatei auslesen
// $blockfiletitle = _WHOWHERETITLE;

?>