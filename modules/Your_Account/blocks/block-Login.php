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

global $JPCACHE_ON;

/* der passende Modulname */
$module_name = basename(dirname(dirname(__FILE__)));

/* Block kann gecached werden? */
$mxblockcache = false;

$content = '';

if (!MX_IS_USER && MX_MODULE != $module_name) {
    if ($JPCACHE_ON) {
        $content .= '
        <ul class="list">
        <li><a href="modules.php?name=' . $module_name . '">' . _GUSERLOGIN . '</a></li>
        <li><a href="modules.php?name=' . $module_name . '&amp;op=pass_lost">' . _GPASSWORDLOST . '</a></li>';
        if (mxModuleAllowed('User_Registration')) {
            $content .= '<li><a href="modules.php?name=User_Registration">' . _GREGNEWUSER . '</a></li>';
        }
        $content .= '</ul>';
    } else {
        $content .= '
        <form action="modules.php" method="post">
        <p>' . _NICKNAME . ':<br />
        <input type="text" name="uname" size="15" maxlength="25" style="width: 80%;" /></p>
        <p>' . _PASSWORD . ':<br />
        <input type="password" name="pass" size="15" style="width: 80%;" /></p>
        <p><input type="submit" value="' . _LOGIN . '" style="width: 40%;" /></p>
        <input type="hidden" name="op" value="login" />
        ' . mxGetUserLoginCheckField() . '</form>
        <ul class="list">
        <li><a href="modules.php?name=' . $module_name . '&amp;op=pass_lost">' . _GPASSWORDLOST . '</a></li>';
        if (mxModuleAllowed('User_Registration')) {
            $content .= '<li><a href="modules.php?name=User_Registration">' . _GREGNEWUSER . '</a></li>';
        }
        $content .= '</ul>';
        if (!defined('mxloginblockviewed')) {
            define('mxloginblockviewed', true);
        }
    }
}

$blockfiletitle = _LOGIN;

?>