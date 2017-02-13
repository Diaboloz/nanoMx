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
 * $Revision: 119 $
 * $Author: PragmaMx $
 * $Date: 2016-03-30 15:35:05 +0200 (Mi, 30. Mrz 2016) $
 *
 *
 */

defined('mxMainFileLoaded') or die('access denied');

if (!mxGetAdminPref('radminuser')) {
    return mxRedirect(adminUrl(), 'Access Denied');
}

/* Sprachdateien auswählen */
mxGetLangfile(__DIR__);
mxGetLangfile('Your_Account');

require_once(PMX_SYSTEM_DIR . DS . 'mx_userfunctions.php');

/**
 * mainusers()
 *
 * @return
 */
function mainusers()
{
    $userconfig = load_class('Userconfig');
    $udata['user_sexus'] = 0;
    $udata['user_stat'] = 1;
    $udata['user_ingroup'] = $userconfig->default_group;
    $udata['user_bday'] = '0000-00-00';
    $newpass = pmx_password_create();
    $options = getAllUsersSelectOptions1();
    pmx_html_passwordchecker();

    include("header.php");
    GraphicAdmin();
    title(_USERADMIN);
    OpenTable();
    echo '<fieldset><legend>' . _YA_EDITUSER . '</legend>';
    echo '<form method="post" action="' . adminUrl(PMX_MODULE, 'modify') . '" name="edit_user_form">';
    echo '<table style="margin:auto" border="0" cellspacing="1" cellpadding="3" class="bgcolor1">';
    echo '<tr class="bgcolor2"><th>' . _YA_USERSTAT . '</th><th>' . _YA_ADM_USERENAMEGROUP . '</th><th>' . _SELECTSTAT . '</th></tr>';

    if ((!empty($options[0])) && ($userconfig->register_option === 2 || $userconfig->register_option === 4)) {
        echo '<tr class="bgcolor2"><td>' . _YA_ADM_NEWUSERS . ':&nbsp;</td>';
        echo '<td class="bgcolor3"><select name="uid_0">' . implode("", $options[0]) . '</select>&nbsp;(' . count($options[0]) . ')</td>';
        echo '<td class="bgcolor3 align-center"><input type="radio" name="user_stat" value="0" /></td></tr>';
    }

    if (!empty($options[1])) {
        echo '<tr class="bgcolor2"><td>' . _YA_ADM_ACTIVUSERS . ':&nbsp;</td>';
        echo '<td class="bgcolor3"><select name="uid_1">' . implode("", $options[1]) . '</select>&nbsp;(' . count($options[1]) . ')</td>';
        echo '<td class="bgcolor3 align-center"><input type="radio" name="user_stat" value="1" checked="checked" /></td></tr>';
    }

    if (!empty($options[2])) {
        echo '<tr class="bgcolor2"><td>' . _YA_ADM_DEACTIVUSERS . ':&nbsp;</td>';
        echo '<td class="bgcolor3"><select name="uid_2">' . implode("", $options[2]) . '</select>&nbsp;(' . count($options[2]) . ')</td>';
        echo '<td class="bgcolor3 align-center"><input type="radio" name="user_stat" value="2" /></td></tr>';
    }

    if (!empty($options[-1])) {
        echo '<tr class="bgcolor2"><td>' . _YA_REAC_DELETED . ':&nbsp;</td>';
        echo '<td class="bgcolor3"><select name="uid_3">' . implode("", $options[-1]) . '</select>&nbsp;(' . count($options[-1]) . ')</td>';
        echo '<td class="bgcolor3 align-center"><input type="radio" name="user_stat" value="-1" /></td></tr>';
    }

    echo '<tr class="bgcolor2"><td colspan="3" style="text-align: center; vertical-align: bottom; height: 30px;"><input type="submit" name="umodify" value="' . _MODIFY . '" />&nbsp;&nbsp;<input type="submit" name="udelete" value="' . _DELETE . '" />';
    echo '<input type="hidden" name="op" value="' . PMX_MODULE . '/modify" /></td></tr>';
    echo '</table></form></fieldset>';
    CloseTable();
    echo '<br />';
    OpenTable();
    echo '<fieldset><legend>' . _ADDUSER . '</legend>'
     . '<form action="' . adminUrl(PMX_MODULE, 'add') . '" method="post">'
     . '<table style="margin:auto" border="0" cellspacing="1" cellpadding="3" class="bgcolor1">'
     . '<tr class="bgcolor2"><td><b>' . _NICKNAME . '</b></td>'
     . '<td class="bgcolor3"><input type="text" name="uname" size="30" maxlength="25" /> <font class="tiny">' . _REQUIRED . '</font></td></tr>'
     . '<tr class="bgcolor2"><td><b>' . _PASSWORD . '</b></td>'
     . '<td class="bgcolor3"><input type="text" name="pass" size="30" value="' . $newpass . '" class="password-checker-input" /> <font class="tiny">' . _REQUIRED . '</font></td></tr>'
     . adminuserform($udata)
     . '<tr class="bgcolor3"><td colspan="2"><input type="submit" value="' . _ADDUSERBUT . '" />'
     . '<input type="hidden" name="op" value="' . PMX_MODULE . '/add" /></td></tr>'
     . '</table></form></fieldset>';

    CloseTable();
    include('footer.php');
}

/**
 * modify()
 *
 * @return
 */
function modify()
{
    global $user_prefix, $prefix;

    if (isset($_POST['umodify']) && isset($_POST['user_stat'])) {
        /* Bearbeiten ueber Adminmenue */
        switch (true) {
            case $_POST['user_stat'] == 0 && $_POST['uid_0']:
                $chng_uid = $_POST['uid_0'];
                break;
            case $_POST['user_stat'] == 1 && $_POST['uid_1']:
                $chng_uid = $_POST['uid_1'];
                break;
            case $_POST['user_stat'] == 2 && $_POST['uid_2']:
                $chng_uid = $_POST['uid_2'];
                break;
            case $_POST['user_stat'] == -1 && $_POST['uid_3']:
                $chng_uid = $_POST['uid_3'];
                break;
            default:
                $chng_uid = 0;
        }
    } else {
        /* Bearbeiten ueber Mitgliederliste etc. */
        $chng_uid = (empty($_REQUEST['chng_uid'])) ? 0 : intval($_REQUEST['chng_uid']);
    }

    $udata = mxGetUserDataFromUid($chng_uid);
    if (empty($udata)) {
        return mxRedirect(adminUrl(PMX_MODULE), _USERNOEXIST);
    }

    switch ($udata['user_stat']) {
        case 0:
            $ptitle = _YA_ADM_NEWUSER;
            break;
        case 2:
            $ptitle = _YA_ADM_DEACTCUSER;
            break;
        case -1:
            $ptitle = _YA_REAC_DELETEDUSER;
            $udata['name'] = ''; // deleted entfernen
            break;
        case 1:
        default:
            $ptitle = _USERUPDATE;
            break;
    }

    $userconfig = load_class('Userconfig');

    switch (true) {
        case ($userconfig->register_option === 2) && ($udata['user_stat'] == 0):
            $formpass = pmx_password_create();
            $forchanges = '';
            break;

        case ($userconfig->register_option === 4) && ($udata['user_stat'] == 0):
            $querypss = "SELECT check_thepss FROM `{$user_prefix}_users_temptable` WHERE (uname='" . mxAddSlashesForSQL($udata['uname']) . "' AND email = '" . mxAddSlashesForSQL($udata['email']) . "')";
            $thequerypss_result = sql_query($querypss);
            if (!$thequerypss_result) {
                return mxErrorScreen(_DATABASEERROR . "Get the readable password --> Activationlink mod --> users.php");
            }
            $getthedata = sql_fetch_assoc($thequerypss_result);
            $formpass = base64_decode($getthedata['check_thepss']);
            $delqry1 = "DELETE FROM `{$user_prefix}_users_temptable` WHERE (uname='" . mxAddSlashesForSQL($udata['uname']) . "' AND email = '" . mxAddSlashesForSQL($udata['email']) . "')";
            $delresult1 = sql_query($delqry1);
            if (!$delresult1) {
                return mxErrorScreen(_DATABASEERROR . "Delete temptable - entry --> users.php");
            }
            $forchanges = '';
            break;

        default:
            $formpass = '';
            $forchanges = ' <span class="tiny">' . _FORCHANGES . '</span>';
    }

    unset($chng_uid, $user_stat);

    /* aktuelles Foto ermitteln */
    $pici = load_class('Userpic', $udata);
    $uploadedpic = $pici->exist();

    include('header.php');
    GraphicAdmin();
    title(_USERADMIN);
    OpenTable();
    echo "<fieldset><legend>" . $ptitle . ": <em>" . $udata['uname'] . "</em></legend>"
     . "<form action=\"" . adminUrl(PMX_MODULE, 'update') . "\" method=\"post\">"
     . "<table style=\"margin:auto\" border=\"0\" cellspacing=\"1\" cellpadding=\"3\" class=\"bgcolor1\">"
     . "<tr class=\"bgcolor2\"><td>" . _USERID . "</td>"
     . "<td class=\"bgcolor3\"><b>" . $udata['uid'] . "</b></td></tr>"
     . "<tr class=\"bgcolor2\"><td><b>" . _NICKNAME . "</b></td>"
     . "<td class=\"bgcolor3\"><b>" . $udata['uname'] . "</b><input type=\"hidden\" name=\"uname\" value=\"" . mxEntityQuotes($udata['uname']) . "\" /></td></tr>"
     . adminuserform($udata);
    if ($udata['user_stat'] != -1) {
        pmx_html_passwordchecker();
        echo "<tr class=\"bgcolor2\"><td>" . _PASSWORD . "</td>"
         . "<td class=\"bgcolor3\"><input type=\"password\" name=\"pass\" value=\"" . $formpass . "\" size=\"30\" class=\"password-checker-input\" />" . $forchanges . "</td></tr>"
         . "<tr class=\"bgcolor2\"><td>" . _RETYPEPASSWD . "</td>"
         . "<td class=\"bgcolor3\"><input type=\"password\" name=\"pass2\" value=\"" . $formpass . "\" size=\"30\" class=\"password-checker-input\" />" . $forchanges . "</td></tr>"
         . "<tr class=\"bgcolor3\"><td colspan=\"2\">
        <input type=\"submit\" value=\"" . _SAVECHANGES . "\" />
        </td></tr>";
    } else {
        echo "<tr class=\"bgcolor3\"><td colspan=\"2\">
        <input type=\"hidden\" name=\"user_stat\" value=\"1\" />
        <input type=\"submit\" name=\"ureactivate\" value=\"" . _REACTIVATEUSER . "\" />
        </td></tr>";
    }

    echo "</table>"
     . "<input type=\"hidden\" name=\"chng_uid\" value=\"" . $udata['uid'] . "\" />"
     . "<input type=\"hidden\" name=\"old_user_stat\" value=\"" . $udata['user_stat'] . "\" />"
     . "<input type=\"hidden\" name=\"op\" value=\"" . PMX_MODULE . "/update\" />"
     . "</form></fieldset>";

    if ($uploadedpic) {

        ?>
<fieldset>
    <legend><?php echo _UPIC_UPLOADED ?></legend>
    <img alt="uploaded" src="<?php echo $uploadedpic ?>"/>
    &nbsp;&nbsp;<a href="modules.php?name=Your_Account&amp;op=deluserpic&amp;uid=<?php echo $udata['uid'] ?>" class="button hidden" id="upicdelete"><?php echo _UPIC_DELETEIMG ?></a>
    <noscript>
        <a class="button" href="#" title="<?php echo _JSSHOULDBEACTIVE ?>"><?php echo _UPIC_DELETEIMG ?></a><br /><span class="tiny">(<?php echo _JSSHOULDBEACTIVE ?>)</span>
    </noscript>
</fieldset>
<script type="text/javascript">
/*<![CDATA[*/
  $(document).ready(function() {
    $('#upicdelete').removeClass('hidden');
  });
  $('#upicdelete').click(function() {
    return confirm('<?php echo addslashes(_UPIC_SUREDELETE) ?>');
  });
/*]]>*/
</script>
    <?php
    }

    CloseTable();
    include('footer.php');
}

/**
 * update()
 *
 * @param mixed $pvs
 * @return
 */
function update($pvs)
{
    global $user_prefix, $prefix;

    $userconfig = load_class('Userconfig');
    // $proofdate = $userconfig->yaproofdate;
    // # Check des Geburtsdatums beim reaktivieren abschalten
    // if ($userconfig->yaproofdate && isset($pvs['ureactivate']) && isset($pvs['old_user_stat'])) {
    // $userconfig->yaproofdate = 0;
    // }
    $pvs = userCheck($pvs);
    // $userconfig->yaproofdate = $proofdate;
    switch (true) {
        case !is_array($pvs):
            return mxErrorScreen($pvs);
        case (!empty($pvs['pass'])) && ($pvs['pass'] != $pvs['pass2']):
        case (!empty($pvs['pass2'])) && ($pvs['pass'] != $pvs['pass2']):
            return mxErrorScreen(_PASSWDNOMATCH);
        case (!empty($pvs['pass'])) && (strlen($pvs['pass']) < $userconfig->minpass):
            return mxErrorScreen(_YOUPASSMUSTBE . " <b>" . $userconfig->minpass . "</b> " . _CHARLONG);
    }

    $pvs = mxAddSlashesForSQL($pvs);

    extract($pvs);
    $user_ingroup = (empty($user_ingroup)) ? $userconfig->default_group : $user_ingroup;
    $user_sexus = (empty($user_sexus)) ? 0 : (int)$user_sexus;
    $user_stat = (empty($user_stat)) ? 0 : (int)$user_stat;
    $old_user_stat = (empty($old_user_stat)) ? 0 : (int)$old_user_stat;
    // wenn user_stat auf 0 gesetzt wird aber vorher einen anderen Wert hatte, user_stat auf deaktiviert setzen (2)
    $user_stat = ($user_stat != $old_user_stat && $old_user_stat != 0 && $user_stat == 0) ? 2 : $user_stat;

    $setbday = (empty($birthday)) ? "NULL" : "'" . strftime('%Y-%m-%d', $birthday) . "'";

    if (!empty($pass2)) {
        if ($user_stat == 0 && $old_user_stat == 0 && ($userconfig->register_option === 2 || $userconfig->register_option === 4)) {
        } else {
            $salt = pmx_password_salt();
            $pwd = pmx_password_hash($pass2, $salt);
            $fields[] = "`pass` = '" . mxAddSlashesForSQL($pwd) . "'";
            $fields[] = "`pass_salt` = '" . mxAddSlashesForSQL($salt) . "'";
        }
    }

    $fields[] = "`name` = '$name'";
    $fields[] = "`email` = '$email'";
    $fields[] = "`url` = '" . mx_urltohtml(mxCutHTTP($url)) . "'";
    $fields[] = "`user_sexus` = $user_sexus";
    $fields[] = "`user_icq` = '$user_icq'";
    $fields[] = "`user_aim` = '$user_aim'";
    $fields[] = "`user_yim` = '$user_yim'";
    $fields[] = "`user_msnm` = '$user_msnm'";
    $fields[] = "`user_from` = '$user_from'";
    $fields[] = "`user_occ` = '$user_occ'";
    $fields[] = "`user_intrest` = '$user_intrest'";
    $fields[] = "`bio` = '$bio'";
    $fields[] = "`user_sig` = '$user_sig'";
    $fields[] = "`user_ingroup` = $user_ingroup";
    $fields[] = "`user_stat` = $user_stat";
    $fields[] = "`user_bday` = $setbday"; // ohne anführz.
    if (isset($pvs['ureactivate']) && $old_user_stat == -1) {
        $fields[] = "`user_regdate` = ''"; //" . mxGetNukeUserregdate(time()) . "
        $fields[] = "`user_regtime` = " . time();
    }

    $qry = "UPDATE `{$user_prefix}_users` SET " . implode(', ', $fields) . " WHERE uid=" . intval($chng_uid);
    $result = sql_query($qry);

    if ($result) {
        if ($user_stat == 1 && $old_user_stat == 0 && ($userconfig->register_option === 2 || $userconfig->register_option === 4)) {
            $subject = _YA_REG_MAILMSG4 . " " . $uname;
            $message = _HELLO . " " . $uname . "\n\n";
            $message .= _YA_REG_MAILMSG3 . "\n\n";
            $message .= "  -" . _NICKNAME . ":\t " . $uname . "\n";
            $message .= "  -" . _PASSWORD . ":\t " . $pass . "\n";
            $message .= "\n\n" . $GLOBALS['slogan'] . "\n-----------------------------------------------------------\n\n";
            $message .= PMX_HOME_URL . "/modules.php?name=Your_Account\n\n\n\n\n\n\n\n\npowered by: pragmaMx " . PMX_VERSION . " (http://www.pragmaMx.org/)";
            mxMail($email, $subject, $message);
        }

        /* Modulspezifische Useränderungen durchfuehren */
		$vuid= $chng_uid; // weil $vuid als Array zurückkommt...
        pmx_run_hook('user.edit', $vuid);
    } else {
        return mxErrorScreen(_DATABASEERROR . '<br /><br />' . $qry);
    }

    switch (true) {
        case isset($pvs['ureactivate']) && $old_user_stat == -1:
            $udata = mxGetUserDataFromUid($chng_uid);
            $message = sprintf(_YA_REAC_MESSAGETEXT, $udata['uname'], $GLOBALS['sitename']);
            $message .= "\n\n\n---------------------------------------------------------------------------\n" . _PASSWORDLOST . "\n" . PMX_HOME_URL . "/modules.php?name=Your_Account&amp;op=pass_lost";
            include('header.php');
            title(_USERADMIN);
            OpenTable();
            echo '<center><p class="note stronger">' . sprintf(_YA_REAC_RESULTOK, htmlspecialchars($uname)) . '</p><br /><br />
             <p><b>' . sprintf(_YA_REAC_SENDMESSAGE, htmlspecialchars($uname)) . '</b></p><br />
             <form action="' . adminUrl(PMX_MODULE, 'reactivate') . '" method="post">
                <p>' . _YA_REAC_EDITMSGTEXT . '</p>
                <textarea cols="75" rows="8" name="message">' . $message . '</textarea><br /><br />
                <input type="hidden" name="op" value="' . PMX_MODULE . '/reactivate" />
                <input type="hidden" name="uid" value="' . $chng_uid . '" />
                <input type="submit" name="sendmail" value="' . _YES . '" /> &nbsp;
                <input type="submit" value="' . _NO . '" />
             </form></center>';
            CloseTable();
            include('footer.php');
            break;

        case $user_stat != $old_user_stat:
            $location = adminUrl(PMX_MODULE);
            return mxRedirect($location, _CHANGESAREOK);

        default:
            $location = adminUrl(PMX_MODULE, 'modify', 'chng_uid=' . $chng_uid); # . '&amp;user_stat=' . $user_stat;
            return mxRedirect($location, _CHANGESAREOK);
    }
}

/**
 * add()
 *
 * @param mixed $pvs
 * @return
 */
function add($pvs)
{
    global $user_prefix, $prefix;

    $userconfig = load_class('Userconfig');

    $pvs = userCheck($pvs);

    switch (true) {
        case !is_array($pvs):
            return mxErrorScreen($pvs);
        case mxCheckNickname($pvs['uname']) !== true:
            return mxErrorScreen(mxCheckNickname($pvs['uname']));
        case empty($pvs['pass']) || (strlen($pvs['pass']) < $userconfig->minpass):
            return mxErrorScreen(_YOUPASSMUSTBE . " <b>" . $userconfig->minpass . "</b> " . _CHARLONG);
    }

    $fields = array();

    $salt = pmx_password_salt();
    $pwd = pmx_password_hash($pvs['pass'], $salt);
    $fields[] = "`pass` = '" . mxAddSlashesForSQL($pwd) . "'";
    $fields[] = "`pass_salt` = '" . mxAddSlashesForSQL($salt) . "'";

    $pvs = mxAddSlashesForSQL($pvs);
    extract($pvs);
    $uname = trim($uname);

    $result = sql_query("SELECT uid FROM `{$user_prefix}_users` WHERE uname='" . mxAddSlashesForSQL($uname) . "'");
    list($new_uid) = sql_fetch_row($result);
    if (!empty($new_uid)) {
        return mxErrorScreen(_USERYESEXIST);
    }

    $user_ingroup = (empty($user_ingroup)) ? $userconfig->default_group : $user_ingroup;
    $fields[] = "`user_ingroup` = $user_ingroup";
    $user_sexus = (empty($user_sexus)) ? 0 : (int)$user_sexus;
    $fields[] = "`user_sexus` = $user_sexus";
    $user_stat = (empty($user_stat)) ? 0 : (int)$user_stat;
    $fields[] = "`user_stat` = $user_stat";

    $setbday = (empty($birthday)) ? "NULL" : "'" . strftime('%Y-%m-%d', $birthday) . "'";
    $fields[] = "`user_bday` = $setbday";

    $user_regdate = "";//mxGetNukeUserregdate();
    $user_regtime = time();
    $fields[] = "`user_regtime` = $user_regtime";

    $fields[] = "`name` = '$name'";
    $fields[] = "`uname` = '$uname'";
    $fields[] = "`email` = '$email'";
    $fields[] = "`url` = '" . mx_urltohtml(mxCutHTTP($url)) . "'";
    $fields[] = "`user_regdate` = '$user_regdate'";
    $fields[] = "`user_icq` = '$user_icq'";
    $fields[] = "`user_aim` = '$user_aim'";
    $fields[] = "`user_yim` = '$user_yim'";
    $fields[] = "`user_msnm` = '$user_msnm'";
    $fields[] = "`user_from` = '$user_from'";
    $fields[] = "`user_occ` = '$user_occ'";
    $fields[] = "`user_intrest` = '$user_intrest'";
    $fields[] = "`bio` = '$bio'";
    $fields[] = "`user_sig` = '$user_sig'";

    $qry = "INSERT INTO `{$user_prefix}_users` SET " . implode(', ', $fields);
    $result = sql_query($qry);
    if (!$result) {
        return mxErrorScreen('Database error, cannot add to users-table <br />(' . sql_error() . ')', "Error");
    }
    $uid = sql_insert_id();

    /* Modulspezifische Useranfügungen durchfuehren */
    pmx_run_hook('user.add', $uid);

    mxRedirect(adminUrl(PMX_MODULE, 'modify', 'chng_uid=' . $uid . '&user_stat=' . $user_stat), _CHANGESAREOK);
}

/**
 * delete()
 *
 * @return
 */
function delete()
{
    global $user_prefix, $prefix;

    if (isset($_POST['udelete']) && isset($_POST['user_stat'])) {
        /* Loeschen ueber Adminmenue */
        switch (true) {
            case $_POST['user_stat'] == 0 && $_POST['uid_0']:
                $chng_uid = $_POST['uid_0'];
                break;
            case $_POST['user_stat'] == 1 && $_POST['uid_1']:
                $chng_uid = $_POST['uid_1'];
                break;
            case $_POST['user_stat'] == 2 && $_POST['uid_2']:
                $chng_uid = $_POST['uid_2'];
                break;
            case $_POST['user_stat'] == -1 && $_POST['uid_3']:
                $chng_uid = $_POST['uid_3'];
                break;
            default:
                $chng_uid = 0;
        }
    } else {
        /* Loeschen ueber Mitgliederliste etc. */
        $chng_uid = (empty($_REQUEST['chng_uid'])) ? 0 : intval($_REQUEST['chng_uid']);
    }

    $udata = mxGetUserDataFromUid($chng_uid);

    if (empty($udata['uid']) || $udata['user_stat'] == -1 || $udata['uid'] === 1) {
        return mxRedirect(adminUrl(PMX_MODULE), _USERNOEXIST);
    }

    include('header.php');
    title(_USERADMIN);
    OpenTableAl();
    echo '<center><p class="option">' . _DELETEUSER . '</p>
     <p>' . _SURE2DELETE . '&nbsp;<b>' . htmlspecialchars($udata['uname']) . '</b>?</p><br />
     <form action="' . adminUrl(PMX_MODULE, 'delete_confirm') . '" method="post">
        <input type="hidden" name="op" value="' . PMX_MODULE . '/delete_confirm" />
        <input type="hidden" name="del_uid" value="' . $udata['uid'] . '" />
        <input type="submit" name="action" value="' . _YES . '" /> &nbsp;
        <input type="submit" value="' . _NO . '" />
     </form></center>';
    CloseTableAl();
    include('footer.php');
}

/**
 * delete_confirm()
 *
 * @param mixed $pvs
 * @return
 */
function delete_confirm($pvs)
{
    global $user_prefix, $prefix;

    extract($pvs);
    if (!isset($pvs['action']) || $pvs['action'] != _YES) {
        mainusers();
        return;
    }

    $uid = (empty($del_uid)) ? 0 : intval($del_uid);
    if (!$uid || $uid === 1) {
        return mxRedirect(adminUrl(PMX_MODULE), _USERNOEXIST);
    }

    /* Modulspezifische Loeschungen durchfuehren */
    pmx_run_hook('user.delete', $uid);

    mxRedirect(adminUrl(PMX_MODULE), _DELETEAREOK);
}

/**
 * adminuserform()
 *
 * @param array $udata
 * @return
 */
function adminuserform($udata = array())
{
    global $user_prefix, $prefix;
    $udata['user_age'] = (empty($udata['user_age'])) ? '?' : $udata['user_age'];

    $out = ""
     . "<tr class=\"bgcolor2\"><td><b>" . _EMAIL . "</b></td>"
     . "<td class=\"bgcolor3\"><input type=\"text\" name=\"email\" value=\"" . ((empty($udata['email'])) ? "" : htmlspecialchars($udata['email'])) . "\" size=\"60\" maxlength=\"100\" /> <font class=\"tiny\">" . _REQUIRED . "</font></td></tr>";
    if ($udata['user_stat'] != -1) {
        $out .= "<tr class=\"bgcolor2\"><td><b>" . _YA_USERSTAT . "</b></td>"
         . "<td class=\"bgcolor3\"><select name=\"user_stat\">" . getUserStatOptions($udata['user_stat']) . "</select><font class=\"tiny\">" . _REQUIRED . "</font></td></tr>";
    }
    $out .= "<tr class=\"bgcolor2\"><td><b>" . _YA_INGROUP . "</b></td>"
     . "<td class=\"bgcolor3\"><select name=\"user_ingroup\">" . getAllAccessLevelSelectOptions($udata['user_ingroup']) . "</select><font class=\"tiny\">" . _REQUIRED . "</font></td></tr>"
     . "<tr class=\"bgcolor2\"><td>" . _NAME . "</td>"
     . "<td class=\"bgcolor3\"><input type=\"text\" name=\"name\" value=\"" . ((empty($udata['name'])) ? "" : htmlspecialchars($udata['name'])) . "\" size=\"60\" maxlength=\"60\" /></td></tr>"
     . "<tr class=\"bgcolor2\"><td>" . _YA_USEXUS . "</td>"
     . "<td class=\"bgcolor3\">" . vkpSexusSelect('user_sexus', $udata['user_sexus']) . "</td></tr>"
     . "<tr class=\"bgcolor2\"><td>" . _YA_UBDAY . "</td>"
     . "<td class=\"bgcolor3\">" . vkpBdaySelect($udata['user_bday']) . " (" . $udata['user_age'] . '&nbsp;' . _YEARS . ")</td></tr>"
     . "<tr class=\"bgcolor2\"><td>" . _URL . "</td>"
     . "<td class=\"bgcolor3\"><input type=\"text\" name=\"url\" value=\"" . ((empty($udata['url'])) ? "" : htmlspecialchars($udata['url'])) . "\" size=\"60\" maxlength=\"255\" /></td></tr>"
     . "<tr class=\"bgcolor2\"><td>" . _ICQ . "</td>"
     . "<td class=\"bgcolor3\"><input type=\"text\" name=\"user_icq\" value=\"" . ((empty($udata['user_icq'])) ? "" : htmlspecialchars($udata['user_icq'])) . "\" size=\"60\" maxlength=\"50\" /></td></tr>"
     . "<tr class=\"bgcolor2\"><td>" . _AIM . "</td>"
     . "<td class=\"bgcolor3\"><input type=\"text\" name=\"user_aim\" value=\"" . ((empty($udata['user_aim'])) ? "" : htmlspecialchars($udata['user_aim'])) . "\" size=\"60\" maxlength=\"100\" /></td></tr>"
     . "<tr class=\"bgcolor2\"><td>" . _YIM . "</td>"
     . "<td class=\"bgcolor3\"><input type=\"text\" name=\"user_yim\" value=\"" . ((empty($udata['user_yim'])) ? "" : htmlspecialchars($udata['user_yim'])) . "\" size=\"60\" maxlength=\"100\" /></td></tr>"
     . "<tr class=\"bgcolor2\"><td>" . _MSNM . "</td>"
     . "<td class=\"bgcolor3\"><input type=\"text\" name=\"user_msnm\" value=\"" . ((empty($udata['user_msnm'])) ? "" : htmlspecialchars($udata['user_msnm'])) . "\" size=\"60\" maxlength=\"100\" /></td></tr>"
     . "<tr class=\"bgcolor2\"><td>" . _LOCATION . "</td>"
     . "<td class=\"bgcolor3\"><input type=\"text\" name=\"user_from\" value=\"" . ((empty($udata['user_from'])) ? "" : htmlspecialchars($udata['user_from'])) . "\" size=\"60\" maxlength=\"100\" /></td></tr>"
     . "<tr class=\"bgcolor2\"><td>" . _OCCUPATION . "</td>"
     . "<td class=\"bgcolor3\"><input type=\"text\" name=\"user_occ\" value=\"" . ((empty($udata['user_occ'])) ? "" : htmlspecialchars($udata['user_occ'])) . "\" size=\"60\" maxlength=\"100\" /></td></tr>"
     . "<tr class=\"bgcolor2\"><td>" . _INTERESTS . "</td>"
     . "<td class=\"bgcolor3\"><input type=\"text\" name=\"user_intrest\" value=\"" . ((empty($udata['user_intrest'])) ? "" : htmlspecialchars($udata['user_intrest'])) . "\" size=\"60\" maxlength=\"150\" /></td></tr>"
     . "<tr class=\"bgcolor2\"><td>" . _EXTRAINFO . "</td>"
     . "<td class=\"bgcolor3\"><textarea name=\"bio\" rows=\"6\" cols=\"60\">" . ((empty($udata['bio'])) ? "" : htmlspecialchars($udata['bio'])) . "</textarea></td></tr>"
     . "<tr class=\"bgcolor2\"><td>" . _SIGNATURE . "</td>"
     . "<td class=\"bgcolor3\"><textarea name=\"user_sig\" rows=\"6\" cols=\"60\">" . ((empty($udata['user_sig'])) ? "" : htmlspecialchars($udata['user_sig'])) . "</textarea></td></tr>";
    return $out;
    // ToDo: Die Textareas sollten mittels JavaScript eine (angezeigte = Restzeichen) Laengenbegrenzung haben (wie im SMF-Profil).
}

/**
 * reactivate()
 *
 * @return
 */
function reactivate()
{
    if (!isset($_POST['sendmail']) || $_POST['sendmail'] != _YES) {
        return mxRedirect(adminUrl(PMX_MODULE));
    }

    $udata = mxGetUserDataFromUid($_POST['uid']);
    if (empty($udata)) {
        return mxRedirect(adminUrl(PMX_MODULE), _USERNOEXIST);
    }

    if (empty($_POST['message'])) {
        return mxRedirect(adminUrl(PMX_MODULE), sprintf(_YA_REAC_SENDMSGERR, $udata['uname']), 5);
    }

    $subject = sprintf(_YA_REAC_MSGSUBJECT, $GLOBALS['sitename']);
    $message = mxStripSlashes($_POST['message']);

    $ok = mxMail($udata['email'], $subject, $message);
    if ($ok) {
        /* Modulspezifische Useränderungen durchfuehren */
        pmx_run_hook('user.reactivate', $chng_uid);
        return mxRedirect(adminUrl(PMX_MODULE), sprintf(_YA_REAC_SENDMSGOK, $udata['uname']));
    } else {
        return mxRedirect(adminUrl(PMX_MODULE), sprintf(_YA_REAC_SENDMSGERR, $udata['uname']), 8);
    }
}

/**
 * getAllUsersSelectOptions1()
 *
 * @return
 */
function getAllUsersSelectOptions1()
{
    global $user_prefix, $prefix;

    $useroptions = array();
    $qry = "SELECT u.uname, u.uid, u.user_ingroup, u.user_stat, ga.access_title
        FROM `{$user_prefix}_users` AS u
        LEFT JOIN `${prefix}_groups_access` AS ga
        ON u.user_ingroup = ga.access_id
        WHERE u.uname <> 'Anonymous'
        ORDER BY u.uname";
    $result = sql_query($qry);
    if ($result) {
        while (list($uname, $uid, $level, $stat, $grp) = sql_fetch_row($result)) {
            $view = (empty($grp) || $level == 1) ? $uname : $uname . '&nbsp;&raquo;&nbsp;' . $grp;
            $useroptions[$stat][] = '<option value="' . $uid . '">' . $view . '</option>';
        }
    }

    $useroptions = (count($useroptions)==0) ? '<option value="0">No Users available</option>' : $useroptions;
    return $useroptions;
}

/**
 * getUserStatOptions()
 *
 * @param mixed $user_stat
 * @return
 */
function getUserStatOptions($user_stat)
{
    $out = "";
    if (empty($user_stat)) {
        // nur Anzeigen bei neuen Usern
        $out .= "<option value=0" . (($user_stat == 0) ? ' selected="selected" class="current"' : '') . ">" . _YA_USERSTAT_0 . "</option>\n"; # neu, noch nicht aktiviert
    }
    // immer anzeigen
    $out .= "<option value=1" . (($user_stat == 1) ? ' selected="selected" class="current"' : '') . ">" . _YA_USERSTAT_1 . "</option>\n"; # aktiviert
    if (!empty($user_stat)) {
        // nicht Anzeigen bei neuen Usern
        $out .= "<option value=2" . (($user_stat == 2) ? ' selected="selected" class="current"' : '') . ">" . _YA_USERSTAT_2 . "</option>\n"; # deaktiviert
    }
    return $out;
}

switch ($op) {
    case PMX_MODULE . '/modify':
        if (isset($_POST['udelete'])) {
            delete();
        } else {
            modify();
        }
        break;

    case PMX_MODULE . '/update':
        update($_POST);
        break;

    case PMX_MODULE . '/delete':
        delete();
        break;

    case PMX_MODULE . '/delete_confirm':
        delete_confirm($_POST);
        break;

    case PMX_MODULE . '/add':
        add($_POST);
        break;

    case PMX_MODULE . '/reactivate':
        reactivate();
        break;

    default:
        mainusers();
        break;
}

?>