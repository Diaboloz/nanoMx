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

/* Sprachdatei auswählen */
mxGetLangfile(dirname(__FILE__));

if (!mxGetAdminPref('radminsuper')) {
    mxErrorScreen("Access Denied");
    die();
}
// mxSessionSetVar('panel', MX_ADMINPANEL_CONTENT);
function MsgDeactive($mid)
{
    global $prefix;
    sql_query("update " . $prefix . "_message set active='0' WHERE mid='" . intval($mid) . "'");
    mxRedirect(adminUrl(PMX_MODULE));
}

function messages()
{
    global $language, $prefix;
    $img_delete = mxCreateImage("images/delete.gif", _DELETE, 0, 'title="' . _DELETE . '"');
    $img_edit = mxCreateImage("images/edit.gif", _EDIT, 0, 'title="' . _EDIT . '"');

    $editor = load_class('Textarea');
    $now = time();

    include("header.php");
    GraphicAdmin();
    title(_MESSAGESADMIN);
    OpenTable();
    echo '
    <table class="full list">
      <thead>
        <tr>
          <th>' . _ID . '</th>
          <th>' . _TITLE . '</th>
          <th>' . _LANGUAGE . '</th>
          <th>' . _VIEW . '</th>
          <th>' . _ACTIVE . '</th>
          <th>' . _FUNCTIONS . '</th>
        </tr>
      </thead>
      <tbody>';
    $class = '';
    $out = '';
    $result = sql_query("select mid, title, content, date, expire, active, view, mlanguage from " . $prefix . "_message");
    while (list($mid, $title, $content, $mdate, $expire, $active, $view, $mlanguage) = sql_fetch_row($result)) {
        if ($active == 1) {
            $mactive = _YES;
        } elseif ($active == 0) {
            $mactive = _NO;
        }
        if ($view == 1) {
            $mview = _MVALL;
        } elseif ($view == 2) {
            $mview = _MVUSERS;
        } elseif ($view == 3) {
            $mview = _MVANON;
        } elseif ($view == 4) {
            $mview = _MVADMIN;
        }
        if (!$mlanguage) {
            $mlanguage = _ALL;
        }
        $class = ($class == '') ? ' class="alternate-a"' : '';
        $out .= '
        <tr' . $class . '>
          <td>' . $mid . '</td>
          <td>' . (($title) ? $title : '<i class="tiny">' . _NOSUBJECT . '</i>') . '</td>
          <td>' . mxGetLanguageString($mlanguage) . '</td>
          <td>' . $mview . '</td>
          <td>' . $mactive . '</td>
          <td>
            <a href="' . adminUrl(PMX_MODULE, 'edit', 'mid=' . $mid) . '">' . $img_edit . '</a>&nbsp;
            <a href="' . adminUrl(PMX_MODULE, 'delete', 'mid=' . $mid) . '">' . $img_delete . '</a>
          </td>
        </tr>';
    }
    if ($out) {
        echo $out;
    } else {
        echo '
        <tr><td colspan="6"></td></tr>';
    }
    echo '
      </tbody>
    </table>';
    CloseTable();
    OpenTable();
    echo "<fieldset><legend>" . _ADDMSG . "</legend>"
     . "<form action=\"" . adminUrl(PMX_MODULE, 'add') . "\" method=\"post\">"
     . '<br /><b>' . _MESSAGETITLE . ":</b><br />"
     . "<input type=\"text\" name=\"add_title\" value=\"\" size=\"50\" maxlength=\"100\" /><br /><br />"
     . "<b>" . _MESSAGECONTENT . ":</b><br />" . $editor->getHtml(array('name' => 'add_content', 'height' => '350')) . "<br /><br />";

    if ($GLOBALS['multilingual']) {
        echo "<b>" . _LANGUAGE . ": </b> ";
        echo mxLanguageSelect('add_mlanguage', $GLOBALS['currentlang'], 'language', 1) . '<br /><br />';
    } else {
        echo "<input type=\"hidden\" name=\"add_mlanguage\" value=\"\" />";
    }
    echo "<b>" . _EXPIRATION . ":</b> <select name=\"add_expire\">"
     . "<option value=\"0\" >" . _UNLIMITED . "</option>"
     . "<option value=\"86400\" >1 " . _DAY . "</option>"
     . "<option value=\"172800\" >2 " . _DAYS . "</option>"
     . "<option value=\"432000\" >5 " . _DAYS . "</option>"
     . "<option value=\"1296000\" >15 " . _DAYS . "</option>"
     . "<option value=\"2592000\" >30 " . _DAYS . "</option>"
     . "</select><br /><br />"
     . "<b>" . _ACTIVE . "?</b> <input type=\"radio\" name=\"add_active\" value=\"1\" checked=\"checked\" />" . _YES . " "
     . "<input type=\"radio\" name=\"add_active\" value=\"0\"  />" . _NO . ""
     . "<br /><br /><b>" . _VIEWPRIV . "</b> <select name=\"add_view\">"
     . "<option value=\"1\" >" . _MVALL . "</option>"
     . "<option value=\"2\" >" . _MVUSERS . "</option>"
     . "<option value=\"3\" >" . _MVANON . "</option>"
     . "<option value=\"4\" >" . _MVADMIN . "</option>"
     . "</select><br /><br />"
     . "<input type=\"hidden\" name=\"op\" value=\"" . PMX_MODULE . "/add\" />"
     . "<input type=\"hidden\" name=\"add_mdate\" value=\"$now\" />"
     . "<input type=\"submit\" value=\"" . _ADDMSG . "\" />"
     . "</form></fieldset>";
    CloseTable();
    include("footer.php");
}

function editmsg($mid)
{
    global $prefix;
    include("header.php");

    GraphicAdmin();
    title(_MESSAGESADMIN);

    $result = sql_query("select title, content, date, expire, active, view, mlanguage from " . $prefix . "_message WHERE mid='$mid'");
    list($title, $content, $mdate, $expire, $active, $view, $mlanguage) = sql_fetch_row($result);

    $editor = load_class('Textarea');

    OpenTable();
    echo "<fieldset><legend>" . _EDITMSG . "</legend>";
    if ($active == 1) {
        $asel1 = ' checked="checked"';
        $asel2 = "";
    } elseif ($active == 0) {
        $asel1 = "";
        $asel2 = ' checked="checked"';
    }
    if ($view == 1) {
        $sel1 = 'selected="selected" class="current"';
        $sel2 = "";
        $sel3 = "";
        $sel4 = "";
    } elseif ($view == 2) {
        $sel1 = "";
        $sel2 = 'selected="selected" class="current"';
        $sel3 = "";
        $sel4 = "";
    } elseif ($view == 3) {
        $sel1 = "";
        $sel2 = "";
        $sel3 = 'selected="selected" class="current"';
        $sel4 = "";
    } elseif ($view == 4) {
        $sel1 = "";
        $sel2 = "";
        $sel3 = "";
        $sel4 = 'selected="selected" class="current"';
    }
    if ($expire == 86400) {
        $esel1 = 'selected="selected" class="current"';
        $esel2 = "";
        $esel3 = "";
        $esel4 = "";
        $esel5 = "";
        $esel6 = "";
    } elseif ($expire == 172800) {
        $esel1 = "";
        $esel2 = 'selected="selected" class="current"';
        $esel3 = "";
        $esel4 = "";
        $esel5 = "";
        $esel6 = "";
    } elseif ($expire == 432000) {
        $esel1 = "";
        $esel2 = "";
        $esel3 = 'selected="selected" class="current"';
        $esel4 = "";
        $esel5 = "";
        $esel6 = "";
    } elseif ($expire == 1296000) {
        $esel1 = "";
        $esel2 = "";
        $esel3 = "";
        $esel4 = 'selected="selected" class="current"';
        $esel5 = "";
        $esel6 = "";
    } elseif ($expire == 2592000) {
        $esel1 = "";
        $esel2 = "";
        $esel3 = "";
        $esel4 = "";
        $esel5 = 'selected="selected" class="current"';
        $esel6 = "";
    } elseif ($expire == 0) {
        $esel1 = "";
        $esel2 = "";
        $esel3 = "";
        $esel4 = "";
        $esel5 = "";
        $esel6 = 'selected="selected" class="current"';
    }
    echo "<form action=\"" . adminUrl(PMX_MODULE, 'save') . "\" method=\"post\">"
     . '<br /><b>' . _MESSAGETITLE . ":</b><br />"
     . "<input type=\"text\" name=\"title\" value=\"" . mxentityquotes($title) . "\" size=\"50\" maxlength=\"100\" /><br /><br />"
     . "<b>" . _MESSAGECONTENT . ":</b><br />" . $editor->getHtml(array('name' => 'msg_content', 'value' => $content, 'height' => '350')) . "<br /><br />";

    if ($GLOBALS['multilingual']) {
        echo "<b>" . _LANGUAGE . ": </b> ";
        echo mxLanguageSelect('mlanguage', $mlanguage, 'language', 1) . '<br /><br />';
    } else {
        echo "<input type=\"hidden\" name=\"mlanguage\" value=\"\" />";
    }
    echo "<b>" . _EXPIRATION . ":</b> <select name=\"expire\">"
     . "<option value=\"0\" $esel6>" . _UNLIMITED . "</option>"
     . "<option value=\"86400\" $esel1>1 " . _DAY . "</option>"
     . "<option value=\"172800\" $esel2>2 " . _DAYS . "</option>"
     . "<option value=\"432000\" $esel3>5 " . _DAYS . "</option>"
     . "<option value=\"1296000\" $esel4>15 " . _DAYS . "</option>"
     . "<option value=\"2592000\" $esel5>30 " . _DAYS . "</option>"
     . "</select><br /><br />"
     . "<b>" . _ACTIVE . "?</b> <input type=\"radio\" name=\"active\" value=\"1\" $asel1 />" . _YES . " "
     . "<input type=\"radio\" name=\"active\" value=\"0\" $asel2 />" . _NO;
    if ($active == 1) {
        echo "<br /><br /><b>" . _CHANGEDATE . "</b>"
         . "<input type=\"radio\" name=\"chng_date\" value=\"1\" />" . _YES . " "
         . "<input type=\"radio\" name=\"chng_date\" value=\"0\" checked=\"checked\" />" . _NO . '<br /><br />';
    } elseif ($active == 0) {
        echo "<br /><span class=\"tiny\">" . _IFYOUACTIVE . "</span><br /><br />"
         . "<input type=\"hidden\" name=\"chng_date\" value=\"1\" />";
    }
    echo "<b>" . _VIEWPRIV . "</b> <select name=\"view\">"
     . "<option value=\"1\" $sel1>" . _MVALL . "</option>"
     . "<option value=\"2\" $sel2>" . _MVUSERS . "</option>"
     . "<option value=\"3\" $sel3>" . _MVANON . "</option>"
     . "<option value=\"4\" $sel4>" . _MVADMIN . "</option>"
     . "</select><br /><br />"
     . "<input type=\"hidden\" name=\"mdate\" value=\"$mdate\" />"
     . "<input type=\"hidden\" name=\"mid\" value=\"$mid\" />"
     . "<input type=\"hidden\" name=\"op\" value=\"" . PMX_MODULE . "/save\" />"
     . "<input type=\"submit\" value=\"" . _SAVECHANGES . "\" />"
     . "</form></fieldset>";
    CloseTable();
    include("footer.php");
}

function savemsg($mid, $title, $content, $mdate, $expire, $active, $view, $chng_date, $mlanguage)
{
    global $prefix;
    $title = mxAddSlashesForSQL(($title));
    $content = mxAddSlashesForSQL(($content));
    if ($chng_date == 1) {
        $newdate = time();
    } elseif ($chng_date == 0) {
        $newdate = $mdate;
    }
    $result = sql_query("update " . $prefix . "_message set title='$title', content='$content', date='$newdate', expire='$expire', active='$active', view='$view', mlanguage='$mlanguage' WHERE mid='" . intval($mid) . "'");
    mxRedirect(adminUrl(PMX_MODULE));
}

function addmsg($add_title, $add_content, $add_mdate, $add_expire, $add_active, $add_view, $add_mlanguage)
{
    global $prefix;
    $title = mxAddSlashesForSQL(($add_title));
    $content = mxAddSlashesForSQL($add_content);
    $result = sql_query("insert into " . $prefix . "_message values (NULL, '$title', '$content', '$add_mdate', '$add_expire', '$add_active', '$add_view', '$add_mlanguage')");
    if (!$result) {
        exit();
    }
    mxRedirect(adminUrl(PMX_MODULE));
}

function deletemsg($mid, $ok = 0)
{
    global $prefix;
    if ($ok) {
        $result = sql_query("delete from " . $prefix . "_message where mid=" . intval($mid) . "");
        if (!$result) {
            return;
        }
        mxRedirect(adminUrl(PMX_MODULE));
    } else {
        include("header.php");
        GraphicAdmin();
        title(_MESSAGESADMIN);

        OpenTableAl();
        echo "<center>" . _REMOVEMSG;
        echo "<br /><br />[&nbsp;<a href=\"" . adminUrl(PMX_MODULE) . "\">" . _NO . "</a> | <a href=\"" . adminUrl(PMX_MODULE, 'delete', 'mid=' . $mid . '&amp;ok=1') . "\">" . _YES . "</a>&nbsp;]</center>";
        CloseTableAl();
        include("footer.php");
    }
}
switch ($op) {
    case PMX_MODULE . "/edit":
        editmsg($mid);
        break;

    case PMX_MODULE . "/add":
        addmsg($add_title, $add_content, $add_mdate, $add_expire, $add_active, $add_view, $add_mlanguage);
        break;

    case PMX_MODULE . "/delete":
        $ok = (empty($_REQUEST['ok'])) ? 0 : $_REQUEST['ok'];
        deletemsg($mid, $ok);
        break;

    case PMX_MODULE . "/save":
        savemsg($mid, $title, $msg_content, $mdate, $expire, $active, $view, $chng_date, $mlanguage);
        break;

    default:
        messages();
        break;
}

?>