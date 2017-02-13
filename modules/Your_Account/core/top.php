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

$hook = function($module_name, $options, &$items)
{
    /* $options enthält nur den Schlüssel $top */
    /* $items ergänzt die bestehende Liste */

    global $prefix, $user_prefix;

    /* die Variable $top extrahieren */
    extract($options, EXTR_SKIP);

    $i_num = 0;

    $userconfig = load_class('Userconfig');

    /**
     * Top 10 users submitters
     */
    $result = sql_query("select uname, counter from {$user_prefix}_users where counter>0 ORDER BY counter DESC LIMIT " . intval($top));
    $rows = sql_num_rows($result);
    if ($rows > 1) {
        $i_num++;
        while (list($uname, $counter) = sql_fetch_row($result)) {
            $items[$module_name . $i_num]['list'][] = '' . mxCreateUserprofileLink($uname) . ' - (' . $counter . ' ' . _NEWSSENT . ')';
        }
        $items[$module_name . $i_num]['caption'] = $rows . ' ' . _NEWSSUBMITTERS;
    }

    /**
     * Top xx Userpunkte
     */
    if (!defined("mxYALoaded")) define("mxYALoaded", 1);

    if ($userconfig->useuserpoints || MX_IS_ADMIN) {
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

        $qry = "
          SELECT ${prefix}_userpoints.punkte, {$user_prefix}_users.uname
          FROM ${prefix}_userpoints
          LEFT JOIN {$user_prefix}_users
          ON ${prefix}_userpoints.uid = {$user_prefix}_users.uid
          WHERE {$user_prefix}_users.user_stat=1 AND ${prefix}_userpoints.punkte>0 AND ({$user_prefix}_users.uname not in($excludedusers))
          ORDER BY ${prefix}_userpoints.punkte DESC, ${prefix}_userpoints.uid
          LIMIT " . intval($top);
        $result = sql_query($qry);
        $content1 = '';
        $ipoints = array();
        if ($result) {
            while (list($punkte, $xuname) = sql_fetch_row($result)) {
                $ipoints[] = mxCreateUserprofileLink($xuname) . ' - (' . $punkte . ' ' . _USERPUNKTE1 . ')';
            }
        }
        if ($ipoints) {
            $i_num++;
            $items[$module_name . $i_num]['caption'] = count($ipoints) . ' ' . _TOPUSERPUNKTE . '<a name="POINT"></a>';
            $items[$module_name . $i_num]['list'] = $ipoints;
        }
    }
} ;

?>