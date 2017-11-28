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

/* Konfiguration: Anzahl der Eintraege pro Seite */
$entries = 8;

/* Konfiguration: Wartezeit, bis die Neueingaenge in der Liste erscheinen, in Stunden. */
$wait_time = 1;

if (!mxGetAdminPref('radminuser')) {
    return mxRedirect(adminUrl(), 'Access Denied');
}

/* Sprachdatei auswählen */
mxGetLangfile(__DIR__);

if (!defined('mxYALoaded')) {
    define('mxYALoaded', '1');
}
include_once(PMX_SYSTEM_DIR . DS . 'mx_userfunctions.php');

/* Seitennavigation */
function prevnext($menge, $counter, $int_counter)
{
    global $entries;
    echo '
    	<table>
    		<tr>';
    If (($counter == 0) && ($menge > ($counter + $entries))) {
        echo '
        	<td>&nbsp;</td>';
        echo "<td><a href=\"" . adminUrl(PMX_MODULE, '', "counter=" . ($counter + $entries), '') . "\">" . _GONEXT . " " . $entries . " &#62;&#62;&#62;</a></td>";
    } elseif (($counter >= $entries) && ($menge > ($counter + $entries))) {
        echo "<td><a href=\"" . adminUrl(PMX_MODULE, '', "counter=" . ($counter - $entries), '') . "\">&#60;&#60;&#60; " . _GOPREV . " " . $entries . "</a></td>";
        echo "<td><a href=\"" . adminUrl(PMX_MODULE, '', "counter=" . ($counter + $entries), '') . "\">" . _GONEXT . " " . $entries . " &#62;&#62;&#62;</a></td>";
    } elseif (($counter >= $entries) && ($menge <= ($counter + $entries))) {
        echo "<td><a href=\"" . adminUrl(PMX_MODULE, '', "counter=" . ($counter - $entries), '') . "\">&#60;&#60;&#60; " . _GOPREV . " " . $entries . "</td>";
        echo "<td>&nbsp;</td>";
    }
    echo '
    		</tr>
    	</table>';
}

/* Die Liste */
function viewthelist($counter = 0)
{
    global $user_prefix, $entries;
    $timeborder = time();
    $query_m = "SELECT * FROM {$user_prefix}_users_temptable WHERE (check_isactive!=1)";
    $result_m = sql_query($query_m);
    $query = "SELECT * FROM {$user_prefix}_users_temptable WHERE (check_isactive!=1) LIMIT $counter, $entries";
    $result1 = sql_query($query);
    if (!$result1) {
        include("header.php");
        echo '
        	<div class="alert alert-error text-center">
        		Error query 101
        	</div>';
        include("footer.php");
        die();
    }
    $menge = (int)sql_num_rows($result_m);
    include('header.php');
    title(_YADTITLE);
    $check = time() - (60 * 60 * $GLOBALS['wait_time']);
    if ($menge > 0) {
        echo '
        	<form name="delete_selected" action="' . adminUrl(PMX_MODULE) . '" method="post">
        		<table class="table table-sm">
        			<thead class="thead-default">
        				<tr>
        					<th>' . _YADMODIFY . '</th>
        					<th>' . _YADENTRYNAME . '</th>
        					<th>' . _YADENTRYEMAIL . '</th>
        					<th>' . _YADENTRYTIME . '</th>
        				</tr>
        			</thead>';
        $internal_counter = 0;
        while ($eintrag = sql_fetch_assoc($result1)) {
            $zeit = mx_strftime(_SHORTDATESTRING, $eintrag['check_time']) . ' ' . date('H:i', $eintrag['check_time']);
            $internal_counter ++;
            if ($eintrag['check_time'] > $check) {
                echo '
                	<tr>
                		<td>&nbsp;</td>';
            } else {
                echo '
                	<tr>
                		<td>
                			<input type="checkbox" name="check_delete[]" value="' . $eintrag['uid'] . '" />
                		</td>';
            }
            if ($eintrag['check_ip'] == "255.255.255.255") {
                $color = '<span class="badge badge-pill badge-primary">';
            } else {
                $color = '<span class="badge badge-pill badge-default">';
            }
            echo '
            	<td>' . $color . $eintrag['uname'] . '</span></td>';
            echo '
            	<td>' . $color . $eintrag['email'] . '</span></td>';
            echo '
            	<td>' . $color . $zeit . '</span></td>
            </tr>';
        }

        echo '
        	</table>

        	<input type="hidden" name="counter" value="' . $counter . '" />
        	<input type="hidden" name="internal_counter" value="' . $internal_counter . '" />

          <div class="form-group">
            <label class="col-sm-2 col-form-label"><?php echo _YADAACTION ?></label>
            <div class="col-sm-3">
              <select class="custom-select name="op">
 				        <option value="' . PMX_MODULE . '/resend">' . _YADADMINRESEND . '</option>
        				<option value="' . PMX_MODULE . '/activate">' . _YADACTIVATE . '</option>
        				<option value="' . PMX_MODULE . '/delete">' . _YADDELDO . '</option>
              </select>
            </div>
          </div>

  
        	<input type="submit" class="btn btn-primary" name="submit" value="' . _SUBMIT . '" />
  
      </form>';

        prevnext($menge, $counter, $internal_counter);
    } else {
        echo _YADNOENTRY . "<br />\n";
    }
    include("footer.php");
}

/* Eintraege loeschen */
function delete_the_requested($counter, $int_counter, $uid_array)
{
    global $user_prefix, $entries;
    $internal_counter = 0;
    foreach ($uid_array as $key => $delete_uid) {
        $delete_uid = intval($delete_uid);
        $internal_counter++;
        if (!$result = sql_query("DELETE FROM {$user_prefix}_users_temptable WHERE uid=" . $delete_uid)) {
            include('header.php');
            echo "<center>Error delete Entry 202</center>";
            include('footer.php');
            die();
        }
    }
    if ($internal_counter == $int_counter) {
        $counter = ($counter - $entries);
    }
    if ($counter < 0) {
        $counter = 0;
    }
    mxRedirect(adminUrl(PMX_MODULE, '', "counter=$counter"));
}

function adminsendusermail($counter, $int_counter, $uid_array)
{
    global $user_prefix, $entries;
    $internal_counter = 0;
    foreach ($uid_array as $key => $resend_uid) {
        $resend_uid = intval($resend_uid);
        $internal_counter++;
        if (!$result = sql_query("SELECT * FROM {$user_prefix}_users_temptable WHERE uid=" . $resend_uid)) {
            include("header.php");
            echo "<center>Error Admin Link resend Entry 451</center>";
            include("footer.php");
            die();
        } else {
            $row = sql_fetch_assoc($result);
            extract($row);
            $subject = $GLOBALS['sitename'] . " - " . _YADREG_MAILSUB5 . " " . $uname;
            $message = _YADREG_MAILMSG5;
            $buildlink = PMX_HOME_URL . "/modules.php?name=User_Registration&op=a&c=" . $check_key . "&t=" . $check_time;
            mxMail($email, $subject, $message . "\n\n" . $buildlink);
            sql_query("UPDATE {$user_prefix}_users_temptable SET check_ip = '255.255.255.255' WHERE uid=" . $resend_uid);
        }
    }
    if ($internal_counter == $int_counter) {
        $counter = ($counter - $entries);
    }
    if ($counter < 0) {
        $counter = 0;
    }
    mxRedirect(adminUrl(PMX_MODULE, '', "counter=$counter"));
}

/* PROTOTYPE Activate Account */
function adminactivateaccount()
{
    global $user_prefix, $prefix;

    $uid_array = $_POST['check_delete'];
    $counter = intval($_POST['counter']);
    $int_counter = intval($_POST['internal_counter']);

    $userconfig = load_class('Userconfig');

    mxCheckUserTempTable(); // temporäre Tabelle auf kompatibilität prüfen und ggf. anpassen
    $internal_counter = 0;
    foreach ($uid_array as $key => $resend_uid) {
        $internal_counter++;

        $result = sql_query("SELECT * FROM {$user_prefix}_users_temptable WHERE uid=" . intval($resend_uid));

        if (!$result) {
            return mxErrorScreen(_YADDATABASEERROR . ' Error Admin activate Entry ' . $resend_uid);
        }

        $row = sql_fetch_assoc($result);
        $defaults = pmx_user_defaults();
        $row = array_merge($defaults, $row);

        if ($row['check_isactive']) {
            return mxErrorScreen(_YADDATABASEERROR . ": " . _YADALREADYACTIVE . ' (' . $resend_uid . ')');
        }

        $sqlvars = mxAddSlashesForSQL($row);
        extract($sqlvars);

        /* verschiedene Feldwerte mit Grundwerten belegen, bzw. auf Gueltigkeit ueberpruefen */
        $setbday = (empty($sqlvars['user_bday'])) ? "NULL" : "'" . $sqlvars['user_bday'] . "'";
        switch ($userconfig->register_option) {
            case 3:
                $user_stat = 1;
                break;
            case 4:
                $user_stat = 0;
                break;
        }

        $fields = array(); // wird in YA-Erweiterung ergänzt
        $fields[] = "`uname` = '$uname'";
        $fields[] = "`pass` = '$pass'";
        $fields[] = "`email` = '$email'";
        $fields[] = "`name` = '$name'";
        $fields[] = "`url` = '" . mx_urltohtml(mxCutHTTP($url)) . "'";
        $fields[] = "`user_avatar` = '$user_avatar'";
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
            return mxErrorScreen(_YADDATABASEERROR . "Insert from temptable into usertable: <br>" . sql_error());
        }
        $uid = sql_insert_id();

        if (!empty($uid)) {
            /* Modulspezifische Useranfügungen durchfuehren */
            pmx_run_hook('user.add', $uid);

            $the_needed_data = $row;
            $the_needed_data['pass'] = '';
            $the_needed_data['uid'] = $uid;
            $the_needed_data['uname'] = $uname;
            if ($userconfig->sendnewusermsg && $userconfig->msgadminid) {
                YADsendnewuserpm($the_needed_data);
            }
        }

        switch ($userconfig->register_option) {
            case 3:
                $delqry = "DELETE FROM {$user_prefix}_users_temptable WHERE uid=" . intval($resend_uid);
                $delresult = sql_query($delqry);
                if (!$delresult) {
                    return mxErrorScreen(_YADDATABASEERROR . "Delete temptable - entry YA_DELETER");
                }
                $the_needed_data['mailpass'] = base64_decode($check_thepss); //Changed here for Password-Mail at Reg-Option 3
                YADsendnewusermail($the_needed_data); //Changed here for Password-Mail at Reg-Option 3
                break;
            case 4:
                $query2 = "UPDATE {$user_prefix}_users_temptable SET check_isactive = 1 WHERE uid=" . intval($resend_uid);
                $result2 = sql_query($query2);
                break;
        }

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
    }
    if ($internal_counter == $int_counter) {
        $counter = ($counter - $entries);
    }
    if ($counter < 0) {
        $counter = 0;
    }

    mxRedirect(adminUrl(PMX_MODULE, '', "counter=$counter"));
}

function YADsendnewusermail($userdata)
{
    $userconfig = load_class('Userconfig');
    // zusätzliche optionale Ausgaben
    $optmessage = (function_exists("sendnewusermail_option")) ? sendnewusermail_option($userdata) : "";
    extract($userdata);
    $message = _WELCOMETO . " " . $GLOBALS['sitename'] . "!\n\n";
    $message .= _YADYOUUSEDEMAIL . " '" . $GLOBALS['sitename'] . "' " . _YADTOREGISTER . ".\n\n";

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
 * private Nachricht an neuen Benutzer senden
 */
function YADsendnewuserpm($userdata)
{
    $userconfig = load_class('Userconfig');

    /* Sprachdatei auswählen */
    mxGetLangfile('User_Registration', 'hello-*.php');

    $time = date("Y-m-d H:i");
    $subject = mxAddSlashesForSQL(mxHtmlEntityDecode(_HELLOSUBJECT1 . " " . $userdata['uname'] . ", " . _HELLOSUBJECT2 . " " . $GLOBALS['sitename']));
    $msg = mxAddSlashesForSQL(mxHtmlEntityDecode(_HELLOTEXT));

    $qry = "INSERT INTO " . $GLOBALS['prefix'] . "_priv_msgs
            (msg_image, subject, from_userid, to_userid, msg_time, msg_text) VALUES
            ('" . $userconfig->msgicon . "', '" . $subject . "', " . $userconfig->msgadminid . ", " . $userdata['uid'] . ", '" . $time . "', '" . $msg . "')";
    $result = sql_query($qry);
    if (!$result) {
        return mxErrorScreen(_YADDATABASEERROR . "Insert into priv.msg-table YA-DELETER");
    }
}

/* Was ist zu tun ? */
switch ($op) {
    case PMX_MODULE . "/delete":
        if ((isset($_POST['check_delete'])) && (!empty($_POST['check_delete']))) {
            $uid_array = $_POST['check_delete'];
            $counter = intval($_POST['counter']);
            $int_counter = intval($_POST['internal_counter']);
            delete_the_requested($counter, $int_counter, $uid_array);
        } else {
            if (!isset($_REQUEST['counter'])) {
                $counter = 0;
            } else {
                $counter = intval($_REQUEST['counter']);
            }
            viewthelist($counter);
        }
        break;

    case PMX_MODULE . "/resend":
        if ((isset($_POST['check_delete'])) && (!empty($_POST['check_delete']))) {
            $uid_array = $_POST['check_delete'];
            $counter = intval($_POST['counter']);
            $int_counter = intval($_POST['internal_counter']);
            adminsendusermail($counter, $int_counter, $uid_array);
        } else {
            if (!isset($_REQUEST['counter'])) {
                $counter = 0;
            } else {
                $counter = intval($_REQUEST['counter']);
            }
            viewthelist($counter);
        }
        break;

    case PMX_MODULE . "/activate":

        if ((isset($_POST['check_delete'])) && (!empty($_POST['check_delete']))) {
            adminactivateaccount();
        } else {
            if (!isset($_REQUEST['counter'])) {
                $counter = 0;
            } else {
                $counter = intval($_REQUEST['counter']);
            }
            viewthelist($counter);
        }
        break;

    default :
        if (!isset($_REQUEST['counter'])) {
            $counter = 0;
        } else {
            $counter = intval($_REQUEST['counter']);
        }
        viewthelist($counter);
        break;
}

?>
