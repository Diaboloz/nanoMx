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
// rechte Bloecke an oder aus
$index = 1;
$plugins = 1;

$module_name = basename(dirname(__FILE__));
mxGetLangfile($module_name);
include_once(PMX_SYSTEM_DIR . DS . 'mxNewsFunctions.php');

function theindex()
{
    global $prefix, $user_prefix, $pagetitle;

    if (isset($_GET['page'])) {
        $currentpage = intval($_GET['page']);
    } else if (isset($_GET['pagenum'])) {
        // Abwärtskompatibilität < 1.12
        $currentpage = intval($_GET['pagenum']);
    }
    if (empty($currentpage)) {
        $currentpage = 1;
    }

    if (isset($_GET['topic'])) {
        $topic = intval($_GET['topic']);
    } else if (isset($_GET['new_topic'])) {
        // Abwärtskompatibilität < 1.12
        $topic = intval($_GET['new_topic']);
    } else {
        $topic = 0;
    }

    $pagecols = (empty($GLOBALS["storyhome_cols"])) ? 1 : intval($GLOBALS["storyhome_cols"]);
    if ($GLOBALS["multilingual"]) {
        if (strpos($GLOBALS['currentlang'], 'german') === 0) $thislang = "german";
        else $thislang = $GLOBALS["currentlang"];
        $where[] = "(s.alanguage LIKE '" . $thislang . "%' OR s.alanguage='')";
    }
    $where[] = (!$topic) ? "(s.ihome=0 OR s.catid=0)" : "topic='" . $topic . "'";
    $where[] = "(s.time <= now())";
    $where = implode(' AND ', $where);
    $qry = "SELECT Count(sid) FROM ${prefix}_stories AS s WHERE " . $where;
    $result = sql_query($qry);
    list($allstories) = sql_fetch_row($result);
    if (MX_IS_USER) {
        $userinfo = mxGetUserData();
        $storynum = (empty($userinfo['storynum'])) ? $GLOBALS["storyhome"] : $userinfo['storynum'];
    } else {
        $storynum = $GLOBALS["storyhome"];
    }
    $storynum = (empty($storynum) || $storynum > 50 || $storynum < 1) ? 10 : intval($storynum);
    $storynum = ceil($storynum / $pagecols) * $pagecols;
    $colwidth = floor(100 / $pagecols);
    $offset = ($currentpage-1) * $storynum;
    $offset = ($offset < 0) ? 0 : $offset;
    $storiecount = 0;
    $topictext = '';

    $result = pmx_news_get_articles_resource($where, $offset, $storynum);

    if ($result) {
        while ($stories[$storiecount] = sql_fetch_assoc($result)) {
            if (empty($stories[$storiecount]['user_viewemail'])) {
                $stories[$storiecount]['email'] = '';
            }
            if ($topic && $storiecount == 0) {
                $topictext = $stories[$storiecount]["topictext"];
            }
            $storiecount++;
        }
    }

    if (empty($storiecount)) {
        $msg = '
	       ' . _NOINFO4TOPIC . '
		   <br />
           <a class="mx-button" href="modules.php?name=News">' . _GOTONEWSINDEX . '</a> <a class="mx-button" href="modules.php?name=Topics">' . _SELECTNEWTOPIC . '</a>';
        mxMessageScreen($msg);
        die();
    }

    $pagetitle = _GOTONEWSINDEX;
    if ($topic && $topictext) {
        $pagetitle .= ', ' . $topictext;
    }
    if ($currentpage > 1) {
        $pagetitle .= ', ' . _PAGE . ' ' . $currentpage;
    }

    $inhome = defined('MX_HOME_FILE');
    $navlink = ($topic) ? 'modules.php?name=News&amp;topic=' . $topic : 'modules.php?name=News';
    $navigation = pmx_news_navigation($navlink, $allstories, $storynum, $currentpage);

    include('header.php');

    switch (true) {
        case $topic && !$inhome:
            title(_ARTICLETOTOPIC . ': ' . $topictext);
            // if (!mxModuleAllowed('Search')) {
            // break;
            // }
            echo "<center>"
            // . "<form action=\"modules.php?name=Search\" method=\"get\">"
            // . "<input type=\"hidden\" name=\"name\" value=\"Search\" />"
            // . _SEARCHONTOPIC . ": <input type=\"text\" name=\"query\" size=\"30\" />&nbsp;&nbsp;"
            // . "<input type=\"submit\" value=\"" . _NEWS_SEARCH . "\" />"
            // . "<input type=\"hidden\" name=\"type\" value=\"stories\" />"
            // . "<input type=\"hidden\" name=\"topic\" value=\"$topic\" />"
            // . "</form>"
             . "<p>[&nbsp;<a href=\"modules.php?name=News\">" . _GOTONEWSINDEX . "</a> | <a href=\"modules.php?name=Topics\">" . _SELECTNEWTOPIC . "</a>&nbsp;]</p>"
             . "</center>";
            break;
        case !$inhome:
            title(_GOTONEWSINDEX);
    }

    if ($navigation && !$inhome) {
        echo '
			<div class="align-right">' . $navigation . '</div>';
    }

    $i = 0;
    if ($pagecols > 1) {
        echo '
			<table class="full blind" border="0" cellspacing="0" cellpadding="0">';
    }
    foreach($stories as $index => $story) {
        if (!is_array($story)) continue;
        $story = vkpGetStoryDetails($story);
        $i++;
        if ($pagecols > 1) {
            if ($i == 1) {
                echo "<tr valign=\"top\">";
            }
            echo '
				<td width="' . $colwidth . '%" class="story-column">';
            $story['column'] = $i;
        } else {
            $story['column'] = 1;
        }
        themeindex($story["aid"], $story["informant"], $story["datetime"], $story["title_formated"], $story["counter"], $story["topic"], $story["hometext"], $story["notes"], $story["morelink"], $story["topicname"], $story["topicimage"], $story["topictext"], $story);
        if ($pagecols > 1) {
            echo '
				</td>';
            if ($i == $pagecols) {
                echo '
				</tr>';
                $i = 0;
            }
        }
    }
    if ($pagecols > 1) {
        if ($i) {
            $colspan = $pagecols - $i;
            echo '
				<td colspan="' . $colspan . '" class="story-column-filled">&nbsp;</td>
			</tr>';
        }
        echo '
			</table>';
    }
    if ($navigation) {
        $pages = ceil($allstories / $storynum);
        echo '
			<br />';
        echo '
			<div class="align-right">' . $navigation . '</div>';;
        echo '
			<p class="tiny align-right">' . $allstories . ' ' . _NEWS_ARTICLE . " (" . $pages . ' ' . _PAGES . ", " . $storynum . ' ' . _NEWS_ARTICLEPERPAGE . ")</p>";
    }

    include('footer.php');
}

if (empty($op)) $op = '';

switch ($op) {
    case 'rate_article':
        if (empty($score)) $score = 0;
        if (empty($sid)) $sid = 0;
        rate_article((int)$sid, (int)$score);
        break;

    case 'rate_complete':
        if (empty($sid)) $sid = 0;
        if (empty($rated)) {
            mxRedirect("modules.php?name=News&amp;file=article&amp;sid=" . intval($sid), _THANKSVOTEARTICLE, 1);
        } elseif ($rated == 1) {
            mxRedirect("modules.php?name=News&amp;file=article&amp;sid=" . intval($sid), _ALREADYVOTEDARTICLE, 3);
        }
        break;

    default:
        theindex();
        break;
}

?>
