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

$module_name = basename(dirname(__FILE__));
// mxGetLangfile($module_name);
mxGetLangfile('News');

include_once(PMX_SYSTEM_DIR . DS . 'mxNewsFunctions.php');

$pagetitle = _SUBMITNEWS;

/**
 */
function defaultDisplay()
{
    include("header.php");
    OpenTable();
    echo "<center><font class=\"title\"><b>" . _SUBMITNEWS . "</b></font></center>";
    echo "<p class=\"content\">" . _SUBMITADVICE . "</p>";
    CloseTable();
    echo '<br />';
    vkpNewsForm();
    include('footer.php');
}

/**
 */
function PreviewStory($story)
{
    $story = mxStripSlashes($story);
    include('header.php');
    if (isset($story['errmsg'])) {
        title(_SUBMITNEWS);
        openTableAl();
        echo '<div class="align-left"><h2>' . _ERROROCCURS . '</h2><ul><li>' . implode('</li><li>', $story['errmsg']) . '</li></ul></div>';
        closeTableAl();
    } else {
        title(_NEWSUBPREVIEW);
        OpenTable();
        echo '<p>' . _STORYLOOK . '<br /><br /></p>';
        vkpStoryPreview($story);
        echo '<p class="align-center tiny">' . _CHECKSTORY . '</p>';
        CloseTable();
    }
    echo '<br />';
    vkpNewsForm($story);
    include('footer.php');
}

/**
 */
function submitStory($story)
{
    global $prefix;
    if (empty($story['topic'])) {
        $story['errmsg'][] = _ERRNOTOPIC;
    }
    if (empty($story['title'])) {
        $story['errmsg'][] = _ERRNOTITLE;
    }
    if (empty($story['hometext'])) {
        $story['errmsg'][] = _ERRNOTEXT;
    }

    $captcha_object = load_class('Captcha', 'newson');
    if (!$captcha_object->check($story, 'captcha')) {
        $story['errmsg'][] = _CAPTCHAWRONG;
    }

    /* wenn Fehler aufgetreten, einfach die Vorschau aufrufen und abbrechen */
    if (isset($story['errmsg'])) {
        $story['op'] = _PREVIEW;
        PreviewStory($story);
        exit;
    }

    extract($story);
    if (MX_IS_USER) {
        $cookie = mxGetUserSession();
        $uid = $cookie[0];
        $name = $cookie[1];
    } else {
        $uid = 1;
        $name = $GLOBALS['anonymous'];
    }
    $qry = "insert into " . $prefix . "_queue
	        (uid,uname,subject,story,storyext,timestamp,topic,alanguage) values
					(" . intval($uid) . ", '" . mxAddSlashesForSQL(strip_tags($name)) . "', '" . mxAddSlashesForSQL(strip_tags($title)) . "', '" . mxAddSlashesForSQL($hometext) . "', '" . mxAddSlashesForSQL($bodytext) . "', now(), " . intval($topic) . ", '" . mxAddSlashesForSQL(strip_tags($alanguage)) . "')";
    // print $qry;
    // mxDebugFuncVars($story);
    // exit;
    $result = sql_query($qry);
    if ($result) {
        if (!empty($GLOBALS['notify'])) {
            $qid = sql_insert_id();
            $story = mxStripSlashes($story);
            extract($story);
            $notify_message = "-- " . _NEWSARTICLES . " --\n" . $GLOBALS['notify_message'] . "\n\n\n========================================================\n" . PMX_HOME_URL . "/" . adminUrl("News", "DisplayStory", "qid=" . $qid) . "\n\n" . $title . "\n\n\n" . $hometext . "\n" . $bodytext . "\n\nfrom: " . $name;
            mxMail($GLOBALS['notify_email'], $GLOBALS['notify_subject'], $notify_message, $GLOBALS['notify_from']);
        }
        mxRedirect("modules.php?name=" . $GLOBALS['module_name'] . "&op=thanks");
    } else {
        $story['errmsg'] = _ERRNOSAVED;
        PreviewStory($story);
        exit;
    }
}

/**
 */
function thankspage()
{
    global $prefix;
    $waiting = 1;
    $result = sql_query("select count(qid) from " . $prefix . "_queue");
    list($waiting) = sql_fetch_row($result);
    include("header.php");
    title(_SUBSENT);
    OpenTable();
    echo '<div class="note">' . _SUBTEXT . "<br />" . _WEHAVESUB . " " . $waiting . " " . _WAITING . '<br /><br /><b>' . _THANKSSUB . '</b></div>';
    CloseTable();
    include('footer.php');
}

/**
 */
function vkpNewsForm($story = array())
{
    $story["topic"] = (empty($story["topic"])) ? vkpGetFirstTopic() : $story["topic"];
    $story["alanguage"] = (empty($story["alanguage"])) ? "" : $story["alanguage"];
    $story["title"] = (empty($story["title"])) ? "" : strip_tags($story["title"]);
    $story["hometext"] = (empty($story["hometext"])) ? "" : $story["hometext"];
    $story["bodytext"] = (empty($story["bodytext"])) ? "" : $story["bodytext"];
    $captcha_object = load_class('Captcha', 'newson');

    OpenTable();
    echo "<div class=\"content\"><form name=\"snews\" action=\"modules.php?name=" . $GLOBALS['module_name'] . "\" method=\"post\">";
    echo "<input type=\"hidden\" name=\"name\" value=\"" . $GLOBALS['module_name'] . "\" />\n";
    echo '<br /><br />';
    vkpSelectTopic($story["topic"]);
    echo '<br /><br />';
    addNewsTextFields($story);
    echo '<br /><br />';
    vkpNewsSelectLanguage($story["alanguage"]);
    echo '<br /><br />';
    if (isset($story["op"]) && $captcha_object->get_active()) {
        echo "<br /><br />" . $captcha_object->image() . "<br /><br />";
        echo "<b>" . $captcha_object->caption() . '</b><br />';
        echo $captcha_object->inputfield() . "<br /><br />";
    }
    echo "<input type=\"submit\" name=\"op\" value=\"" . _PREVIEW . "\" />";
    if (isset($story["op"])) {
        echo "&nbsp;&nbsp;<input type=\"submit\" name=\"op\" value=\"" . _OK . "\" />";
        if ($captcha_object->get_active()) {
            echo '&nbsp;&nbsp;' . $captcha_object->reloadbutton() . ' ';
        }
    }
    echo "</form></div>";
    CloseTable();
}

$op = (empty($_REQUEST['op'])) ? "" : $_REQUEST['op'];
switch ($op) {
    case _PREVIEW:
        PreviewStory($_POST);
        break;
    case _OK:
        SubmitStory($_POST);
        break;
    case "thanks":
        thankspage();
        break;
    default:
        defaultDisplay();
        break;
}

?>