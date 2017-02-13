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

/* $uid muss vorhanden sein!! */
$hook = function($module_name, $para_uid)
{
    /* $para_uid enthält hier nur die uid des Users !! */
    extract($para_uid, EXTR_SKIP);

    $userinfo = mxGetUserDataFromUid($uid);

    global $prefix, $user_prefix;

    if ($uid && $userinfo) {
        sql_query("UPDATE ${prefix}_authors SET user_uid=NULL WHERE user_uid=" . intval($uid));
        sql_query("DELETE FROM ${prefix}_userpoints WHERE uid=" . intval($uid));
        sql_query("DELETE FROM ${prefix}_visitors WHERE uid=" . intval($uid));

        /* Sollte ein Userbild hochgeladen sein, dieses löschen! */
        $pici = load_class('Userpic', $userinfo);
        $pici->delete_uploaded();

        if (empty($userinfo['user_stat'])) {
            /* wenn noch nicht aktiviert war, komplett löschen */
            sql_query("DELETE FROM {$user_prefix}_users WHERE uid=" . intval($uid));
            sql_query("DELETE FROM {$user_prefix}_users_temptable WHERE (uname='" . mxAddSlashesForSQL($userinfo['uname']) . "' AND email = '" . mxAddSlashesForSQL($userinfo['email']) . "')");
        } else {
            mt_srand((double)microtime() * 1000000);
            $selforadmin = ($userinfo['current']) ? 'self' : 'admin';
            $fields[] = "user_stat = -1";
            $fields[] = "uid       = " . intval($uid);
            $fields[] = "uname     = '" . mxAddSlashesForSQL($userinfo['uname']) . "'";
            $fields[] = "name      = 'deleted (" . $selforadmin . ")'";
            $fields[] = "pass      = '" . mxAddSlashesForSQL(pmx_password_salt()) . "'";
            $fields[] = "pass_salt = '" . mxAddSlashesForSQL(pmx_password_salt()) . "'";
            $qry = "REPLACE INTO {$user_prefix}_users SET " . implode(', ', $fields);
            sql_query($qry);
        }
    }
} ;

?>