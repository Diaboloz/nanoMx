<?php
/**
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

$hook = function($module_name, $parameters, &$sumpoints)
{
    /* $parameters = array (
     *   userinfo => array : die kompletten Userdaten des betroffenen Users
     *   userconfig => array : die Benutzerkonfiguration (Userconfig) für die Punktzahlen
     * )
     */
    extract($parameters, EXTR_SKIP);
    extract($userconfig, EXTR_SKIP);

    switch (true) {
        case empty($userinfo['uid']):
        case empty($userinfo['uname']):
            break;

        default:
            global $prefix;

            if (!empty($upoints_entries)) { // Artikel
                $result = sql_query("SELECT COUNT(sid) FROM {$prefix}_stories WHERE informant='" . mxAddSlashesForSQL($userinfo['uname']) . "' AND  time  <= now()");
                list($points) = sql_fetch_row($result);
                $sumpoints += ($points * $upoints_entries);
            }
            if (!empty($upoints_comments)) { // Artikel Kommentare
                $result = sql_query("SELECT COUNT(tid) FROM {$prefix}_comments WHERE name='" . mxAddSlashesForSQL($userinfo['uname']) . "'");
                list($points) = sql_fetch_row($result);
                $sumpoints += ($points * $upoints_comments);
            }
    }
} ;

?>
