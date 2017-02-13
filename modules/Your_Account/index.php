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
require_once(PMX_SYSTEM_DIR . DS . 'mx_userfunctions.php');

/**
 * Startfunktion des Moduls
 */
function main()
{
    if (MX_IS_USER) {
        $uinfo = mxGetUserData();
        include_once(PMX_MODULES_DIR . DS . 'Userinfo' . DS . 'view.php');
        viewuserinfo($uinfo);
    } else {
        include_once(__DIR__ . DS . 'loginout.php');
        loginscreen();
    }
}

/**
 * Liste aller Avatare anzeigen
 */
function avatarlist()
{
    $pici = load_class('Userpic');
    $filelist = $pici->get_available_avatars();
    $imagepath = $pici->path_avatars;

    /* Template initialisieren */
    $template = load_class('Template');
    $template->init_path(__FILE__);

    /* hier die Ausgabefelder angeben */
    $template->assign(compact('filelist', 'imagepath'));
    /* Template ausgeben (echo) */
    $template->display('avatarlist.html');
}

/**
 * ya_deluserpic()
 * Ein Benutzerbild löschen
 *
 * @return nothing
 */
function ya_deluserpic()
{
    $redir = (empty($_SERVER['HTTP_REFERER'])) ? PMX_HOME_URL . '/modules.php?name=Your_Account' : $_SERVER['HTTP_REFERER'];

    switch (true) {
        case empty($_GET['uid']):
        case !intval($_GET['uid']):
            return mxRedirect($redir, _ACCESSDENIED);
    }

    $pici = load_class('Userpic', $_GET['uid']);
    $admin = mxGetAdminPref('radminuser');

    /* Berechtigung prüfen */
    switch (true) {
        case !MX_IS_USER && !$admin:
        case !$pici->is_own_image() && !$admin:
            return mxRedirect($redir, _ACCESSDENIED);
    }

    $deleted = $pici->delete_uploaded();
    if ($deleted) {
        return mxRedirect($redir, _UPIC_DELETED);
    } else {
        return mxRedirect($redir, _UPIC_DELETEERR);
    }
}

/**
 * Auswahl der Funktionen
 */
switch ($op) {
    case 'userinfo':
        mxRedirect('modules.php?name=Userinfo&uname=' . urlencode($_REQUEST['uname']) . '');
        break;

    case 'login':
        include_once(__DIR__ . DS . 'loginout.php');
        douserlogin($_POST);
        break;

    case 'logout':
        include_once(__DIR__ . DS . 'loginout.php');
        logout();
        break;

    case 'logoutfin':
        include_once(__DIR__ . DS . 'loginout.php');
        logoutfinisch();
        break;

    case 'new_user':
    case 'confirm':
    case 'finish':
        mxRedirect('modules.php?name=User_Registration');
        break;

    case 'edituser':
    case 'saveuser':
        //$pagetitle = _CHANGEHOME;
        include_once(__DIR__ . DS . 'edituser.php');
        $tmp = new pmxUserEdit($op);
        break;

    case 'edithome':
    case 'savehome':
        $pagetitle = _CHANGEHOME;
        include_once(__DIR__ . DS . 'edithome.php');
        $tmp = new pmxUserEditHome($op);
        break;

    case 'delete':
        $pagetitle = _DELETEACCT;
        include_once(__DIR__ . DS . 'deleteuser.php');
        deleteuser();
        break;

    case 'delconfirm':
        $pagetitle = _DELETEACCT;
        include_once(__DIR__ . DS . 'deleteuser.php');
        delconfirm($_GET);
        break;

    case 'deletefinisch':
        $pagetitle = _DELETEACCT;
        include_once(__DIR__ . DS . 'deleteuser.php');
        deletefinisch($_GET);
        break;

    case 'lost_pass':
    case 'pass_lost':
        $pagetitle = _PASSWORDLOST;
        include_once(__DIR__ . DS . 'passlost.php');
        pass_lost();
        break;

    case 'mailpasswd':
        $pagetitle = _PASSWORDLOST;
        include_once(__DIR__ . DS . 'passlost.php');
        mail_password($_POST);
        break;

    case 'avatarlist':
        $pagetitle = _AVAILABLEAVATARS;
        avatarlist();
        break;

    case 'deluserpic':
        ya_deluserpic();
        break;

    default:
        $pagetitle = _THISISYOURPAGE;
        main();
        break;
}

?>