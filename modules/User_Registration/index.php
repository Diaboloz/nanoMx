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
 * $Revision: 81 $
 * $Author: PragmaMx $
 * $Date: 2015-08-19 08:59:13 +0200 (Mi, 19. Aug 2015) $
 */

defined('mxMainFileLoaded') or die('access denied');

if (!defined('mxYALoaded')) define('mxYALoaded', 1);
$module_name = basename(__DIR__);

$url_images = "images";

if (MX_IS_USER) {
    mxRedirect('modules.php?name=Your_Account');
    die();
}

mxGetLangfile('Your_Account');
require_once(PMX_SYSTEM_DIR . DS . 'mx_userfunctions.php');

/* Tabelle für private Nachrichten */
$GLOBALS['tblprivatemessage'] = $GLOBALS['prefix'] . '_priv_msgs';

$pagetitle = _REGNEWUSER;

/**
 * new_user()
 *
 * @param string $msg
 * @return
 */
function new_user($msg = '')
{
    global $prefix, $module_name;
    $userconfig = load_class('Userconfig');
    $oldvals = array();
    mt_srand((double)microtime() * 1000000);
    mxSessionSetVar("newusercheck", mt_rand());
    if (is_array(mxSessionGetVar("vkpnewuser"))) {
        $oldvals = mxSessionGetVar("vkpnewuser");
        mxSessionDelVar("vkpnewuser");
        $oldvals = mxStripSlashes($oldvals);
        /* alle Werte in das richtige Format bringen */
        $oldvals = prepareUserdataFromRequest($oldvals);
        $oldvals['user_bday'] = (empty($oldvals['birthday'])) ? '' : strftime('%Y-%m-%d', $oldvals['birthday']);
    }

    include('header.php');
    title(_USERREGLOGIN);
    userNavigation('register');
    echo '
		<div class="tabs-panel">
			<p>' . _REGISTERNOW . '
			<br />' . _WEDONTGIVE . '</p>';
    if ($userconfig->yaproofdate) {
        echo '
			<p>' . sprintf(_ERRAPPROVEDATE, $userconfig->yaproofdate) . '</p>';
    }
    echo '
		<p>' . _COOKIEWARNING . '</p>';
    if ($userconfig->pp_link) {
        pmxHeader::add_lightbox();
        if (strpos($userconfig->pp_link, '?') !== false) {
            $link = $userconfig->pp_link . '&amp;iframe=true&amp;width=100%&amp;height=100%';
        } else {
            $link = $userconfig->pp_link . '?iframe=true&amp;width=100%&amp;height=100%';
        }
    /*    echo '
			<p class="align-right">
				<a href="' . $link . '" rel="prettyPhoto[iframes]" target="_blank">&raquo; ' . _LEGALPP . '</a>
			</p>';*/
    }

    if ($msg) {
        echo '
            <div class="alert alert-warning">' . $msg . '</div>';
    }

    echo '
    <h3 class="mtm">' . _YA_ACCOUNTDATA . '</h3>
    <form name="Register" action="modules.php?name=' . $module_name . '" method="post" class="mx-form mx-form-aligned">
        <div class="control-group">
            <label for="uname">' . _NICKNAME . '</label>
            <input type="text" name="uname" id="uname" size="50" maxlength="25" value="' . ((isset($oldvals['uname'])) ? mxentityquotes($oldvals['uname']) : "") . '" placeholder="' . _NICKNAME . '" />
            <span class="mx-form-message-inline">' . _REQUIRED . '</span>
        </div>
        <div class="control-group">
            <label for="email">' . _UREALEMAIL . '</label>
            <input type="text" name="email" id="email" size="50" maxlength="100" value="' . ((isset($oldvals['email'])) ? mxentityquotes($oldvals['email']) : "") . '" placeholder="' . _UREALEMAIL . '" />
            <span class="mx-form-message-inline">' . _REQUIRED . '</span>            
        	<p class="small">' . _EMAILNOTPUBLIC . '</p>
        </div>';

    switch ($userconfig->register_option) {
        case 1:
        case 3:
        case 4:
            pmx_html_passwordchecker();
            $xpass = pmx_password_create();
            $msg = _PASSWILLSEND;
            //<p class="small">' . _OPTIONAL2 . '' . _YA_PWVORSCHLAG . ':&nbsp;' . $xpass . '</p>
            echo '
            <div class="control-group">
                <label for="pass">' . _DESIREDPASS . '</label>
                <input type="hidden" name="xpass" value="' . $xpass . '" />
                <input type="password" name="pass" id="pass" size="22" value="" class="password-checker-input" placeholder="' . _DESIREDPASS . '" />
                <input type="password" name="vpass" size="22" value="" class="password-checker-input" placeholder="Retaper ' . _DESIREDPASS . '" />
                <span class="mx-form-message-inline">' . sprintf(_OPTIONAL1, $userconfig->minpass) . '</span>             
            </div>';
            break;
        case 2:
            $msg = _YA_REG_MAILMSG2;
            break;
    }
    
    echo '
        <h3 class="mtl">' . _PERSONALINFO . '</h3>';
    echo vkpUserform($oldvals);

    if ($userconfig->agb_agree && $userconfig->agb_agree_link) {
        pmxHeader::add_lightbox();
        if (strpos($userconfig->agb_agree_link, '?') !== false) {
            $link = $userconfig->agb_agree_link . '&amp;iframe=true&amp;width=100%&amp;height=100%';
        } else {
            $link = $userconfig->agb_agree_link . '?iframe=true&amp;width=100%&amp;height=100%';
        }

        echo '
        <p>
          <input type="checkbox" name="readrules" value="1" />
          ' . _IHAVE . '
          <a href="' . $link . '" rel="prettyPhoto[iframes]" target="_blank" title="' . _LEGAL . ' ' . _SHOWIT . '">' . _LEGAL . '</a>
          ' . _READDONE . '
          <span class="small">' . _REQUIRED . '</span>
        </p>';
    }
    echo '
        <div class="controls">
		  <input type="submit" class="mx-button mx-button-primary" value="' . _NEWUSER . '" />
    		  ' . $msg . '
	      <input type="hidden" name="op" value="confirm" />
		  <input type="hidden" name="name" value="' . $GLOBALS['module_name'] . '" />
		  <input type="hidden" name="check" value="' . md5(mxsessiongetvar("newusercheck")) . '" />
        </div>
     </form>';
    echo '
		</div>'; // tabs-panel
    include('footer.php');
}

/**
 * confirmNewUser()
 *
 * @param mixed $pvs
 * @param string $msg
 * @return
 */
function confirmNewUser($pvs, $msg = '')
{
    global $module_name;

    $userconfig = load_class('Userconfig');
    $stop = '';

    $pvs['user_sexus'] = (empty($pvs['user_sexus'])) ? 0 : (int)$pvs['user_sexus'];
    foreach($pvs as $key => $value) {
        if ($key != "pass" && $key != "vpass" && $key != "xpass" && $key != "check") { // Passworte und check ausnehmen
            $value = mxPrepareCensored($value); // bad-words ausfiltern
        }
        $session[$key] = $value; // Variablen fuer session erstellen ohne stripslashes
        $pvs[$key] = mxStripSlashes($value); // Variablen fuer Anzeige erstellen mit stripslashes
    }

    $check = (empty($pvs['check'])) ? mt_rand() : $pvs['check']; // sicherstellen, dass $check initialisiert ist
    if ($check != MD5(mxSessionGetVar("newusercheck"))) {
        pmxDebug::pause();
        mxUserSecureLog('bad User-Registration', "Account: " . $pvs['uname'] . " (no session-check-id)");
        return mxRedirect("modules.php?name=" . $GLOBALS['module_name'] . "&op=new_user");
    }

    switch ($userconfig->register_option) {
        case 1:
        case 3:
        case 4:
            if (empty($pvs['pass']) || empty($pvs['vpass'])) {
                $pvs['pass'] = $pvs['xpass'];
                $pvs['vpass'] = $pvs['xpass'];
            } else if ($pvs['pass'] != $pvs['vpass']) {
                $stop = _NOTCONFIRMED;
            } else if ((strlen($pvs['pass']) < $userconfig->minpass) || (strlen($pvs['vpass']) < $userconfig->minpass)) {
                $stop = _YOUPASSMUSTBE . ' <strong>' . $userconfig->minpass . '</strong> '. _CHARLONG;
            }
            $password = $pvs['pass'];
            break;
        case 2:
        default:
            $password = pmx_password_create();
            break;
    }

    $salt = pmx_password_salt();
    $session['pass_salt'] = $salt;
    $session['pass'] = pmx_password_hash($password, $salt);
    $session['mailpass'] = $password;

    mxSessionSetVar("vkpnewuser", $session); // praeparierte Postvars in Session stellen
    if ($stop) {
        $pvs = $stop;
    } else {
        $pvs = userCheck($pvs);
    }

    if (!is_array($pvs)) {
        // $pvs enthält hier die Fehlermeldung
        return new_user($pvs);
    }

    if ($pvs['birthday']) {
        $pvs['cbday'] = mx_strftime(_SHORTDATESTRING, $pvs['birthday']);
    }

    include('header.php');
    title(_USERREGLOGIN);
    userNavigation('register');
    echo '
		<div class="tabs-panel">';
    echo '
		<form action="modules.php?name=' . $module_name . '" method="post">
			<h3>'. $pvs['uname'] . '</h3>
			<p class="mbm">' . _USERCHECKDATA . '</p>';
    if ($msg) {
        echo '
			<div class="alert alert-info">' . $msg . '</div>';
    }
    echo '
		<table class="mx-table mx-table-horizontal">

			<tr>
				<td>
                    ' . _NICKNAME . ':
                </td>
				<td>
                    <strong>' . $pvs['uname'] . '</strong>
				</td>
			</tr>';
    if ($userconfig->register_option === 1 || $userconfig->register_option === 3 || $userconfig->register_option === 4) {
        echo '
			<tr>
				<td>
                    ' . _PASSWORD . ':</td>
				<td>
                    <strong>
                        ' . $pvs['vpass'] . '
                    </strong>
				</td>
			</tr>';
    }
    if (!empty($pvs['email'])) 
			echo '
		<tr>
			<td>
                ' . _UREALEMAIL . ':
            </td>
			<td>
                <strong>' . $pvs['email'] . '</strong>
            </td>
		</tr>';
    if (function_exists("confirmNewUser_option")) {
        confirmNewUser_option($pvs);
    } else {
        if (!empty($pvs['realname'])) echo '<tr><td><b>' . _UREALNAME . ':</b></td><td class="bgcolor3">' . $pvs['realname'] . '</td></tr>';
        if (!empty($pvs['user_sexus'])) echo '<tr><td><b>' . _YA_USEXUS . ':</b></td><td class="bgcolor3">' . vkpGetSexusString($pvs['user_sexus']) . '</td></tr>';
        if (!empty($pvs['cbday'])) echo '<tr><td><b>' . _YA_UBDAY . ':</b></td><td class="bgcolor3">' . $pvs['cbday'] . '</td></tr>';
        if (!empty($pvs['url'])) echo '<tr><td><b>' . _YOURHOMEPAGE . ':</b></td><td class="bgcolor3">' . $pvs['url'] . '</td></tr>';
        if (!empty($pvs['user_icq'])) echo '<tr><td><b>' . _YICQ . ':</b></td><td class="bgcolor3">' . $pvs['user_icq'] . '</td></tr>';
        if (!empty($pvs['user_aim'])) echo '<tr><td><b>' . _YAIM . "</b></td><td class=\"bgcolor3\">" . $pvs['user_aim'] . '</td></tr>';
        if (!empty($pvs['user_yim'])) echo '<tr><td><b>' . _YYIM . ':</b></td><td class="bgcolor3">' . $pvs['user_yim'] . '</td></tr>';
        if (!empty($pvs['user_msnm'])) echo '<tr><td><b>' . _YMSNM . ':</b></td><td class="bgcolor3">' . $pvs['user_msnm'] . '</td></tr>';
        if (!empty($pvs['user_from'])) echo '<tr><td><b>' . _YLOCATION . ':</b></td><td class="bgcolor3">' . $pvs['user_from'] . '</td></tr>';
        if (!empty($pvs['user_occ'])) echo '<tr><td><b>' . _YOCCUPATION . ':</b></td><td class="bgcolor3">' . $pvs['user_occ'] . '</td></tr>';
        if (!empty($pvs['user_intrest'])) echo '<tr><td><b>' . _YINTERESTS . ':</b></td><td class="bgcolor3">' . $pvs['user_intrest'] . '</td></tr>';
        if (!empty($pvs['bio'])) echo '<tr><td><b>' . _EXTRAINFO . ':</b></td><td class="bgcolor3">' . $pvs['bio'] . '</td></tr>';
        if (!empty($pvs['user_sig'])) echo '<tr><td><b>' . _SIGNATURE . ':</b></td><td class="bgcolor3">' . $pvs['user_sig'] . '</td></tr>';
    }
    echo '
		</table>';

    $captcha_object = load_class('Captcha', 'registrationon');
    echo $captcha_object->complete();

    echo '
		<input type="hidden" name="name" value="' . $GLOBALS['module_name'] . '" />
		<input type="hidden" name="op" value="finish" />
		<input type="submit" class="mx-button mx-button-primary" value="' . _FINISH . '" /> <a href="modules.php?name=' . $module_name . '" class="mx-button">' . strip_tags(_GOBACK) . '</a>';
    if (($userconfig->register_option === 1 || $userconfig->register_option === 3 || $userconfig->register_option === 4) && empty($pvs['pass'])) {
        echo '
			<p class="small">' . _NOTPROVIDE . '</p>';
    }
    echo '
		</form>';
    echo '
		</div>'; // tabs-panel
    include('footer.php');
}

/**
 * finishNewUser()
 *
 * @return
 */
function finishNewUser()
{
    global $user_prefix, $prefix, $module_name;

    /* jedes Absenden des Formulars erhöht den Zähler */
    $postcounter = intval(mxSessionGetVar('uregistrate'));
    mxSessionSetVar('uregistrate', $postcounter + 1);

    $session = mxSessionGetVar("vkpnewuser"); // die Sessiondaten sind hier noch mit Slashes!
    $defaults = pmx_user_defaults();
    $session = array_merge($defaults, $session);

    if ($postcounter > 5) {
        /* bei mehr als 5 Versuchen ist Schluss */
        mxUserSecureLog('bad User-Registration', '5 times sent the form - Account: ' . ((empty($session['uname'])) ? 'empty()' : $session['uname']));
        return mxRedirect('./');
    }

    $userconfig = load_class('Userconfig');

    $captcha_object = load_class('Captcha', 'registrationon');

    if (!$captcha_object->check($_POST, 'captcha')) {
        return confirmNewUser($session, _CAPTCHAWRONG);
    }

    $newusercheck = mxSessionGetVar("newusercheck");
    mxSessionDelVar("newusercheck");
    $check = (empty($session["check"])) ? mt_rand() : $session["check"]; // sicherstellen, dass $check initialisiert ist
    if ($check != MD5($newusercheck)) {
        mxUserSecureLog('bad User-Registration', "(no session-check-id)");
        return mxRedirect("modules.php?name=" . $GLOBALS['module_name'] . "&op=new_user");
    }
    $session = userCheck($session);

    if (!is_array($session)) {
        return new_user($session);
    }

    $sqlvars = mxAddSlashesForSQL($session);
    extract($sqlvars);

    /* verschiedene Feldwerte mit Grundwerten belegen, bzw. auf Gueltigkeit ueberpruefen */
    $setbday = (empty($session['birthday'])) ? "NULL" : "'" . strftime('%Y-%m-%d', $session['birthday']) . "'";
    $userip = MX_REMOTE_ADDR;
    $user_regtime = time();
    $user_regdate = "";//mxGetNukeUserregdate();
    $user_ingroup = $userconfig->default_group;

    switch ($userconfig->register_option) {
        case 0:
            // generiertes Passwort zusenden
            $user_stat = 1;
            $msg1 = _YOUAREREGISTERED_0;
            $msg3 = '
				<a href="modules.php?name=Your_Account">' . _LOGIN . '</a>';
            break;
        case 1:
            // eigenes Passwort sofort freischalten
            $user_stat = 1;
            $msg1 = _YOUAREREGISTERED_1;
            $msg3 = '
				<form action="modules.php?name=' . $module_name . '" method="post">
					<input type="hidden" name="uname" value="' . $session['uname'] . '" />
					<input type="hidden" name="pass" value="' . $session['mailpass'] . '" />
						' . (mxGetUserLoginCheckField()) . '
					<input type="submit" value="' . _DIRECTLOGIN . '" />
				</form>';
            break;
        case 2:
            // generiertes Passwort zusenden, Adminfreischaltung
            mt_srand((double)microtime() * 1000000);
            $user_stat = 0;
            $pass = pmx_password_hash(mt_rand()); /// ungueltiges Passwort schreiben
            $msg1 = _YOUAREREGISTERED_2;
            $msg3 = '
				<a href="./" class="mx-button">' . _HOME . '</a>';
            break;
        case 3:
        case 4:
            // eigenes Passwort, Aktivierungslink zusenden
            $user_stat = 0;
            $msg1 = _YOUAREREGISTERED_3;
            $msg3 = '
				<a href="./" class="mx-button">' . _HOME . '</a>';
            break;
    }

    $fields[] = "`uname` = '$uname'";
    $fields[] = "`pass` = '$pass'";
    $fields[] = "`email` = '$email'";
    $fields[] = "`name` = '$realname'";
    $fields[] = "`url` = '" . mx_urltohtml(mxCutHTTP($url)) . "'";
    $fields[] = "`user_regdate` = '$user_regdate'";
    $fields[] = "`user_icq` = '$user_icq'";
    $fields[] = "`user_occ` = '$user_occ'";
    $fields[] = "`user_from` = '$user_from'";
    $fields[] = "`user_intrest` = '$user_intrest'";
    $fields[] = "`user_sig` = '$user_sig'";
    $fields[] = "`user_aim` = '$user_aim'";
    $fields[] = "`user_yim` = '$user_yim'";
    $fields[] = "`user_msnm` = '$user_msnm'";
    $fields[] = "`user_level` = 1";
    $fields[] = "`user_ingroup` = $user_ingroup";
    $fields[] = "`user_regtime` = $user_regtime";
    $fields[] = "`user_stat` = $user_stat";
    $fields[] = "`user_sexus` = $user_sexus";
    $fields[] = "`user_lastvisit` = $user_regtime";
    $fields[] = "`user_lastip` = '$userip'";
    $fields[] = "`user_bday` = $setbday"; // ohne anfuehrz.
    $fields[] = "`bio` = '$bio'";
    // ab pragmaMx 2.0
    $fields[] = "`pass_salt` = '$pass_salt'";

    if (function_exists("finishNewUser_option")) {
        // loeschen oder hinzufuegen von Insert-Elementen
        $fields = finishNewUser_option($session, $fields);
    }

    switch ($userconfig->register_option) {
        case 3:
        case 4:
            mxCheckUserTempTable(); // temporäre Tabelle auf kompatibilität pruefen und ggf. anpassen
            srand ((double)microtime() * 1000000);
            $check_key = rand(0, 32767);
            $check_time = time();
            $check_ip = MX_REMOTE_ADDR;
            $sendpass = base64_encode($session['mailpass']);
            $check_host = gethostbyaddr($userip);
            $fields[] = "check_key = $check_key";
            $fields[] = "check_time = $check_time";
            $fields[] = "check_ip = '$check_ip'";
            $fields[] = "check_host = '$check_host'";
            $fields[] = "check_thepss = '$sendpass'";
            $checkqry1 = "SELECT uid FROM {$user_prefix}_users WHERE (uname='" . $uname . "') OR (email='" . $email . "')";
            $checkqry2 = "SELECT uid FROM {$user_prefix}_users_temptable WHERE (uname='" . $uname . "') OR (email='" . $email . "')";
            $thecheckresult1 = sql_num_rows(sql_query($checkqry1));
            $thecheckresult2 = sql_num_rows(sql_query($checkqry2));
            if (($thecheckresult1) || ($thecheckresult2)) {
                mxErrorScreen(_ALREADY_EXIST);
                return;
            } else {
                mxSessionDelVar('uregistrate');
                $qry = "INSERT INTO {$user_prefix}_users_temptable SET " . implode(', ', $fields);
                $result = sql_query($qry);
                if (!$result) {
                    mxErrorScreen(_DATABASEERROR . " 101");
                    return;
                }
                mxSessionDelVar("vkpnewuser");
                $subject = $GLOBALS['sitename'] . " - " . _YA_REG_MAILSUB5 . " " . $uname;
                $buildlink = PMX_HOME_URL . "/modules.php?name=" . $module_name . "&op=a&c=" . $check_key . "&t=" . $check_time;
                $message = _YA_REG_MAILMSG5 . "\n\n" . $buildlink . "";
                mxMail($email, $subject, $message);

                include('header.php');
                title(_USERREGACT);
                userNavigation('register');
                echo '
					<div class="tabs-panel">
					<p class="bigger">' . _ACTLINKSENDED . '</p><br />' . $msg1 . '<br /><br />' . _THANKSUSER . ' ' . $GLOBALS['sitename'] . '!<br /><br />' . $msg3 ;
                if (MX_IS_ADMIN) {
                    echo '
					<hr />
					<p>The message (only for you as admin)</p>';
                    echo '
					<textarea cols="60" rows="15" style="width: 99%">' . $message . '</textarea>';
                }
                echo '
					</div>'; // tabs-panel
                include('footer.php');
                break;
            }
        default:

            mxSessionDelVar('uregistrate');
            $qry = "INSERT INTO {$user_prefix}_users SET " . implode(', ', $fields);
            $result = sql_query($qry);
            if (!$result) {
                mxErrorScreen(_DATABASEERROR);
                return;
            }
            mxSessionDelVar("vkpnewuser");

            $viewvars = mxStripSlashes($session);
            $viewvars['uid'] = sql_insert_id();

            sendnewusermail($viewvars);
            sendnewuserpm($viewvars);

            /* Modulspezifische Useranfügungen durchfuehren */
            pmx_run_hook('user.add', $viewvars['uid']);

            include('header.php');
            title(_USERREGLOGIN);
            userNavigation('register');
            echo '
				<div class="tabs-panel">
					<p class="bigger">' . _ACCOUNTCREATED . '</p>
					<p>' . $msg1 . '</p><p>' . _THANKSUSER . ' ' . $GLOBALS['sitename'] . '!</p><br /><p>' . $msg3 . '</p>
				</div>'; // tabs-panel
            include('footer.php');
            break;
    }
}

/**
 * sendnewusermail()
 *
 * @param mixed $userdata
 * @return
 */
function sendnewusermail($userdata)
{
    $userconfig = load_class('Userconfig');
    // zusaetzliche optionale Ausgaben
    $optmessage = (function_exists("sendnewusermail_option")) ? sendnewusermail_option($userdata) : "";
    extract($userdata);
    $message = _WELCOMETO . " " . $GLOBALS['sitename'] . "!\n\n";
    $message .= _YOUUSEDEMAIL . " '" . $GLOBALS['sitename'] . "' " . _TOREGISTER . ".\n\n";
    switch ($userconfig->register_option) {
        case 0:
        case 1:
            // generiertes Passwort zusenden oder eigenes Passwort sofort freischalten
            $user_stat = 1;
            $subject = _YA_REG_MAILMSG4 . " " . $uname;
            $message .= _YA_FOLLOWINGMEM . "\n\n";
            $message .= "  -" . _NICKNAME . ":\t " . $uname . "\n";
            $message .= "  -" . _PASSWORD . ":\t " . $mailpass . "\n";
            $message .= "  -" . _EMAIL . ":\t " . $email . "\n";
            $message .= $optmessage;
            break;
        case 2:
            // generiertes Passwort zusenden, Adminfreischaltung
            $user_stat = 0;
            $subject = $GLOBALS['sitename'] . " - " . _YA_REG_MAILSUB2 . " " . $uname;
            $message .= _YA_FOLLOWINGMEM . "\n\n";
            $message .= "  -" . _NICKNAME . ":\t " . $uname . "\n";
            $message .= "  -" . _EMAIL . ":\t " . $email . "\n";
            $message .= $optmessage . "\n\n" . _YA_REG_MAILMSG2;
            break;
        case 3:
            // eigenes Passwort, Aktivierungslink zusenden
            $user_stat = 0;
            $subject = _YA_REG_MAILMSG4 . " " . $uname; //Changed here for Password-Mail at Reg-Option 3
            $message .= _YA_FOLLOWINGMEM . "\n\n"; //Changed here for Password-Mail at Reg-Option 3
            $message .= "  -" . _NICKNAME . ":\t " . $uname . "\n"; //Changed here for Password-Mail at Reg-Option 3
            $message .= "  -" . _PASSWORD . ":\t " . $mailpass . "\n"; //Changed here for Password-Mail at Reg-Option 3
            $message .= "  -" . _EMAIL . ":\t " . $email . "\n"; //Changed here for Password-Mail at Reg-Option 3
            $message .= $optmessage;
            break;
    }

    $message .= "\n\n\n\n" . $GLOBALS['slogan'] . "\n-----------------------------------------------------------\n\n";
    $message .= PMX_HOME_URL . "/modules.php?name=Your_Account";

    mxMail($email, $subject, $message);

    if ($userconfig->sendaddmail && (!($userconfig->register_option === 3))) {
        $subject = _NEWMEMBERON . " " . $GLOBALS['sitename'] . ": " . $uname;
        $message = "\n" . _NEWMEMBERON . " '" . $GLOBALS['sitename'] . "'\n\n";
        $message .= " - " . _NICKNAME . ":\t " . $uname . "\n";
        $message .= " - " . _EMAIL . ":\t " . $email . "\n\n";
        $message .= " - ip:\t\t " . MX_REMOTE_ADDR . ((MX_REMOTE_HOST) ? ' (' . MX_REMOTE_HOST . ')' : '') . "\n";
        $message .= " - " . _ON . ":\t\t " . mx_strftime("%d-%m-%Y %H:%M:%S", time()) . "\n\n";
        $message .= $optmessage;
        $message .= "\n-----------------------------------------------------------\n\n";
        $message .= _NEWMEMBERINFO . ": " . PMX_HOME_URL . "/modules.php?name=Userinfo&uname=" . urlencode($uname) . "\n";
        $message .= _EDIT . ": " . PMX_HOME_URL . "/" . adminUrl("users", "modify", "chng_uid=" . $uid . "&user_stat=" . $user_stat) . "\n";
        mxMail($GLOBALS['adminmail'], $subject, $message);
    }
}

/**
 * sendnewuserpm()
 * private Nachricht an neuen Benutzer senden
 *
 * @param mixed $userdata
 * @return
 */
function sendnewuserpm($userdata)
{
    $userconfig = load_class('Userconfig');
    if (!$userconfig->sendnewusermsg || !$userconfig->msgadminid) {
        return;
    }

    /* Sprachdatei auswählen */
    mxGetLangfile(__DIR__, 'hello-*.php');

    $time = date("Y-m-d H:i");
    $subject = mxAddSlashesForSQL(mxHtmlEntityDecode(_HELLOSUBJECT1 . " " . $userdata['uname'] . ", " . _HELLOSUBJECT2 . " " . $GLOBALS['sitename']));
    $msg = mxAddSlashesForSQL(mxHtmlEntityDecode(_HELLOTEXT));

    $qry = "INSERT INTO " . $GLOBALS['tblprivatemessage'] . "
            (msg_image, subject, from_userid, to_userid, msg_time, msg_text) VALUES
            ('" . $userconfig->msgicon . "', '" . $subject . "', " . $userconfig->msgadminid . ", " . $userdata['uid'] . ", '" . $time . "', '" . $msg . "')";
    $result = sql_query($qry);
}

/**
 * account_linkactivate()
 *
 * @return
 */
function account_linkactivate()
{
    global $user_prefix, $prefix, $module_name;

    switch (true) {
        case isset($_GET['c']):
            $thekey = intval($_GET['c']);
            $thetime = (isset($_GET['t'])) ? intval($_GET['t']) : 0;
            break;
        case isset($_GET['new_check']):
            // alte Links, vor 1.12
            $thekey = intval($_GET['new_check']);
            $thetime = (isset($_GET['new_time'])) ? intval($_GET['new_time']) : 0;
            break;
        default:
            $thekey = 0;
            $thetime = 0;
    }

    mxCheckUserTempTable(); // temporäre Tabelle auf kompatibilität prüfen und ggf. anpassen
    $readable_password = '';
    $query = "SELECT * FROM {$user_prefix}_users_temptable WHERE (check_key=" . $thekey . " AND check_time = " . $thetime . ")";
    $result = sql_query($query);
    if (sql_num_rows($result) != 1) {
        mxErrorScreen(_ACTIVEORDELETED, _DATABASEERROR, false);
        return;
    }
    $row = sql_fetch_assoc($result);
    $defaults = pmx_user_defaults();
    $row = array_merge($defaults, $row);

    if ($row['check_isactive']) {
        $qry = "DELETE FROM {$user_prefix}_users_temptable WHERE (check_key=" . $thekey . " AND check_time = " . $thetime . ")";
        sql_query($qry);
        mxErrorScreen(_ALREADYACTIVE, '', false);
        return;
    }

    $sqlvars = mxAddSlashesForSQL($row);
    extract($sqlvars);

    $exist = mxGetUserDataFromUsername($uname);
    if ($exist) {
        mxErrorScreen(_ALREADYACTIVE, '', false);
        return;
    }

    /* verschiedene Feldwerte mit Grundwerten belegen, bzw. auf Gueltigkeit ueberpruefen */
    $setbday = (empty($sqlvars['user_bday'])) ? "NULL" : "'" . $sqlvars['user_bday'] . "'";
    $readable_password = mxSecureValue(base64_decode($row['check_thepss']), true);

    $userconfig = load_class('Userconfig');
    switch ($userconfig->register_option) {
        case 3:
            $user_stat = 1;
            break;
        case 4:
            $user_stat = 0;
            break;
    }

    $fields[] = "`uname` = '$uname'";
    $fields[] = "`pass` = '$pass'";
    $fields[] = "`email` = '$email'";
    $fields[] = "`name` = '$name'";
    $fields[] = "`url` = '" . mx_urltohtml(mxCutHTTP($url)) . "'";
    $fields[] = "`user_regdate` = '$user_regdate'";
    $fields[] = "`user_icq` = '$user_icq'";
    $fields[] = "`user_occ` = '$user_occ'";
    $fields[] = "`user_from` = '$user_from'";
    $fields[] = "`user_intrest` = '$user_intrest'";
    $fields[] = "`user_sig` = '$user_sig'";
    $fields[] = "`user_aim` = '$user_aim'";
    $fields[] = "`user_yim` = '$user_yim'";
    $fields[] = "`user_msnm` = '$user_msnm'";
    $fields[] = "`user_level` = 1";
    $fields[] = "`user_ingroup` = $user_ingroup";
    $fields[] = "`user_regtime` = $user_regtime";
    $fields[] = "`user_stat` = $user_stat";
    $fields[] = "`user_sexus` = $user_sexus";
    $fields[] = "`user_lastvisit` = $user_regtime";
    $fields[] = "`user_lastip` = '$user_lastip'";
    $fields[] = "`user_bday` = $setbday"; // ohne anführz.
    $fields[] = "`bio` = '$bio'";
    // ab pragmaMx 2.0
    $fields[] = "`pass_salt` = '$pass_salt'";

    if (function_exists("finishNewUser_option")) {
        // loeschen oder hinzufuegen von Insert-Elementen
        $fields = finishNewUser_option($row, $fields);
    }

    $qry = "INSERT INTO {$user_prefix}_users SET " . implode(', ', $fields);
    $result = sql_query($qry);
    if (!$result) {
        return mxErrorScreen(_DATABASEERROR . " 'Insert from temptable into usertable'");
    }

    $query = "SELECT * FROM {$user_prefix}_users WHERE uid = " . intval(sql_insert_id());
    $result = sql_query($query);
    if (sql_num_rows($result) != 1) {
        return mxErrorScreen(_ACTIVEORDELETED, _DATABASEERROR, false);
    }
    $the_needed_data = sql_fetch_assoc($result);

    /* Modulspezifische Useranfügungen durchfuehren */
    pmx_run_hook('user.add', $the_needed_data['uid']);

    sendnewuserpm($the_needed_data);

    if ($userconfig->sendaddmail) {
        $optmessage = (function_exists("sendnewusermail_option")) ? sendnewusermail_option($the_needed_data) : "";
        $subject = _NEWMEMBERON . " " . $GLOBALS['sitename'] . ": " . $uname;
        $message = "\n" . _NEWMEMBERON . " '" . $GLOBALS['sitename'] . "'\n\n";
        $message .= " - " . _NICKNAME . ":\t " . $uname . "\n";
        $message .= " - " . _EMAIL . ":\t " . $email . "\n\n";
        $message .= " - ip:\t\t " . MX_REMOTE_ADDR . ((MX_REMOTE_HOST) ? ' (' . MX_REMOTE_HOST . ')' : '') . "\n";
        $message .= " - " . _ON . ":\t\t " . mx_strftime("%d-%m-%Y %H:%M:%S", time()) . "\n\n";
        $message .= $optmessage;
        $message .= "\n-----------------------------------------------------------\n\n";
        $message .= _NEWMEMBERINFO . ": " . PMX_HOME_URL . "/modules.php?name=Userinfo&uname=" . urlencode($uname) . "\n";
        $message .= _EDIT . ": " . PMX_HOME_URL . "/" . adminUrl("users", "modify", "chng_uid=" . $the_needed_data['uid'] . "&user_stat=" . $user_stat) . "\n";
        mxMail($GLOBALS['adminmail'], $subject, $message);
    }

    switch ($userconfig->register_option) {
        case 3:
            $delqry = "DELETE FROM {$user_prefix}_users_temptable WHERE (check_key=" . $thekey . " AND check_time = " . $thetime . ")";
            $delresult = sql_query($delqry);
            if (!$delresult) {
                mxErrorScreen(_DATABASEERROR . " 'Delete temptable - entry'");
                return;
            }
            $the_needed_data['mailpass'] = $readable_password; //Changed here for Password-Mail at Reg-Option 3
            sendnewusermail($the_needed_data); //Changed here for Password-Mail at Reg-Option 3

            /* automatisches Login */
            pmx_user_setlogin($the_needed_data);

            /* Weiterleitung zu der in den Einstellungen definierten Seite */
            mxRedirect($userconfig->yastartpage, _ACTSUCCESS);
            return;
        case 4:
            $query2 = "UPDATE {$user_prefix}_users_temptable SET check_isactive = 1 WHERE (check_key=" . $thekey . " AND check_time = " . $thetime . ")";
            $result2 = sql_query($query2);
            include_once('header.php');
            userNavigation('register');
            echo '
				<div class="tabs-panel">
					<br />
					' . _ACTSUCCESS . '
					<br />
					' . _WAITFORADMINACTION . '
					<br />
					<a href="./">' . _HOME . '</a>
				</div>';
            include_once('footer.php');
            return;
    }
}

$op = (empty($_REQUEST['op'])) ? '' : $_REQUEST['op'];
switch ($op) {
    case 'new_user':
        new_user();
        break;

    case 'confirm':
        confirmNewUser($_POST);
        break;

    case 'finish':
        finishNewUser();
        break;

    case 'a':
    case 'activate_account': // für alte Links, vor 1.12
        account_linkactivate();
        break;
    default:
        new_user();
        break;
}

?>