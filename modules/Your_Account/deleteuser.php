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
defined('mxYALoaded') or die('access denied');

/**
 * deleteuser()
 *
 * @return
 */
function deleteuser()
{
    if (!MX_IS_USER) {
        mxUserSecureLog("bad Userdelete", "(is no user)");
        mxErrorScreen(_YOUBAD);
    }
    $usersession = mxGetUserSession();
    mxSessionSetVar("udelcode", $usersession[2]); // Passwort in session speichern
    include('header.php');
    echo '
		<div class="alert alert-warning">
			' . _SUREDELETE . '
			<br />
			<a href="modules.php?name=Your_Account&amp;op=delconfirm&amp;uid=' . $usersession[0] . '">' . _YES . '</a>
			' . _OR . '
			<a href="modules.php?name=Your_Account">' . _NO . '</a>
		</div>';
    include('footer.php');
}

/**
 * delconfirm()
 *
 * @param mixed $gvs
 * @return
 */
function delconfirm($gvs)
{
    global $prefix, $user_prefix;

    $uid = (empty($gvs["uid"])) ? 0 : intval($gvs["uid"]);
    $userinfo = mxGetUserDataFromUid($uid);

    $code = mxSessionGetVar("udelcode");
    mxSessionDelVar("udelcode");

    switch (true) {
        case !$uid:
        case !$userinfo:
        case !$userinfo['current']:
            mxUserSecureLog("bad Userdelete", "Account: " . $uid . " (no user-id)");
            return mxErrorScreen(_YOUBAD);
        case $code != $userinfo['pass']:
            mxUserSecureLog("bad Userdelete", "Account: " . $uid . " (no session-check-id)");
            return mxErrorScreen(_YOUBAD);
    }

    /* Modulspezifische Loeschungen durchfuehren */
    pmx_run_hook('user.delete', $uid);

    $userconfig = load_class('Userconfig');
    if ($userconfig->senddeletemail) {
        $subject = sprintf(_YA_DELETED_MAILSUBJ, $GLOBALS['sitename']);
        $message = sprintf(_YA_DELETED_MAILTEXT, $userinfo['uname'], $GLOBALS['sitename']);
        mxMail($GLOBALS['adminmail'], $subject, $message);
    }

    include_once(__DIR__ . DS . 'loginout.php');
    logout('delete');
}

/**
 * deletefinisch()
 *
 * @return
 */
function deletefinisch()
{
    mxRedirect('index.php', _ACCTDELETED);
}

?>