<?php
/**
 * mxBoard, pragmaMx Module
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * $Revision: 6 $
 * $Author: PragmaMx $
 * $Date: 2015-07-08 09:07:06 +0200 (mer. 08 juil. 2015) $
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
        case !include(dirname(__DIR__) . DS . 'settings.php'):
            break;

        default:
            global $prefix;

            if (!empty($upoints_threads)) {
                $result = sql_query("SELECT COUNT(tid) FROM " . $table_threads . " WHERE author='" . mxAddSlashesForSQL($userinfo['uname']) . "'");
                list($points) = sql_fetch_row($result);
                $sumpoints += ($points * $upoints_threads);
            }

            if (!empty($upoints_posts)) {
                $result = sql_query("SELECT COUNT(pid) FROM " . $table_posts . " WHERE author='" . mxAddSlashesForSQL($userinfo['uname']) . "'");
                list($points) = sql_fetch_row($result);
                $sumpoints += ($points * $upoints_posts);
            }
    }
} ;

?>