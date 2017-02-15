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
 * some parts of this file based on:
 * php-nuke Web Portal System - http://phpnuke.org/
 * Thatware - http://thatware.org/
 */

defined('mxMainFileLoaded') or die('access denied');
// Betreffeld in Kommentaren verwenden?
$comments_use_subject = 0;
// Zeit, die zwischen 2 Kommentaren verstreichen muss, in Sekunden
$comments_waitseconds = 30;

if (empty($articlecomm)) {
    return;
}

$module_name = basename(dirname(__FILE__));
mxGetLangfile($module_name);

function DisplayTopic()
{
    global $prefix, $user_prefix, $module_name, $anonpost, $story;
    // $story ist global in articles.php, also bei Änderungen darauf achten...
    if ($story["acomm"] || empty($story['sid'])) {
        return false;
    }

    $qry = "SELECT u.uid, u.uname, u.email, c.sid, c.tid, c.reply_date, c.host_name, c.subject, c.comment, c.name AS postername
            FROM ${prefix}_comments AS c LEFT JOIN {$user_prefix}_users AS u ON c.uid = u.uid
            WHERE (((c.sid)=" . intval($story['sid']) . ") AND ((c.modul_name)='" . $module_name . "'))
            ORDER BY  reply_date  DESC";
    $result = sql_query($qry);

    $views = '';
    while ($data = sql_fetch_assoc($result)) {
        $data['posteruid'] = $data['uid'];
        $data['op'] = 'comments';
        $views .= commentview($data);
    }

    echo '
        <section class="commentlist">';
    if (!empty($anonpost) || (MX_IS_ADMIN && $GLOBALS['articlecomm'] == 2) || (MX_IS_USER && $GLOBALS['articlecomm'] == 1)) {
        echo "
        <form action=\"modules.php?name=" . $module_name . "&amp;file=article&amp;sid=" . $story['sid'] . "#comments\" method=\"post\" name=\"commentform\" class=\"mx-form\">
            <input type=\"hidden\" name=\"name\" value=\"" . $module_name . "\" />
            <input type=\"hidden\" name=\"file\" value=\"comments\" />
            <input type=\"hidden\" name=\"sid\" value=\"" . $story['sid'] . "\" />
            <input type=\"hidden\" name=\"op\" value=\"Reply\" />
            <input type=\"hidden\" name=\"title\" value=\"" . base64_encode($story['title']) . "\" />
            <p class=\"txtcenter\">
                <input class=\"mx-button mx-button-primary\" type=\"submit\" value=\"" . _REPLYMAIN . "\" />
            </p>
        </form>";
    } elseif ((empty($anonpost) || MX_IS_USER) && $GLOBALS['articlecomm'] == 2) {
        echo '<p class="alert alert-info">' . _STOPCOMMENTS . '</p>';
    } elseif (empty($anonpost) && $GLOBALS['articlecomm'] == 1) {
        echo '<p class="alert alert-info">' . _NOANONCOMMENTS . '</p>';
    }

    if ($views) {

    echo '
        <h3 class="txtcenter">' . mxPrepareToDisplay(strip_tags($story['title'])) . '</h3>
        <a name="comments" id="comments"></a>
          <article>
              ' . $views . '
          </article>
        </section>';
    }
}

function reply()
{
    global $anonymous, $anonpost, $module_name, $comments_use_subject, $pagetitle;
    if (empty($anonpost) && !MX_IS_USER) {
        return mxErrorScreen(_NOANONCOMMENTS, _COMMENTREPLY);
    } elseif (!MX_IS_ADMIN && MX_IS_USER && $GLOBALS['articlecomm'] == 2) {
        return mxErrorScreen(_STOPCOMMENTS, _COMMENTREPLY);
    }
    $data = mxStripSlashes($_POST);
    if (!isset($data['sid'])) {
        return mxErrorScreen(_NOTRIGHT);
    }
    $data['sid'] = intval($data['sid']);
    if (empty($data['sid'])) {
        return mxErrorScreen(_NCOMMERR_1, _COMMENTREPLY);
    }

    $title = mxSecureValue(strip_tags(base64_decode($data['title'])), true);

    if ($comments_use_subject) {
        $data['subject'] = $title;
        if (stripos($data['subject'], 'Re:') !== 0) {
            $data['subject'] = "Re: " . substr($data['subject'], 0, 81);
        }
    } else {
        $data['subject'] = '';
    }
    $data['comment'] = '';
    $data['posttype'] = 'plaintext';
    $pagetitle = _COMMENTREPLY . ': ' . $title;
    include('header.php');
    title(_COMMENTREPLY . ': ' . $title);
    echo '
      <p class="alert alert-info">
        ' . _COMMENTSWARNING . '
      </p>';
    commentform($data);
    include('footer.php');
}

function replyPreview($errormessage = '')
{
    global $anonymous, $module_name, $pagetitle;
    $data = mxStripSlashes($_POST);
    $data['captcha'] = '';
    if (!isset($data['sid'])) {
        return mxErrorScreen(_NOTRIGHT);
    }
    $data = commentsCheckUser($data);
    $pagetitle = _COMMENTREPLY . ': ' . $data['subject'];
    include('header.php');
    title(_COMREPLYPRE);
    echo '
      <ol style="list-style: none;" class="man pan">
        ' . commentview($data) . '
      </ol>';
    if ($errormessage) {
        echo '
            <div class="alert alert-warning">
                ' . $errormessage .' 
            </div>';
    }
    title(_COMMENTREPLY . ': ' . $data['subject']);
    commentform($data);
    include('footer.php');
}

function commentform($data)
{
    global $module_name, $anonpost, $comments_use_subject;

    $data = mxStripSlashes($data);
    $data['subject'] = strip_tags($data['subject']);

    $data = commentsCheckUser($data);

    $editor = load_class('Textarea', array('name' => 'comment', 'value' => $data['comment'], 'mode' => 'mini', 'height' => '200'));
    // $editor->setWysiwyg(false);
    $wysiwyg = $editor->is_wysiwyg();

    $captcha_object = load_class('Captcha', 'commentson');

    echo '
        <form action="modules.php?name=' . $module_name . '&amp;file=article&amp;sid=' . $data['sid'] . '#comments" method="post" name="commentform" class="mx-form">';
    if (!$comments_use_subject) {
        echo '<input type="hidden" name="subject" value="" />';
    }
    if (!$anonpost) {
        echo '<input type="hidden" name="postername" value="' . htmlspecialchars($data['postername']) . '" />';
    }

    echo '
        <table class="w100">';
    if ($comments_use_subject) {
        echo '
            <tr>
                <td>' . _SUBJECT . ':</td>
                <td>
                    <input type="text" name="subject" size="50" maxlength="85" value="' . mxEntityQuotes($data['subject']) . '" />
                </td>
            </tr>';
    }

    echo "
        <tr>
            <td>" . $editor->getHtml() . "</td>
        </tr>";

    if (!MX_IS_USER && $anonpost) {
        echo '
        <tr>
            <td>' . _YOURNAME . ':</td>
            <td>
                <input type="text" name="postername" value="' . mxEntityQuotes($data['postername']) . '" size="30" maxlength="25" />
            </td>
        </tr>';
    }

    if ($captcha_object->get_active()) {
        echo '
            <tr>
                <td>' . $captcha_object->image() . '</td>
            </tr>
            <tr>
                <td>' . $captcha_object->caption() . " </td>
                <td>" . $captcha_object->inputfield() . "</td>
            </tr>";
    }
    echo '
            <tr>
                <td>
                    <input type="submit" name="op" value="' . _PREVIEW . '" class="mx-button" />
                    <input type="submit" name="op" value="' . _OK . '" class="mx-button mx-button-primary" />';
    if ($captcha_object->get_active()) {
        echo "
            &nbsp;" . $captcha_object->reloadbutton() . "&nbsp;";
    }
    if ($wysiwyg) {
        echo '<input type="hidden" name="posttype" value="html" />';
    } else {
        echo '<select name="posttype">';
        echo '<option value="exttrans"' . (($data['posttype'] == "exttrans") ? ' selected="selected" class="current"' : '') . '>' . _EXTRANS;
        echo '<option value="html"' . (($data['posttype'] == "html") ? ' selected="selected" class="current"' : '') . '>' . _HTMLFORMATED;
        echo '<option value="plaintext"' . (($data['posttype'] == "plaintext") ? ' selected="selected" class="current"' : '') . '>' . _PLAINTEXT;
        echo '</select>';
    }
    echo '</td></tr>
    </table>
        <input type="hidden" name="uid" value="' . $data['uid'] . '" /><br />
        <input type="hidden" name="sid" value="' . $data['sid'] . '" /><br />
        <input type="hidden" name="name" value="' . $module_name . '" /><br />
        <input type="hidden" name="file" value="comments" /><br />
    </form>';
}

function commentview($data)
{
    global $module_name, $commentlimit, $anonymous, $comments_use_subject;

  /*remove nanomx  static $x = 1;
    $c = fmod($x++, 2);
    $dclass = ($c) ? 'bgcolor1' : 'bgcolor3';*/

    $pici = load_class('Userpic', intval($data['uid']));
    $avatar = $pici->getHtml('small', array('scale-width' => 40, 'class' => 'float-left'), true);

    $data['postername'] = mxPrepareToHTMLDisplay((empty($data['postername'])) ? $anonymous : $data['postername']);
    $nameclick = ($data['posteruid']) ? mxCreateUserprofileLink($data['postername']) : $data['postername'];
    $data['subject'] = ($comments_use_subject) ? strip_tags($data['subject']) : '';
    $data['tid'] = (empty($data['tid'])) ? 0 : intval($data['tid']);
    $data['date'] = (empty($data['reply_date'])) ? time() : $data['reply_date'];
    $datetime = mx_strftime(_SHORTDATESTRING . ' %H:%M', $data['date']);
    $full = (empty($_REQUEST['full'])) ? 0 : intval($_REQUEST['full']);

    if (isset($data['posttype']) && $data['comment']) {
        switch ($data['posttype']) {
            case 'exttrans':
                $data['comment'] = mxNL2BR(trim(htmlspecialchars($data['comment'])));
                break;
            case 'plaintext':
                $data['comment'] = mxNL2BR(trim(strip_tags($data['comment'])));
                break;
            case 'html':
            default:
                $data['comment'] = trim(mxPrepareToHTMLDisplay($data['comment']));
        }
    }
    if ((strlen($data['comment']) > $commentlimit) && ($full != $data['tid'])) {
        $data['comment'] = substr($data['comment'], 0, $commentlimit) . "<div align=\"right\">
        <a href=\"modules.php?name=" . $module_name . "&amp;file=article&amp;sid=" . $data['sid'] . "&amp;full=" . $data['tid'] . "#c" . $data['tid'] . "\">" . _READREST . "</a>
        </div>";
    } else {
        $data['comment'] .= '<br />';
    }
    $data['comment'] = make_clickable($data['comment']);
    if (!empty($data['subject'])) {
        $data['comment'] = '<p>' . $data['subject'] . '</p>' . $data['comment'];
    }
    // irgend eine Firefox Erweiterung macht den Scheiss da rein...
    $data['comment'] = str_replace('&lt;br type="_moz" /&gt;', '', $data['comment']);

    $pics = array();
    if ($data['op'] != _PREVIEW) {
        if (MX_IS_ADMIN && !empty($data['tid'])) {
            $pics[] = "<a href=\"" . adminUrl(PMX_MODULE, "RemoveComment", "tid=" . $data['tid'] . "&amp;sid=" . $data['sid'] . "&amp;ok=0") . "\">" . mxCreateimage("modules/$module_name/images/delete.gif", _DELETE) . "</a>";
            if ($data['host_name']) $pics[] = "<a title=\"" . $data['host_name'] . "\" onclick=\"alert('ip: " . $data['host_name'] . "')\">" . mxCreateimage("modules/$module_name/images/ip.gif", $data['host_name']) . "</a>";
        }
    }
    $adminpics = (MX_IS_ADMIN) ? '<span style="float:right">' . implode(' ', $pics) . '</span>' : '';
    return '
      <p id="c' . $data['tid'] . '" class="comment-meta">
        ' . $adminpics . '
        ' . $avatar . $nameclick . '&nbsp;<span class="tiny">' . _WRITES . '&nbsp;' . _ON . ' ' . $datetime . '</span>
      </p>
      <div class="comment">
        ' . $data['comment'] . '
      </div>';
}

function CreateTopic()
{
    global $prefix, $anonpost, $module_name, $comments_use_subject, $comments_waitseconds;
    $data = mxStripSlashes($_POST);

    if (!isset($data['sid'])) {
        return mxErrorScreen(_NOTRIGHT);
    }
    $data = commentsCheckUser($data);
    if (empty($data['posteruid'])) {
        $data['postername'] = substr($data['postername'], 0, 58) . " * ";
    }
    if (isset($data['errorinname'])) {
        return replyPreview($data['errorinname']);
    }

    $captcha_object = load_class('Captcha', 'commentson');
    if (!$captcha_object->check($_POST, 'captcha')) {
        return replyPreview(_CAPTCHAWRONG);
    }

    $data['sid'] = intval($data['sid']);
    $data['subject'] = ($comments_use_subject) ? mxAddSlashesForSQL(strip_tags($data['subject'])) : '';
    switch ($data['posttype']) {
        case 'exttrans':
            $data['comment'] = mxAddSlashesForSQL(mxNL2BR(trim(htmlspecialchars($data['comment']))));
            break;
        case 'plaintext':
            $data['comment'] = mxAddSlashesForSQL(mxNL2BR(trim(strip_tags($data['comment']))));
            break;
        case 'html':
        default:
            $data['comment'] = mxAddSlashesForSQL(trim($data['comment']));
    }
    $data['comment'] = trim($data['comment']);
    if (empty($data['comment'])) {
        return replyPreview(_ERRNOTEXT);
    }
    $result = sql_query("select sid from ${prefix}_stories where sid='" . $data['sid'] . "' AND acomm=0");
    if (!sql_num_rows($result)) {
        return mxErrorScreen(_NCOMMERR_1);
    }
    $checkdate = time() - intval($comments_waitseconds);
    $result = sql_query("select tid from ${prefix}_comments where reply_date >= " . $checkdate . " AND (host_name = '" . MX_REMOTE_ADDR . "' OR uid = " . intval($data['uid']) . ")");
    if (sql_num_rows($result)) {
        return mxErrorScreen(_NCOMMERR_2);
    }
    // $result = sql_query("select tid from ${prefix}_comments where comment = '" . mxAddSlashesForSQL($data['comment']) . "' AND (host_name = '" . MX_REMOTE_ADDR . "' OR uid = " . intval($data['uid']) . ")");
    // if (sql_num_rows($result)) {
    // return mxErrorScreen(_NCOMMERR_3);
    // }
    if ((MX_IS_USER || MX_IS_ADMIN || !empty($anonpost)) && !empty($data['comment'])) {
        $qry = "INSERT INTO ${prefix}_comments SET
        pid = 0,
        sid = " . intval($data['sid']) . ",
        modul_name = '" . $module_name . "',
        reply_date = '" . time() . "',
        name = '" . mxAddSlashesForSQL($data['postername']) . "',
        uid = " . intval($data['posteruid']) . ",
        host_name = '" . MX_REMOTE_ADDR . "',
        subject = '" . mxAddSlashesForSQL(strip_tags($data['subject'])) . "',
        comment = '" . mxAddSlashesForSQL($data['comment']) . "'
        ";
        if (sql_query($qry)) {
            $result = sql_query("SELECT count(tid) from " . $prefix . "_comments where sid=" . $data['sid'] . "");
            list($numresults) = sql_fetch_row($result);
            sql_query("update " . $prefix . "_stories set comments=" . intval($numresults) . " where sid=" . $data['sid'] . "");
            /* Notitfy comment by stefvar */
            if (!empty($GLOBALS['notifycomment'])) {
                $message = _HELLO . ",\n" . sprintf(''
                     . _COMMENTSNOTIFY, $GLOBALS['sitename']) . "\n\n"
                 . "========================================================\n"
                 . $data['postername'] . " " . _WRITES . ":\n\n"
                 . $data['comment'] . "\n\n"
                 . "========================================================\n"
                 . PMX_HOME_URL . "/modules.php?name=News&file=article&sid=" . $data['sid'] . "#comments\n\n" ;
                mxMail($GLOBALS['notify_email'], $GLOBALS['notify_subject'], $message, $GLOBALS['notify_from']);
            }
        }
    }
    mxRedirect("modules.php?name=" . $module_name . "&file=article&sid=" . $data['sid'] . "#comments");
}

function commentsCheckUser($data)
{
    global $anonpost, $user_prefix;
    if (!isset($data['postername']) || !isset($data['uid'])) {
        $data['postername'] = '';
        $data['posteruid'] = 0;
        $data['uid'] = 0;
        if (MX_IS_USER) {
            $userinfo = mxGetUserData();
            $data['postername'] = $userinfo['uname'];
            $data['uid'] = $userinfo['uid'];
            $data['posteruid'] = $userinfo['uid'];
        }
    } else {
        if (MX_IS_USER) {
            $userinfo = mxGetUserData();
            $data['posteruid'] = $userinfo['uid'];
            if (!$anonpost) {
                $data['postername'] = $userinfo['uname'];
                $data['uid'] = $userinfo['uid'];
            } else if ($data['postername'] != $userinfo['uname'] && $data['uid'] > 1) {
                $data['posteruid'] = 0;
                $result = sql_query("select uid from {$user_prefix}_users where uname='" . mxAddSlashesForSQL(substr($data['postername'], 0, 25)) . "'");
                if (sql_num_rows($result)) {
                    $data['errorinname'] = sprintf(_USERNAMENOTALLOWED, $data['postername']);
                    $data['postername'] = $userinfo['uname'];
                    $data['posteruid'] = $userinfo['uid'];
                }
            }
        }
    }
    $data['postername'] = substr(strip_tags($data['postername']), 0, 25);
    return $data;
}

$op = (isset($_REQUEST['op'])) ? $_REQUEST['op'] : '';
switch ($op) {
    case 'Reply':
        reply();
        break;

    case _PREVIEW:
        replyPreview();
        break;

    case _OK:
        CreateTopic();
        break;
}

?>