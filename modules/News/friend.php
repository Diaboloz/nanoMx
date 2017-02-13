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
 *
 *
 */

defined('mxMainFileLoaded') or die('access denied');
// rechte Bloecke anzeigen?
// 0 = nein, 1 = ja
$index = 0;

$module_name = basename(__DIR__);
mxGetLangfile($module_name);

$pagetitle = _FRIEND;

if (!mxModuleAllowed('Recommend_Us')) {
    mxErrorScreen('<strong>' . _MODULENOTACTIVE . '</strong><br />');
}

/**
 * das Formular
 */
function RecommendSite($event = array())
{
    global $sitename, $prefix, $module_name;

    if (isset($event['more'])) {
        $tmp = mxSecureValue(unserialize(base64_decode($event['more'])), true);
        if (is_array($tmp)) {
            $event['yname'] = $tmp['yname'];
            $event['ymail'] = $tmp['ymail'];
        }
    }

    if (MX_IS_USER && empty($event['yname']) && empty($event['ymail'])) {
        $uinfo = mxGetUserData();
        $event['yname'] = $uinfo['uname'];
        $event['ymail'] = $uinfo['email'];
    }

    $sid = (isset($_REQUEST['sid'])) ? $_REQUEST['sid'] : 0;
    $result = sql_query("select sid, title from " . $prefix . "_stories where sid=" . intval($sid));
    list($event['sid'], $event['title']) = sql_fetch_row($result);
    if (empty($event['sid'])) {
        mxErrorScreen(_NOTRIGHT);
    }

    include_once("header.php");
    title(_FRIEND);
    if (isset($event['recerror'])) {
        openTableAl();
        echo '<div style="text-align: left;"><h2>' . _REC_ERRORTITLE . '</h2><ul><li>' . implode('</li><li>', $event['recerror']) . '</li></ul></div>';
        closeTableAl();
        echo '<br />';
    }
    OpenTable();
    echo '<p>' . _YOUSENDSTORY . ' \'<b>' . $event['title'] . '</b>\'</p>';
    echo "<form action=\"modules.php?name=" . $GLOBALS['module_name'] . "&amp;file=friend&amp;sid=" . $event['sid'] . "\" method=\"post\" name=\"sendform\">"
     . "<input type=\"hidden\" name=\"name\" value=\"" . $GLOBALS['module_name'] . "\" />"
     . "<input type=\"hidden\" name=\"file\" value=\"friend\" />"
     . "<input type=\"hidden\" name=\"op\" value=\"SendSite\" />"
     . "<input type=\"hidden\" name=\"site\" value=\"" . mx_urltohtml($_SERVER['HTTP_REFERER']) . "\" />"
     . "<input type=\"hidden\" name=\"sid\" value=\"" . $event['sid'] . "\" />"
     . "<table class=\"form\">"
     . "<tr><td>" . _FFRIENDNAME . " </td><td> <input type=\"text\" name=\"fname\" value=\"" . (isset($event['fname']) ? mxEntityQuotes($event['fname']): '') . "\" size=\"35\" /></td></tr>\n"
     . "<tr><td>" . _FFRIENDEMAIL . " </td><td> <input type=\"text\" name=\"fmail\" value=\"" . (isset($event['fmail']) ? mxEntityQuotes($event['fmail']): '') . "\" size=\"35\" /></td></tr>\n"
     . "<tr valign=\"top\"><td>" . _FREMARKS . " </td><td><textarea name=\"remarks\" cols=\"35\" rows=\"5\">" . (isset($event['remarks']) ? mxEntityQuotes(strip_tags($event['remarks'])): '') . "</textarea></td></tr>\n"
     . "<tr><td>" . _FYOURNAME . " </td><td> <input type=\"text\" name=\"yname\" value=\"" . (!empty($event['yname']) ? mxEntityQuotes($event['yname']): '') . "\" size=\"35\" /></td></tr>\n"
     . "<tr><td>" . _FYOUREMAIL . " </td><td> <input type=\"text\" name=\"ymail\" value=\"" . (!empty($event['ymail']) ? mxEntityQuotes($event['ymail']): '') . "\" size=\"35\" /></td></tr>\n";
    // captcha anzeigen
    $captcha_object = load_class('Captcha', 'recommendon');
    if ($captcha_object->get_active()) {
        echo '<tr><td>&nbsp;</td><td><div>' . $captcha_object->image() . '</div></td></tr>' . "\n"
         . "<tr><td>" . $captcha_object->caption() . " </td><td>" . $captcha_object->inputfield() . "</td></tr>\n";
    }
    echo "<tr><td colspan=\"2\" align=\"center\"><br /><input type=\"submit\" name=\"submitcancel\" value=\"" . _SENDCANCEL . "\" title=\"" . _TOCONTENT . "\" />&nbsp;&nbsp;<input type=\"submit\" name=\"submitsend\" value=\"" . _SENDFRIEND . "\" />";
    if ($captcha_object->get_active()) {
        echo "&nbsp;&nbsp;" . $captcha_object->reloadbutton() . "";
    }
    echo "</td></tr></table>"
     . "</form>\n";
    CloseTable();
    include_once('footer.php');
}

/**
 * Die Verarbeitung und Fehlerprüfung
 */
function SendSite()
{
    global $sitename, $prefix, $module_name;

    $event = mxStripSlashes($_POST);
    $sid = (isset($event['sid'])) ? $event['sid'] : 0;

    if (isset($event['submitcancel']) && $sid) {
        mxRedirect('modules.php?name=' . $module_name . '&amp;file=article&amp;sid=' . $sid);
    }
    $result = sql_query("select sid, title from " . $prefix . "_stories where sid=" . intval($sid));
    list($event['sid'], $event['title']) = sql_fetch_row($result);
    if (empty($event['sid'])) {
        mxErrorScreen(_NOTRIGHT);
    }
    if (empty($event['yname'])) {
        $event['recerror'][] = _REC_ERRORNAME;
    }

    switch (true) {
        case empty($event['ymail']):
        case !mxCheckEmail($event['ymail']):
            $event['recerror'][] = _REC_ERRORSENDER;
            break;
        case pmx_is_mail_banned($event['ymail']):
            $event['recerror'][] = _REC_ERRORSENDER . ' (' . _MAILISBLOCKED . ')';
    }

    switch (true) {
        case empty($event['fmail']):
        case !mxCheckEmail($event['fmail']):
            $event['recerror'][] = _REC_ERRORRECEIVER;
            break;
        case pmx_is_mail_banned($event['fmail']):
            $event['recerror'][] = _REC_ERRORRECEIVER . ' (' . _MAILISBLOCKED . ')';
    }

    $captcha_object = load_class('Captcha', 'recommendon');
    if (!$captcha_object->check($_POST, 'captcha')) {
        $event['recerror'][] = _CAPTCHAWRONG;
    }

    if (isset($event['recerror'])) {
        RecommendSite($event);
    } else {
        if (MX_IS_USER) {
            $usersession = mxGetUserSession();
        } else {
            $usersession[0] = 0;
        }
        $subject = strip_tags(_INTERESTING . " " . $sitename . ", " . $event['title']);
        $message = _HELLO . " " . $event['fname'] . ",\n\n" . _YOURFRIEND . " " . $event['yname'] . " " . _CONSIDERED . "\n" . _NEWSURL . ": " . PMX_HOME_URL . "/modules.php?name=" . $module_name . "&amp;file=article&amp;sid=" . $event['sid'] . " \n\n" . _YOUCANREAD . " " . $sitename . "\n" . PMX_HOME_URL;
        if (!empty($event['remarks'])) {
            $message .= "\n\n" . $event['yname'] . " " . _FMEANS . ":\n" . $event['remarks'] . "\n\n\n";
        }
        $message = strip_tags($message);
        if (mxMail($event['fmail'], $subject, $message, $event['ymail'], "text", "", $event['yname'])) {
            sql_query("INSERT INTO " . $prefix . "_recommend VALUES (NULL,'" . mxAddSlashesForSQL(strip_tags($event['fname'])) . "','" . $event['fmail'] . "','" . mxAddSlashesForSQL(strip_tags($event['yname'])) . "','" . $event['ymail'] . "','" . $usersession[0] . "','" . mxAddSlashesForSQL(MX_REMOTE_ADDR) . "','" . time() . "','" . mxAddSlashesForSQL(strip_tags($event['site'])) . "')");
        }
        // das ist nur eine Dummy-Weiterleitung um das Aktualisieren der Seite zu verhindern
        $more = array('fname' => $event['fname'],
            'fmail' => $event['fmail'],
            'yname' => $event['yname'],
            'ymail' => $event['ymail'],
            );
        $more = base64_encode(serialize($more));
        mxRedirect('modules.php?name=' . $module_name . '&file=friend&op=SiteSent&sid=' . $event['sid'] . '&more=' . $more, '', 0);
    }
}

/**
 * das ist nur eine Dummy-Weiterleitung um das Aktualisieren der Seite zu verhindern
 */
function SiteSent()
{
    global $module_name, $prefix;
    $sid = (isset($_GET['sid'])) ? $_GET['sid'] : 0;
    $result = sql_query("select sid, title from " . $prefix . "_stories where sid=" . intval($sid));
    list($sid, $title) = sql_fetch_row($result);
    if (empty($sid)) {
        mxErrorScreen(_NOTRIGHT);
    }
    $more = isset($_GET['more']) ? $_GET['more'] : '';
    if ($more) {
        $tmp = mxSecureValue(unserialize(base64_decode($more)), true);
        $fname = $tmp['fname'];
    } else {
        $fname = '?';
    }
    $msg = _FSTORY . ' <strong>' . $title . '</strong> ' . _HASSENT . ' ' . $fname . '... <br /><br />' . _THANKS;
    mxRedirect('modules.php?name=' . $module_name . '&amp;file=article&amp;sid=' . $sid, $msg, 5);
}

/**
 * Auswahl der Optionen
 */
$op = (isset($_REQUEST['op'])) ? $_REQUEST['op'] : "";
switch ($op) {
    case 'SendSite':
        SendSite();
        break;

    case 'SiteSent':
        SiteSent();
        break;

    default:
        RecommendSite($_GET);
        break;
}

?>