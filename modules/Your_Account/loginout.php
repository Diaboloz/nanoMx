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
 * loginscreen()
 *
 * @return nothing
 */
function loginscreen()
{
    if (MX_IS_USER) {
        return mxRedirect('modules.php?name=Your_Account');
    }

    $userconfig = load_class('Userconfig');

    $tplvars = (is_array(mxSessionGetVar('formvalues'))) ? mxSessionGetVar('formvalues') : array();
    $tplvars['uname'] = (isset($tplvars['uname'])) ? $tplvars['uname'] : '';
    $tplvars['rememberme'] = (isset($tplvars['rememberme'])) ? 1 : 0;
    $tplvars['pp_link'] = ($userconfig->pp_link) ? $userconfig->pp_link : '';

    if (isset($_GET['nogo'])) {
        $tplvars['message'] = _LOGININCOR;
    } else {
        $tplvars['message'] = '';
    }

    mxSessionDelVar('formvalues');

    /* Templateausgabe erstellen */
    $tpl = load_class('Template');
    $tpl->init_path(__DIR__);
    $tpl->init_template(__FILE__);
    $tpl->assign($tplvars);

    include('header.php');
    title(_USERREGLOGIN);
    userNavigation('login');
    $tpl->display('loginout.html');
    include('footer.php');
}

/**
 * douserlogin()
 *
 * @param mixed $pvs
 * @return
 */
function douserlogin($pvs)
{
    global $user_prefix, $prefix;

    if (MX_IS_USER) {
        return mxRedirect('modules.php?name=Your_Account');
    }

    $defaults = array(/* Standardwerte, die immer von einem gültigen Formular kommen sollten */
        'check' => mt_rand(),
        'check_cookie' => mt_rand(),
        'uname' => '',
        'pass' => '',
        'rememberme' => '0',
        );
    // TODO: rememberme auswerten !!!
    $pvs = array_merge($defaults, $pvs);

    $userconfig = load_class('Userconfig');

    if (isset($pvs['redirect'])) {
        $nexturl = $pvs['redirect'];
        unset($pvs['redirect']);
    } else {
        $nexturl = $userconfig->yastartpage;
    }

    /* Wenn Sicherheitscookie eingeschaltet, wird immer ein neues salt/hash-Paar in die DB geschrieben */
    $resetdb = (!empty($GLOBALS['vkpSafeCookie1']));

    $uloginreqcheck = mxSessionGetVar('uloginreqcheck');
    mxSessionDelVar('uloginreqcheck');
    mxSessionSetVar('formvalues', $pvs); // Werte zwischenspeichern, falls Fehler im Login
    $stop = 0;
    $loggedin = false;

    switch (true) {
        case empty($pvs['uname']):
            /* Loginname nicht angegeben */
            $stop = 3;
            break;
        case empty($pvs['pass']):
            /* Passwort nicht angegeben */
            $stop = 10;
            break;
        case $pvs['check_cookie'] != MD5(session_id()):
            /* Verstecktes Formularfeld check_cookie entweder leer, verfälscht oder nicht zur aktuellen Session passend.
             * Unautorisierter Loginversuch über externes Formular, oder ungültige Session weil keine Cookies aktiviert, bzw. Cookie ungültig
             */
            $stop = 1;
            break;
        case $pvs['check'] != MD5($uloginreqcheck):
            /* Verstecktes Formularfeld check entweder leer, verfälscht oder nicht zur aktuellen Session passend.
             * Unautorisierter Loginversuch über externes Formular, oder ungültige Session weil keine Cookies aktiviert, bzw. Cookie ungültig
             */
            $stop = 2;
            break;
        case in_array(strtolower($pvs['uname']), mxGetCensorListUsers()):
            /* zensierter Benutzername angegeben */
            $stop = 11;
            break;
        default:
            $pvs['uname'] = trim(substr($pvs['uname'], 0, 25));
    }

    $dbdata = array();
    if (!$stop) {
        /* Userdaten aus DB lesen */
        $dbdata = mxGetUserDataFromUsername($pvs['uname']);
    }

    switch (true) {
        case $stop:
            /* wenn schon ein Fehler, braucht man nicht mehr prüfen... */
            break;

        case !$dbdata:
        case empty($dbdata['uid']);
            /* Angegebener Username existiert nicht in der Datenbank */
            $stop = 4;
            break;

        case empty($dbdata['user_stat']):
            /* Useraccount neu, noch nicht aktiviert. */
            $stop = 6;
            break;

        case $dbdata['user_stat'] < 0:
            /* Useraccount ist gelöscht. */
            $stop = 7;
            break;

        case $dbdata['user_stat'] == 2:
            /* Useraccount ist deaktiviert. */
            $stop = 9;
            break;

        case ($dbdata['uname'] != $pvs['uname']) && (!$userconfig->uname_caseinsensitive):
            /* Benutzername existiert, aber falsche Gross-Kleinschreibung */
            $stop = 8;
            break;

        case in_array(strtolower($pvs['uname']), mxGetCensorListUsers()):
            /* zensierter Benutzername angegeben */
            /* 2 mal stop 11, weil evtl. $pvs['uname'] inzwischen geändert wurde */
            $stop = 11;
            break;

        case $dbdata['uname'] != $pvs['uname']:
            /* Benutzername existiert, falsche Gross-Kleinschreibung wird ignoriert */
            if (strtolower($dbdata['uname']) == strtolower($pvs['uname']) && $userconfig->uname_caseinsensitive) {
                /* korrekte Gross-Kleinschreibung für Loginnamen verwenden */
                $pvs['uname'] = $dbdata['uname'];
                /* und weiter, kein break, deshalb hier erst am Schluss des CASE */
            } else {
                /* Angegebener Username existiert nicht in der Datenbank */
                $stop = 4;
                break;
            }
            // mxDebugFuncVars($dbdata['uname'], $pvs['uname']);
    }

    switch (true) {
        case $stop:
            /* wenn schon ein Fehler, braucht man nicht mehr prüfen... */
            break;

        case mxSessionGetVar('user_uid') && mxSessionGetVar('user_uname'):
            /* prüfen, ob die evtl. vorhandenen Sessiondaten mit den Logindaten übereinstimmen */
            if (mxSessionGetVar('user_uid') != $dbdata['uid'] || mxSessionGetVar('user_uname') != $dbdata['uname']) {
                /* Es existiert bereits eine gültige Session mit anderen Userdaten. */
                mxSessionDestroy();
                $stop = 10;
            }
            break;
    }

    /* Logindaten mit Datenbankwerten vergleichen */
    switch (true) {
        case $stop:
            /* wenn schon ein Fehler, braucht man nicht mehr prüfen... */
            break;

        /* wenn neuer pbkdf2-Hash richtig */
        case pmx_password_verify($pvs['pass'], $dbdata['pass_salt'], $dbdata['pass']):
            $loggedin = true;
            break;

        /* Check auf alten MD5-Hash ohne Salt */
        case empty($dbdata['pass_salt']):
            /* wenn alter MD5-Hash richtig */
            if ($dbdata['pass'] === md5($pvs['pass'])) {
                $loggedin = true;
                /* ein neues salt/hash-Paar wird automatisch weiter unten generiert */
                $resetdb = true;
            } else {
                /* Passwort falsch. */
                $stop = 5;
            }
            break;

        default:
            /* Passwort falsch. */
            $stop = 5;
    }

    if ($loggedin && !$stop) {
        if ($resetdb) {
            /* Neues salt/hash-Paar in Tabelle schreiben */
            $dbdata['pass_salt'] = pmx_password_salt();
            $dbdata['pass'] = pmx_password_hash($pvs['pass'], $dbdata['pass_salt']);
            sql_query("UPDATE {$user_prefix}_users SET
              pass='" . mxAddSlashesForSQL($dbdata['pass']) . "',
              pass_salt='" . mxAddSlashesForSQL($dbdata['pass_salt']) . "'
              WHERE uid=" . intval($dbdata['uid']));
        } else {
            // $pass = $adminrow['pwd'];
        }

        /* Login durchführen */
        pmx_user_setlogin($dbdata);

        /* aufräumen */
        mxSessionDelVar('formvalues');
        mxSessionDelVar('ucountbadlogin');
        mxSessionDelVar('newusercheck'); // falls noch von Accounterstellung vorhanden
        /* Sprache einstellen */
        mxSessionSetVar('lang', $dbdata['user_lang']);

        return mxRedirect($nexturl);
    }

    /* Die Klasse mit den Sprachkonstanten auswählen */
    if (class_exists('pmx_userlogin_debug_errors_' . $GLOBALS['language'])) {
        $langclass = 'pmx_userlogin_debug_errors_' . $GLOBALS['language'];
    } else {
        $langclass = 'pmx_userlogin_debug_errors_english';
    }
    // geile Syntax, loooolll...
    $message = (isset($langclass::$lang[$stop])) ? $langclass::$lang[$stop] : $langclass::$lang[0];
    mxUserSecureLog('bad Userlogin', $message . ' - Account: ' . $pvs['uname']);

    mxSessionSetVar('ucountbadlogin', intval(mxSessionGetVar('ucountbadlogin')) + 1);
    // TODO: die Fehlversuche in der Datenbank anstatt in der Session speichern
    if (mxSessionGetVar('ucountbadlogin') >= 5) {
        mxSessionSetVar('ucountbadlogin', 3);
        $nexturl = 'modules.php?name=Your_Account&op=pass_lost';
        sleep(10); // 10 Sekunden warten
    } else {
        $nexturl = 'modules.php?name=Your_Account&nogo=' . mxSessionGetVar('ucountbadlogin');
    }

    return mxRedirect($nexturl);
}

/**
 * logout()
 *
 * @param mixed $delete
 * @return
 */
function logout($delete = false)
{
    global $prefix, $user_prefix, $currentlang;

    if (MX_IS_USER) {
        $udata = mxGetUserData();
        $qry = "UPDATE {$user_prefix}_users set user_lastvisit=" . time() . ", user_lastip='" . MX_REMOTE_ADDR . "', user_lastmod='logout' where uid=" . intval($udata['uid']);
        $result = sql_query($qry);
        if (MX_IS_ADMIN) {
            mxSessionSetVar('lasttime', 0); // Zurücksetzen, damit Online ausgeführt wird
            mxSessionDelVar('user');
            mxSessionSafeCookie(MX_SAFECOOKIE_NAME_USER, 0);
            //mxSetNukeCookie('user');
        } else {
            mxSessionDestroy();
        }

        /* Modulspezifische Logouts durchfuehren */
        pmx_run_hook('user.logout', $udata['uid']);
    }

    switch (true) {
        case $delete:
            return mxRedirect('modules.php?name=Your_Account&op=deletefinisch&newlang=' . $currentlang);
        case isset($_REQUEST['redirect']):
            return mxRedirect($_REQUEST['redirect'] . ((strpos($_REQUEST['redirect'], '?') === false) ? '?' : '&') . 'newlang=' . $currentlang);
        default:
            return mxRedirect('modules.php?name=Your_Account&op=logoutfin&newlang=' . $currentlang);
    }
}

/**
 * logoutfinisch()
 *
 * @return
 */
function logoutfinisch()
{
    return mxRedirect('index.php', _YOUARELOGGEDOUT);
}

/*
 * die Fehlermeldungen, die oben im Code stehen, zweisprachig,
 * abhängig von der default-language, hier in dieser Datei definieren
 * wird ja nur ins LOG geschrieben...
 */
class pmx_userlogin_debug_errors_german {
    // das sind nur Fehlermeldungen die abhängig von der eingestellten STANDARDSPRACHE der Seite ins Log geschrieben werden
    public static $lang = array(/* Fehlermeldungen */
        0 => 'undefinierter Fehler...',
        1 => 'Verstecktes Formularfeld check_cookie entweder leer, verfälscht oder nicht zur aktuellen Session passend',
        2 => 'Verstecktes Formularfeld check entweder leer, verfälscht oder nicht zur aktuellen Session passend',
        3 => 'Benutzername nicht angegeben',
        4 => 'Angegebener Benutzername existiert nicht',
        5 => 'Passwort falsch',
        6 => 'Useraccount ist neu und noch nicht aktiviert',
        7 => 'Useraccount ist gelöscht',
        8 => 'Benutzername existiert, aber falsche Gross-Kleinschreibung',
        9 => 'Useraccount ist deaktiviert',
        10 => 'Passwort nicht angegeben',
        11 => 'zensierter Benutzername angegeben',
        );
}
class pmx_userlogin_debug_errors_german_du extends pmx_userlogin_debug_errors_german {
}
class pmx_userlogin_debug_errors_english extends pmx_userlogin_debug_errors_german {
    // das sind nur Fehlermeldungen die abhängig von der eingestellten STANDARDSPRACHE der Seite ins Log geschrieben werden
    public static $lang = array(/* Fehlermeldungen */
        0 => 'undefined error ...',
        1 => 'Hidden field check_cookie empty, corrupted or does not match the current session',
        2 => 'Hidden field check empty, corrupted or does not match the current session',
        3 => 'Username not specified',
        4 => 'The specified username does not exist',
        5 => 'password incorrect',
        6 => 'Useraccount is new and has not yet been activated',
        7 => 'Useraccount is deleted',
        8 => 'Username exists but incorrect case sensitivity',
        9 => 'User Account is disabled',
        10 => 'Password not specified',
        11 => 'Censored Username was specified'
        );
}

?>