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
 * $Revision: 106 $
 * $Author: PragmaMx $
 * $Date: 2016-01-26 13:20:12 +0100 (mar. 26 janv. 2016) $
 */

/* $uid muss vorhanden sein!! */
$hook = function($module_name, $para_uid)
{
	global $user_prefix;
    /* $para_uid enthält hier nur die uid des Users !! */
    extract($para_uid, EXTR_SKIP);

    /* deswegen die restlichen Userdaten per API-Funktion abfragen */
    $userinfo = mxGetUserDataFromUid($uid);

    if (!defined('MXB_INIT')) define('MXB_INIT', true);

    defined('MXB_SETTINGSFILE')
    or include_once(dirname(dirname(__file__)) . DS . 'includes' . DS . 'initvar.php');

    global $table_members, $table_whosonline, $table_threads, $table_posts, $table_forums;
    include(MXB_SETTINGSFILE);

    if (!isset($table_members)) {
        return false;
    }
	if ($userinfo['user_regtime']==0) {
		$res = sql_query("SELECT * FROM $table_members WHERE username='" . mxAddSlashesForSQL($userinfo['uname']) . "'");
		$buser =sql_fetch_array($res);
		if (array_key_exists("regdate",$buser))sql_query("UPDATE {$user_prefix}_users SET user_regtime='" . $buser['regdate'] . "' WHERE uid=" . $para_uid);
	}
    $res = sql_query("SELECT username FROM $table_members WHERE username='" . mxAddSlashesForSQL($userinfo['uname']) . "'");
    list($tmpusername) = sql_fetch_row($res);
    if ($tmpusername) {
        return true;
    }

    function_exists('mxb_insert_user')
    or include_once(dirname(dirname(__file__)) . DS . 'includes' . DS . 'functions.php');

    function_exists('mxb_insert_user')
    and mxb_insert_user($userinfo['uname']);
}

?>