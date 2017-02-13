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

/* $uid muss vorhanden sein!! */
$hook = function($module_name, $para_uid)
{
    /* $para_uid enthält hier nur die uid des Users !! */
    extract($para_uid, EXTR_SKIP);

    $userinfo = mxGetUserDataFromUid($uid);

    if (!defined('MXB_INIT')) define('MXB_INIT', true);

    !function_exists('mxbCleanUserdata')
    and include_once(dirname(dirname(__file__)) . DS . 'includes' . DS . 'functions.php');

    global $table_members, $table_whosonline, $table_threads, $table_posts, $table_forums;
    include(dirname(dirname(__file__)) . DS . 'settings.php');

    function_exists('mxbCleanUserdata') and isset($table_members)
    and mxbCleanUserdata($userinfo['uname']);
} ;

?>