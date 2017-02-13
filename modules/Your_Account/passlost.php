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
 * pass_lost()
 *
 * @return
 */
function pass_lost()
{
    if (MX_IS_USER) {
        return main();
    }
    $userconfig = load_class('Userconfig');

    if ($userconfig->passlost_codeoption && isset($_GET['dat'])) {
        /* Übergabeparameter aufdröseln */
        list($uname, $code) = explode('|', mxSecureValue(base64_decode($_GET['dat']), true));
    } else {
        $uname = (isset($_GET['uname'])) ? $_GET['uname'] : '';
        $code = '';
    }

    if ($userconfig->passlost_codeoption) {
        $noproblem = _NOPROBLEM;
        $sendpassword = _SENDPASSWORD;
        $fieldname = 'code';
        $fieldcaption = _CONFIRMATIONCODE;
    } else {
        $noproblem = _NOPROBLEM_2;
        $sendpassword = _SENDPASSWORD_2;
        $fieldname = 'email';
        $fieldcaption = _EMAIL;
    }

    /* Templateausgabe erstellen */
    $tpl = load_class('Template');
    $tpl->init_path(__DIR__);
    $tpl->assign(compact('uname', 'code', 'noproblem', 'sendpassword', 'fieldname', 'fieldcaption'));

    include('header.php');
    title(_USERREGLOGIN);
    userNavigation('passlost');
    $tpl->display('passlost.html');
    include('footer.php');
}

/**
 * mail_password()
 *
 * @return
 */
function mail_password()
{
    global $user_prefix;

    $userconfig = load_class('Userconfig');

    if (empty($_POST['uname'])) {
        $uname = '';
        $areyou = rand();
    } else {
        $uname = substr(strip_tags($_POST['uname']), 0, 25);
        $userinfo = mxGetUserDataFromUsername($uname);
        $areyou = substr(md5($userinfo['pass']), 3, 10);
    }

    /* jedes Absenden des Formulars erhöht den Zähler */
    $postcounter = intval(mxSessionGetVar('passlost'));
    mxSessionSetVar('passlost', $postcounter + 1);
    if ($postcounter > 5) {
        /* bei mehr als 5 Versuchen ist Schluss */
        mxUserSecureLog('bad Passlost', '5 times sent the form - Account: ' . $uname);
        return mxRedirect('./');
    }

    $successinfo = _CODEFOR . " " . $uname . " " . _MAILED;

    switch (true) {
        case empty($uname):
            /* beide optionen, kein Username angegeben */
            return mxRedirect('modules.php?name=Your_Account&op=pass_lost', _ERROR_NO_USERNAME);

        case empty($userinfo['uid']):
            /* beide optionen, Username nicht gefunden */
            mxUserSecureLog('bad Passlost', 'username not found in database - Account: ' . $uname);
            return mxRedirect('modules.php?name=Your_Account&op=pass_lost', $successinfo, 10); // _ERROR_USERNAMENOTEXIST

        case empty($userinfo['email']):
        case !mxCheckEmail($userinfo['email']):
            /* beide optionen, Emailadresse des Accounts ungültig oder nicht vorhanden */
            return mxRedirect('modules.php?name=Your_Account&op=pass_lost', _ERROR_USERHASNOEMAIL);

        case pmx_is_mail_banned($userinfo['email']):
            /* beide optionen, Emailadresse des Accounts gesperrt */
            return mxRedirect('modules.php?name=Your_Account&op=pass_lost', _ERROR_USERHASNOEMAIL . ' (' . _MAILISBLOCKED . ')');

        case !$userconfig->passlost_codeoption && empty($_POST['email']):
            /* ohne Codeoption, Emailadresse beim Senden nicht angegeben */
            return mxRedirect('modules.php?name=Your_Account&op=pass_lost', _ERROR_NO_USERNAME_EMAIL);

        case !$userconfig->passlost_codeoption && !mxCheckEmail($_POST['email']):
            /* ohne Codeoption, ungültige Emailadresse beim Senden angegeben */
            return mxRedirect('modules.php?name=Your_Account&op=pass_lost', _ERRORINVEMAIL);

        case !$userconfig->passlost_codeoption && pmx_is_mail_banned($_POST['email']):
            /* ohne Codeoption, gesperrte Emailadresse beim Senden angegeben */
            return mxRedirect('modules.php?name=Your_Account&op=pass_lost', _BLOCKEDMAIL);

        case !$userconfig->passlost_codeoption && $_POST['email'] != $userinfo['email']:
            /* ohne Codeoption, beim Senden angegebene Emailadresse passt nicht zum Usernamen */
            mxUserSecureLog('bad Passlost', 'false mail for this useraccount - Account: ' . $uname);
            return mxRedirect('modules.php?name=Your_Account&op=pass_lost', $successinfo, 10); // _ERROR_USEREMAILNOTMATCH

        case $userconfig->passlost_codeoption && !empty($_POST['code']) && $areyou != $_POST['code']:
            /* mit Codeoption, kein Code, oder ungültiger Code beim Senden angegeben */
            return mxRedirect('modules.php?name=Your_Account&op=pass_lost', _ERROR_FALSECODE);

        case $userconfig->passlost_codeoption && empty($_POST['code']):
            mxSessionDelVar('passlost');
            /* mit Codeoption, erste Stufe, alles ok, Code senden */
            $addlink = '&dat=' . base64_encode($userinfo['uname'] . '|' . $areyou);
            $message = (_HELLO . " " . $userinfo['uname'] . ",\n\n" . _CODEREQUESTED . " [ip: " . MX_REMOTE_ADDR . "]\n\n" . _YOURCODEIS . " " . $areyou . "\n\n" . _HASTHISEMAIL . " \n" . PMX_HOME_URL . "/modules.php?name=Your_Account&op=pass_lost" . $addlink . "\n\n" . _IFYOUDIDNOTASK2);
            $subject = (_CODEFOR . " " . $userinfo['uname'] . " (" . $GLOBALS['sitename'] . ")");
            if (mxMail($userinfo['email'], $subject, $message)) {
                $msg = $successinfo;
            } else {
                $msg = _ERROR_CANNOTSENDMAIL;
            }
            return mxRedirect('modules.php?name=Your_Account&op=pass_lost', $msg, 10);

        default:
            /* beide Optionen, alles ok, neues Passwort zusendenn */
            $newpass = pmx_password_create();
            /* neues Passwort generieren */
            $salt = pmx_password_salt();
            $pass = pmx_password_hash($newpass, $salt);

            /* Datenbank aktualisieren */
            $query = "UPDATE {$user_prefix}_users SET pass='" . mxAddSlashesForSQL($pass) . "', pass_salt='" . mxAddSlashesForSQL($salt) . "' WHERE uid=" . intval($userinfo['uid']);
            if (sql_query($query)) {
                mxSessionDelVar('passlost');
                /* Wenn DB aktualisiert werden konnte, Passwort per Mail versenden */
                $message = (_HELLO . " " . $userinfo['uname'] . ",\n\n" . _HASREQUESTED . " [ip: " . MX_REMOTE_ADDR . "]\n\n" . _YOURNEWPASSWORD . " " . $newpass . "\n\n" . _YOUCANCHANGE . " \n" . PMX_HOME_URL . "/modules.php?name=Your_Account\n\n" . _IFYOUDIDNOTASK);
                $subject = (_USERPASSWORD4 . " " . $userinfo['uname'] . " (" . $GLOBALS['sitename'] . ")");
                if (mxMail($userinfo['email'], $subject, $message)) {
                    /* Modulspezifische Passwortänderungen durchfuehren */
                    pmx_run_hook('user.passlost', $userinfo['uid']);                    /* ok, mail konnte gesendet werden */

                    $msg = _PASSWORD4 . " " . $userinfo['uname'] . " " . _MAILED;
                    return mxRedirect('modules.php?name=Your_Account', $msg);
                } else {
                    /* mail konnte nicht gesendet werden, Passwort in DB wieder auf alten Wert zurücksetzen */
                    $query = "UPDATE {$user_prefix}_users SET pass='" . $userinfo['pass'] . "' WHERE uid=" . intval($userinfo['uid']);
                    sql_query($query);
                    $msg = _ERROR_CANNOTSENDMAIL;
                }

            } else {
                $msg = _UPDATEFAILED;
            }
            return mxRedirect('modules.php?name=Your_Account&op=pass_lost', $msg, 5);
    }
}

?>