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

$module_name = basename(dirname(__FILE__));
include_once(PMX_MODULES_DIR . DS . "$module_name/config.php");
mxGetLangfile($module_name);
$pagetitle = _TOPICS_ACTIVES;
include 'header.php';
$useMxMiddlebox = (function_exists("thememiddlebox") && $useMxMiddlebox) ? 1 : 0;
$columnscount = (empty($columnscount)) ? 1 : $columnscount;
$tpath = trim($tipath, ' ;,:./\\') . '/';

if ($GLOBALS["multilingual"] == 1) {
    if (strpos($GLOBALS['currentlang'], 'german') === 0) $thislang = "german";
    else $thislang = $GLOBALS["currentlang"];
    $qrylang1 = "WHERE ((${prefix}_stories.alanguage LIKE '" . $thislang . "%' Or ${prefix}_stories.alanguage = '') AND ${prefix}_stories.time <= now())";
    $qrylang2 = "AND ((${prefix}_stories.alanguage LIKE '" . $thislang . "%' Or ${prefix}_stories.alanguage = ''))";
} else {
    $qrylang1 = "WHERE ${prefix}_stories.time <= now()";
    $qrylang2 = "";
}
$tid = (empty($_REQUEST['tid'])) ? 0 : (int)$_REQUEST['tid'];
if (empty($tid)) {
    $qry1 = "SELECT topicid, topicimage, topictext, Count(${prefix}_stories.sid) AS sidcount, Sum(${prefix}_stories.counter) AS allreads
	        FROM ${prefix}_topics
					LEFT JOIN ${prefix}_stories ON ${prefix}_topics.topicid = ${prefix}_stories.topic
	        $qrylang1
	        GROUP BY topicid, topicimage, topictext
	        HAVING Count(${prefix}_stories.sid) > 0
					ORDER BY topictext";
} else {
    $headlinecount = $headlinecount_topic;
    $columnscount = 1;
    $qry1 = "SELECT topicid, topicimage, topictext, Sum(${prefix}_stories.counter) AS allreads, Count(${prefix}_stories.sid) AS sidcount
	         FROM ${prefix}_topics LEFT JOIN ${prefix}_stories ON ${prefix}_topics.topicid = ${prefix}_stories.topic
	         WHERE (${prefix}_topics.topicid=" . $tid . " AND ${prefix}_stories.time <= now() " . $qrylang2 . ")
	         GROUP BY ${prefix}_topics.topicid, ${prefix}_topics.topicimage, ${prefix}_topics.topictext";
}
$i = 0;
$result1 = sql_query($qry1);
if (($result1)) {
    while ($topics = sql_fetch_array($result1)) {
        $topics["topictext"] = htmlspecialchars(strip_tags(stripslashes($topics["topictext"])));
        if ($topics["sidcount"] > 0) {
            $tid = $topics["topicid"];
            $qry2 = "SELECT sid, ${prefix}_stories.title, time, aid, ${prefix}_stories.catid, ${prefix}_stories_cat.title AS cat_title
			         FROM ${prefix}_stories LEFT JOIN ${prefix}_stories_cat ON ${prefix}_stories.catid = ${prefix}_stories_cat.catid
			         WHERE (`topic`='$tid' AND ${prefix}_stories.time <= now() $qrylang2)
			         ORDER BY ${prefix}_stories.time DESC, ${prefix}_stories.sid DESC
							 LIMIT 0," . $headlinecount . "";
            $result2 = sql_query($qry2);
            $topics["stories"] = "";
            if (($result2)) {
                while ($story = sql_fetch_array($result2)) {
                    $storydate = ($columnscount == 1) ? mx_strftime(_TOPICS_DATE_FORMAT, strtotime($story['time'])) . "&nbsp;&middot;&nbsp;" : "";
                    $storytitle = (empty($story["title"])) ? _SUBMISSIONS : htmlspecialchars(strip_tags(stripslashes($story["title"])));
                    $cattitle = (empty($story["cat_title"])) ? "" : " <span class=\"tiny\">(<a href=\"modules.php?name=News&amp;file=categories&amp;catid=" . $story["catid"] . "\"><i>" . htmlspecialchars(strip_tags(stripslashes($story["cat_title"]))) . "</i></a>)</span>";
                    $topics["stories"] .= "<li><span class=\"content\">" . $storydate . "<a href=\"modules.php?name=News&amp;file=article&amp;sid=" . $story["sid"] . "\">" . $storytitle . "</a> </span>" . $cattitle . "</li>\n";
                }
            } else {
                $topics["stories"] = _TOPICS_NOSTORIES . " (SQL-Error)";
            }
        } else {
            $topics["stories"] = _TOPICS_NOSTORIES;
            continue;
        }
        $topicimage = '';
        if ($showimage) {
            $topicimage = $tpath . $topics["topicimage"];
        }
        if ($headlinecount == 0) {
            $useMxMiddlebox = 0;
            $topicimage = (empty($topicimage)) ? '<br /><br /><br />' : mxCreateImage($topicimage, _TOPICS_CLICKTO, 0, 'hspace="5" vspace="5"');
            $out[$i] = '<div><a href="modules.php?name=News&amp;topic=' . $topics['topicid'] . '" title="' . _TOPICS_CLICKTO . '"><span class="title">' . $topics['topictext'] . '</span><br />';
            $out[$i] .= $topicimage . '</a></div><br />';
            $out[$i] .= '<span class="tiny">';
            $out[$i] .= _TOPICS_ALLNEWS . ':&nbsp;<b>' . $topics['sidcount'] . '</b>&nbsp; ';
            $out[$i] .= _TOPICS_ALLREADS . ':&nbsp;<b>' . $topics['allreads'] . '</b>';
            $out[$i] .= '</span>';
        } else if ($topicimageRight || !$showimage) {
            $topicimage = (empty($topicimage)) ? '' : mxCreateImage($topicimage, (($topics['sidcount'] > 1) ? _TOPICS_CLICKTO : ''), 0, 'align="right" hspace="5" vspace="5"');
            $out[$i] = '<table width="100%" border="0" cellspacing="0" cellpadding="2">';
            if (!$useMxMiddlebox) $out[$i] .= '<tr><td colspan="3"><font class="title">' . $topics['topictext'] . '</font></td></tr>';
            $out[$i] .= '<tr valign="top">';
            $out[$i] .= '<td width="90%"><font class="tiny">';
            $out[$i] .= _TOPICS_ALLNEWS . ':&nbsp;<b>' . $topics['sidcount'] . '</b>&nbsp;&nbsp;';
            $out[$i] .= _TOPICS_ALLREADS . ':&nbsp;<b>' . $topics['allreads'] . '</b>';
            $out[$i] .= '</font></td>';
            $out[$i] .= '<td width="10%" align="right" nowrap="nowrap">' . (($topics['sidcount'] > 1) ? '<a href="modules.php?name=News&amp;topic=' . $topics['topicid'] . '" title="' . _TOPICS_CLICKTO . '">' . _TOPICS_READMORE . '</a>' : '') . '</td>';
            $out[$i] .= '</tr>';
            $out[$i] .= '<tr valign="top">';
            $out[$i] .= '<td colspan="2">' . (($topics['sidcount'] > 1) ? '<a href="modules.php?name=News&amp;topic=' . $topics['topicid'] . '">' . $topicimage . '</a>' : $topicimage) . '<ul ' . $liststyle . '>' . $topics['stories'] . '</ul></td>';
            $out[$i] .= '</tr>';
            $out[$i] .= '</table>';
        } else {
            $topicimage = (empty($topicimage)) ? '<br /><br />' : mxCreateImage($topicimage, (($topics['sidcount'] > 1) ? _TOPICS_CLICKTO : ''), 0, 'hspace="5" vspace="5"');
            $out[$i] = '<table width="100%" border="0" cellspacing="0" cellpadding="2">';
            if (!$useMxMiddlebox) $out[$i] .= '<tr><td colspan="3"><span class="title">' . $topics['topictext'] . '</span></td></tr>';
            $out[$i] .= '<tr><td colspan="2" width="90%">';
            $out[$i] .= '<span class="tiny">';
            $out[$i] .= _TOPICS_ALLNEWS . ':&nbsp;<b>' . $topics['sidcount'] . '</b>&nbsp; ';
            $out[$i] .= _TOPICS_ALLREADS . ':&nbsp;<b>' . $topics['allreads'] . '</b>';
            $out[$i] .= '</span></td>';
            $out[$i] .= '<td width="10%" align="right" nowrap="nowrap">' . (($topics['sidcount'] > 1) ? '<a href="modules.php?name=News&amp;topic=' . $topics['topicid'] . '" title="' . _TOPICS_CLICKTO . '">' . _TOPICS_READMORE . '</a>' : '') . '</td>';
            $out[$i] .= '</tr>';
            $out[$i] .= '<tr valign="top">';
            $out[$i] .= '<td width="10%">' . (($topics['sidcount'] > 1) ? '<a href="modules.php?name=News&amp;topic=' . $topics['topicid'] . '">' . $topicimage . '</a>' : $topicimage) . '</td>';
            $out[$i] .= '<td width="90%" colspan="2"><ul ' . $liststyle . '>' . $topics['stories'] . '</ul></td>';
            $out[$i] .= '</tr>';
            $out[$i] .= '</table>';
        }
        $thetitle[$i] = $topics["topictext"];
        $i++;
    }
} else {
    $topicerr = 1;
}

if ($showcaption) {
    title(_TOPICS_ACTIVES);
}

if (empty($out) || isset($topicerr)) {
    $var = (isset($topicerr)) ? " (SQL-Error)" : "";
    echo '
        <div class="alert alert-info">
            ' . _TOPICS_NOAVAILABLE . $var . '
        </div>';
} else {
    $tdwidth = (int)(100 / $columnscount);
    echo "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n<tr valign=\"top\">\n";
    $tdcount = 0;
    for($i = 0; $i < count($out); $i++) {
        $tdcount++;
        echo "<td width=\"" . $tdwidth . "%\">\n";
        if ($useMxMiddlebox) {
            $block['title'] = $thetitle[$i];
            $block['content'] = $out[$i];
            $block['position'] = "c";
            $block['weight'] = $i;
            thememiddlebox ($block['title'], $block['content'], $block);
        } else {
            echo $out[$i];
        }
        echo "</td>";
        if (!(($i + 1) % $columnscount)) {
            echo "</tr>\n<tr valign=\"top\">";
            $tdcount = 0;
        }
    }
    $colspan = $columnscount - $tdcount;
    if ($colspan && $colspan < $columnscount) {
        echo "<td colspan=\"$colspan\">&nbsp;</td>\n";
    } else {
        echo "<td>&nbsp;</td>\n";
    }
    echo "</tr></table>\n";
}

if (mxModuleAllowed('Search') && mxModuleAllowed('News')) {
    echo "<br />\n";
    OpenTable();
    echo "<div align=\"center\">"
     . "<form action=\"modules.php?name=Search\" method=\"get\">"
     . "<span class=\"content\">" . _TOPICS_SEARCH . "&nbsp;&nbsp;"
     . "<input type=\"hidden\" name=\"name\" value=\"Search\" />"
     . "<input type=\"hidden\" name=\"m\" value=\"News\" />"
     . "<input type=\"text\" name=\"q\" size=\"30\" />&nbsp;&nbsp;"
     . "<input type=\"submit\" value=\"" . _SEARCH . "\" />"
     . "</span></form></div>\n";
    CloseTable();
}

include 'footer.php';

?>