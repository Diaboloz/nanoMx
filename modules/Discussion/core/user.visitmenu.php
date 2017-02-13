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

$hook = function($module_name, $userinfo, &$items)
{
    /* $userinfo enthält die kompletten Userdaten des betroffenen Users */
    /* $items ergänzt die bestehende Liste */
    // extract($userinfo, EXTR_SKIP);
    defined('MXB_INIT')
    or define('MXB_INIT', true);

    defined('MXB_SETTINGSFILE')
    or include_once(dirname(dirname(__file__)) . DS . 'includes' . DS . 'initvar.php');

    global $table_members;
    include(MXB_SETTINGSFILE);

    if (!isset($table_members)) {
        return false;
    }

    $res = sql_query("SELECT username FROM $table_members WHERE username='" . mxAddSlashesForSQL($userinfo['uname']) . "'");
    list($tmpusername) = sql_fetch_row($res);

    if (!$tmpusername) {
        return false;
    }
    // defined('MXB_LANGFILE_INCLUDED') or mxGetLangfile($module_name);
    $items[] = array(/* Attribute */
        'link' => 'modules.php?name=' . $module_name . '&amp;file=member&amp;action=viewpro&amp;member=' . $userinfo['uname'],
        'caption' => _MXBBOARDPROFILE,
        'image' => PMX_MODULES_PATH . $module_name . '/images/folder_user.png',
        'tabname' => 'mxb' . $module_name,
        // 'title' => $langtmp['pmx_profile'],
        );
}

?>