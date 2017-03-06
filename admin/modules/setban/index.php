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
 * $Date: 2015-07-08 09:07:06 +0200 (mer., 08 juil. 2015) $
 */

defined('mxMainFileLoaded') or die('access denied');

/* Sprachdatei auswählen */
mxGetLangfile(dirname(__FILE__));

if (!mxGetAdminPref('radminsuper')) {
    mxErrorScreen("Access Denied");
    die();
}

function setban()
{
    global $prefix;
    $ban_ip = array();
    $ban_name = array();
    $ban_mail = array();
    $ban_auto = '';
    $result = sql_query("SELECT ban_type, ban_val
    						FROM " . $prefix . "_user_ban
    						ORDER BY ban_val ASC ");
    while (list($ban_type, $ban_val) = sql_fetch_row($result)) {
        if ($ban_type == 'ban_auto') {
            $ban_auto = $ban_val;
        } else if ($ban_type == 'ban_ip') {
            $ban_ip[] = $ban_val;
        } else if ($ban_type == 'ban_name') {
            $ban_name[] = $ban_val;
        } else if ($ban_type == 'ban_mail') {
            $ban_mail[] = $ban_val;
        }
    }

    $ban_ip = (count($ban_ip) != 0) ? implode(', ', $ban_ip) : '';
    $ban_name = (count($ban_name) != 0) ? implode(', ', $ban_name) : '';
    $ban_mail = (count($ban_mail) != 0) ? implode(', ', $ban_mail) : '';

    /* alte Daten aus config.php importieren, falls vorhanden */
    if (empty($ban_name) && isset($GLOBALS['CensorListUsers']) && is_array($GLOBALS['CensorListUsers'])) {
        foreach ($GLOBALS['CensorListUsers'] as $oldban_name) {
            $ban_name[] = $oldban_name;
        }
        $ban_name = (count($ban_name) != 0) ? implode(', ', $ban_name) : '';
    }

    $template = load_class('Template');
    $template->init_path(__FILE__);
    $template->assign(compact('ban_ip', 'ban_name', 'ban_mail', 'ban_auto'));

    include('header.php');
    $template->display('navTabs.html');
    include('footer.php');
}

function setbanSave()
{
    global $prefix, $user_prefix;

    switch ($_POST['ip_type']) {
        case 'ban_ip':
            // IPs
            sql_query("DELETE FROM " . $prefix . "_user_ban WHERE ban_type='ban_ip'");
            $ip_ban = array_unique(preg_split('#\s*[,; ]\s*#', $_POST['ip_ban']));
            foreach($ip_ban as $ban_ip) {
                $ban_ip = trim(strip_tags($ban_ip));
                if (preg_match('#^((\d|[1-9]\d|2[0-4]\d|25[0-5]|1\d\d)(?:\.(\d|[1-9]\d|2[0-4]\d|25[0-5]|1\d\d)){3})$#', $ban_ip)) {
                    sql_query("INSERT IGNORE INTO " . $prefix . "_user_ban (ban_type, ban_val) VALUES ('ban_ip', '" . $ban_ip . "')");
                }
            }
            break;

        case 'ban_name':
            // Bentuzernamen
            $ip_auto = (empty($_POST['ip_auto'])) ? 0 : 1;
            sql_query("DELETE FROM " . $prefix . "_user_ban
            			WHERE ban_type='ban_name'");
            $ip_ban = array_unique(preg_split('#\s*[,; ]\s*#', str_replace(array('"', "'"), '', $_POST['ip_ban'])));
            foreach($ip_ban as $ban_name) {
                $ban_name = trim(strip_tags($ban_name));
                if ($ban_name) {
                    sql_query("INSERT IGNORE INTO " . $prefix . "_user_ban (ban_type, ban_val) VALUES ('ban_name', '" . mxAddSlashesForSQL($ban_name) . "')");
                    if ($ip_auto == 1) {
                        sql_query("UPDATE {$user_prefix}_users SET user_stat='2' WHERE uname='" . mxAddSlashesForSQL($ban_name) . "'");
                    }
                }
            }
            sql_query("UPDATE " . $prefix . "_user_ban SET ban_val='" . $ip_auto . "' WHERE ban_type='ban_auto'");
            break;

        case 'ban_mail':
            // eMailadressen
            sql_query("DELETE FROM " . $prefix . "_user_ban WHERE ban_type='ban_mail'");
            $ip_ban = array_unique(preg_split('#\s*[,; ]\s*#', $_POST['ip_ban']));
            foreach($ip_ban as $ban_mail) {
                $ban_mail = trim(strip_tags($ban_mail));
                if ($ban_mail && preg_match('/(([a-zA-Z0-9-])+([a-zA-Z0-9\._-])*)?@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+/', $ban_mail, $matches)) {
                    $ban_mail = strtolower($matches[0]);
                    sql_query("INSERT IGNORE INTO " . $prefix . "_user_ban (ban_type, ban_val) VALUES ('ban_mail', '" . $ban_mail . "')");
                }
            }
            break;
    }
    return mxRedirect(adminUrl(PMX_MODULE, '', '', '#' . $_POST['ip_type']), _IPADDED);
}

switch ($op) {
    case PMX_MODULE . '/save':
        setbanSave();
        break;
    default:
        setban();
        break;
}

?>