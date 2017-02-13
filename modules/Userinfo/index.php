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

if (!defined('mxYALoaded')) define('mxYALoaded', 1);

mxGetLangfile('Your_Account');
$pagetitle = _YAUSERINFO;
// TODO: Always call for YA, but need for call modules.php?uname=XXX
/* modulspezifisches Stylesheet einbinden */
$module_name = basename(dirname(__FILE__));
pmxHeader::add_style(PMX_MODULES_DIR . DS . $module_name . DS . 'style/style.css');
//pmxHeader::add_style('modules/Your_Account/style/style.css');

switch (true) {
    case isset($_REQUEST['uid']) && is_numeric($_REQUEST['uid']):
        $userdata = mxGetUserDataFromUid($_REQUEST['uid']);
        break;
    case isset($_REQUEST['id']) && is_numeric($_REQUEST['id']):
        $userdata = mxGetUserDataFromUid($_REQUEST['id']);
        break;
    case isset($_REQUEST['uname']) && is_string($_REQUEST['uname']):
        $userdata = mxGetUserDataFromUsername($_REQUEST['uname']);
        break;
    case isset($_REQUEST['id']) && is_string($_REQUEST['id']):
        $userdata = mxGetUserDataFromUsername($_REQUEST['id']);
        break;
    case MX_IS_USER:
        $userdata = mxGetUserData();
        break;
    default:
        mxErrorScreen(_SORRYNOUSERINFO, _YAUSERINFO);
        exit;
}

if (empty($userdata['uname'])) {
    mxErrorScreen(_SORRYNOUSERINFO, _YAUSERINFO);
    exit;
}

require_once(PMX_SYSTEM_DIR . DS . 'mx_userfunctions.php');
include_once(dirname(__FILE__) . DS . 'view.php');

viewuserinfo($userdata);

?>
