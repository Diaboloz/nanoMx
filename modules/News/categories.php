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

if (isset($_GET['catid'])) {
    $catid = intval($_GET['catid']);
}
if (empty($catid)) {
    include(dirname(__FILE__) . '/index.php');
    exit;
}
// rechte Bloecke an oder aus
$index = 1;

$module_name = basename(dirname(__FILE__));
mxGetLangfile($module_name);
include_once(PMX_SYSTEM_DIR . DS . 'mxNewsFunctions.php');

function newscatindex($catid)
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

    $catid = intval($catid);
    sql_query("UPDATE ${prefix}_stories_cat set counter=counter+1 where catid=" . intval($catid));

    $pagecols = (empty($GLOBALS["storyhome_cols"])) ? 1 : intval($GLOBALS["storyhome_cols"]);
    if ($GLOBALS["multilingual"] == 1) {
        if (strpos($GLOBALS['currentlang'], 'german') === 0) $thislang = "german";
        else $thislang = $GLOBALS["currentlang"];
        $where[] = "(s.alanguage LIKE '" . $thislang . "%' OR s.alanguage='')";
    }
    $where[] = "(s.catid = " . $catid . ") ";
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
    $cattext = '';

    $result = pmx_news_get_articles_resource($where, $offset, $storynum);

    if ($result) {
        while ($stories[$storiecount] = sql_fetch_assoc($result)) {
            if (empty($stories[$storiecount]['user_viewemail'])) {
                $stories[$storiecount]['email'] = '';
            }
            if ($storiecount == 0) {
                $cattext = _ARTICLETOCATEGORY . ': ' . $stories[$storiecount]['cattitle'];
            }
            $storiecount++;
        }
    }

    if (empty($storiecount)) {
        $msg = '
			<br />
			<br />
			<b>' . _NOINFO4TOPIC . '</b>
			<br />
			<br />[&nbsp;<a href="modules.php?name=News">' . _GOTONEWSINDEX . '</a>&nbsp;]';
        mxMessageScreen($msg);
        die();
    }

    $pagetitle = $cattext;
    if ($currentpage > 1) {
        $pagetitle .= ', ' . _PAGE . ' ' . $currentpage;
    }

    $navlink = 'modules.php?name=News&amp;file=categories&amp;catid=' . $catid;
    $navigation = pmx_news_navigation($navlink, $allstories, $storynum, $currentpage);

    include('header.php');

    title($cattext);
    if ($navigation) {
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
        $story["title_formated"] = $story["title"]; /// Titel wieder zuruecksetzen ohne Kategorie
        $i++;
        if ($pagecols > 1) {
            if ($i == 1) {
                echo '
					<tr valign="top">';
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
			<br />
			<div class="align-right">' . $navigation . '</div>
			<p class="tiny align-right">' . $allstories . ' ' . _NEWS_ARTICLE . ' (' . $pages . ' ' . _PAGES . ', ' . $storynum . ' ' . _NEWS_ARTICLEPERPAGE . ')</p>';
    }
    include('footer.php');
}

newscatindex($catid);

?>