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
 * $Revision: 390 $
 * $Author: pragmamx $
 * $Date: 2017-10-08 14:33:39 +0200 (So, 08. Okt 2017) $
 */

defined('mxMainFileLoaded') or die('access denied');

/* Sprachdatei auswählen */
mxGetLangfile(__DIR__);

/**
 * admin_login()
 * das Admin-Loginformular
 *
 * @return nothing
 */
function admin_login()
{
    mt_srand((double)microtime() * 1000000);
    $xcv = mt_rand();
    mxSessionSetVar('reqcheck', $xcv);
    $formmessage = mxSessionGetVar('formmessage');
    mxSessionDelVar('formmessage');

    $field['submit'] = array('type' => 'submit',
        'value' => _LOGIN,
        );

    $field['op'] = array('type' => 'hidden',
        'value' => 'login',
        );

    $field['check'] = array('type' => 'hidden',
        'value' => MD5($xcv),
        );

    $field['sess'] = array('type' => 'hidden',
        'value' => MD5(session_id()),
        );

    $field['uri'] = array('type' => 'hidden',
        'value' => base64_encode(PMX_HOME_URL . $_SERVER['REQUEST_URI']),
        );

    $field['aid'] = array('type' => 'text',
        'label' => _ADMINID,
        'attribs' => array('size' => 30, 'maxlength' => 25, 'id' => 'aid'),
        );

    $field['pwd'] = array('type' => 'password',
        'label' => _PASSWORD,
        'attribs' => array('size' => 30),
        'options' => array('validate' => false),
        );

    /* build the default values for the form elements */
    foreach (array_keys($field) as $name) {
        $field[$name]['name'] = $name;
    }
    // $message = (isset($first_data['msg'])) ? $first_data['msg']:'';
    pmxHeader::add_jquery();

    $template = load_class('Template');
    $template->init_path(__FILE__);
    $template->message = $formmessage;
    $template->elements = $field;

	
	
	/* neu */
	
/* header senden */
if (!headers_sent()) {
	header(pmxHeader::Status()); 
    header('Content-type: text/html; charset=utf-8');
    header('Content-Language: ' . _DOC_LANGUAGE);
    header('X-Powered-By: pragmaMx ' . PMX_VERSION);
    header('X-UA-Compatible: IE=edge;FF=5;chrome=1');
}
echo '<html lang="'. _DOC_LANGUAGE. '" dir="'. _DOC_DIRECTION. '" >'; 
?>

<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<base href="<?php echo PMX_HOME_URL; ?>" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="language" content="<?php echo _DOC_LANGUAGE ?>" />
	<title>Admin | <?php echo pmxBase::get('sitename'); ?></title>
	<link rel="stylesheet" href="<?php echo PMX_THEMES_PATH .  'admin-coreUI'.DS.'style'.DS.'admin-login.css' ?>" type="text/css" />
</head>
	<body class="login">
		<div class="login-page">
			<div class="title"><?php echo pmxBase::get('sitename') ?></div>
			<div class="content">
				<div class="login-form"><?php $template->display('loginform.html'); ?></div>
				<a href="<?php echo PMX_HOME_URL; ?>" title="<?php echo pmxBase::get('sitename') ?>"><?php echo _HOME ?></a>
			</div>
		</div>
	</body>
</html>

<?php 
	die();
	/* neu ende */
}

/**
 * admin_login_action()
 * das eigentliche login
 *
 * @param mixed $pvs
 * @return
 */
function admin_login_action()
{
    global $prefix, $user_prefix;

    $defaults = array('aid' => '', 'pwd' => '', 'op' => '', 'check' => '', 'sess' => '', 'uri' => '');
    $pvs = array_merge($defaults, $_POST);

    $abad = intval(mxSessionGetVar('abad'));
    $pvs['check'] = (empty($pvs['check'])) ? mt_rand() : $pvs['check']; // sicherstellen, dass $check initialisiert ist
    $welcome = adminUrl();
    $real_uri = '';
    $baduri = false;

    /* Wenn Sicherheitscookie eingeschaltet, wird immer ein neues salt/hash-Paar in die DB geschrieben */
    $resetdb = (!empty($GLOBALS['vkpSafeCookie2']));
	$matches=NULL;
	
    if (!empty($pvs['uri'])) {
        $real_uri = mxSecureValue(base64_decode($pvs['uri']), true);
        if (preg_match('#^' . preg_quote(PMX_HOME_URL) . '(/.*)?#', $real_uri, $matches)) {
            if (!preg_match('#nextlogin_#', $matches[1])) {
                $welcome = $matches[1];
            }
        } else {
            $baduri = true;
        }
    }
	
    switch (true) {
        case md5(session_id()) != $pvs['sess']:
            return mxErrorScreen(_ADMIN_BADLOGIN4);
        case empty($pvs['aid']):
        case empty($pvs['pwd']):
            $msg = '<b>' . _ADMIN_BADLOGIN . '!</b><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . _ADMIN_BADLOGIN2 . '<br />';
            mxSessionSetVar('formmessage', $msg);
            return mxRedirect(adminUrl('nextlogin_' . $abad));
        case $pvs['check'] != MD5(mxSessionGetVar('reqcheck')):
        case strlen($pvs['aid']) > 25:
            return mxYoubad('Bad Adminlogin - Account: ' . $pvs['aid'] . ' (no session-check-id)', true);
        case empty($pvs['uri']):
        case $baduri === true:
            return mxYoubad('Bad Adminlogin - Account: ' . $pvs['aid'] . ' (bad Referer: ' . $real_uri . ')', false);
    }

    /* Admin- und zugehörige Userdaten aus Datenbank lesen */
    $qry = "SELECT a.aid AS aid, a.pwd AS pwd, a.pwd_salt AS pwd_salt, u.*
            FROM ${prefix}_authors AS a
                      LEFT JOIN {$user_prefix}_users AS u
                      ON a.user_uid = u.uid
            WHERE (a.aid='" . mxAddSlashesForSQL($pvs['aid']) . "');";

    $result = sql_query($qry);
    $adminrow = sql_fetch_assoc($result);

    /* Logindaten mit Datenbankwerten vergleichen */
    switch (true) {
        /* Admin-Name nicht gefunden */
        case !$adminrow:
            $loggedin = false;
            break;

        /* wenn neuer pbkdf2-Hash richtig */
        case pmx_password_verify($pvs['pwd'], $adminrow['pwd_salt'], $adminrow['pwd']):
            $loggedin = true;
            break;

        /* Check auf alten MD5-Hash ohne Salt */
        case empty($adminrow['pwd_salt']):
            /* wenn alter MD5-Hash richtig */
            if ($adminrow['pwd'] === md5($pvs['pwd'])) {
                $loggedin = true;
                /* ein neues salt/hash-Paar wird automatisch weiter unten generiert */
                $resetdb = true;
            } else {
                $loggedin = false;
            }
            break;

        default:
            $loggedin = false;
    }

    if (!$loggedin) {
        if ($abad > 3) {
            mxUserSecureLog('bad Adminlogin', 'Account: ' . $pvs['aid']);
            sleep(10); // 10 Sekunden warten
        }
        if ($abad > 5) {
            sleep(10); // und nochmal 10 Sekunden warten
        }
        if ($abad > 8) {
            mxSessionSetVar('abad', $abad -2);
            return mxYoubad('Bad Adminlogin - Account: ' . $pvs['aid'] . ' (bad ' . ($abad + 2) . '))', false);
        }
        mxSessionSetVar('abad', $abad + 1);
        $msg = '<b>' . _ADMIN_BADLOGIN . '!</b><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . _ADMIN_BADLOGIN2 . '<br />';
        mxSessionSetVar('formmessage', $msg);
        return mxRedirect(adminUrl('nextlogin_' . $abad));
    }

    if ($resetdb) {
        /* Daten aktualisieren, bei jedem Login ein neues salt/hash-Paar erstellen */
        $salt = pmx_password_salt();
        $pass = pmx_password_hash($pvs['pwd'], $salt);
        /* Neues salt/hash-Paar in Tabelle schreiben */
        sql_query("UPDATE ${prefix}_authors SET
              pwd='" . mxAddSlashesForSQL($pass) . "',
              pwd_salt='" . mxAddSlashesForSQL($salt) . "'
              WHERE aid='" . mxAddSlashesForSQL($pvs['aid']) . "'");
    } else {
        $pass = $adminrow['pwd'];
    }

    /* Daten für das Login übergeben */
    pmx_admin_setlogin($adminrow['aid'], $pass);

    /* Zurücksetzen, damit Online ausgeführt wird */
    mxSessionSetVar('lasttime', 0);

    /* aufräumen */
    mxSessionDelVar('abad');
    mxSessionDelVar('formmessage');
    mxSessionDelVar('reqcheck');

    /* zugehörigen Useraccount automatisch einloggen */
    if (empty($adminrow['uid']) || $adminrow['user_stat'] != 1) {
        $location = adminUrl();
    } else {
        /* automatisches User-Login */
        if (!function_exists('pmx_user_setlogin')) {
            require_once(PMX_SYSTEM_DIR . DS . 'mx_userfunctions.php');
        }
        pmx_user_setlogin($adminrow);

        /* Sprache einstellen */
        mxSessionSetVar('lang', $adminrow['user_lang']);
        $location = $welcome;
    }

    return mxRedirect($location);
}

/**
 * admin_dologout()
 *
 * @return
 */
function admin_dologout()
{
    mxSessionSafeCookie(MX_SAFECOOKIE_NAME_ADMIN, 0);
    //mxSetNukeCookie('admin');
    mxSessionDelVar('admin');
    if (!MX_IS_USER) {
        mxSessionDestroy();
    }
    return mxRedirect(adminUrl('logout', 'fin'));
}

/**
 * admin_dologoutfin()
 *
 * @return
 */
function admin_dologoutfin()
{
    //mxSessionDelVar('admin');
    mxSessionSetVar('lasttime', 0); // Zurücksetzen, damit Online ausgeführt wird
    return mxRedirect('index.php', _YOUARELOGGEDOUT);
}

switch (true) {
    case MX_IS_ADMIN && $op == 'logout/fin':
        return mxErrorScreen('<b>Admin-Logout failed</b><br />Please delete your Session-Cookie (' . MX_SESSION_NAME . ') manualy.');

    case MX_IS_ADMIN && $op == 'logout':
        return admin_dologout();

    case !MX_IS_ADMIN && $op == 'logout/fin':
        return admin_dologoutfin();

    case !MX_IS_ADMIN && $op == 'login':
        return admin_login_action();

    default:
        return admin_login();
}

?>