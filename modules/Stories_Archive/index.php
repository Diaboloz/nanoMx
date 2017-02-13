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

include(dirname(__FILE__) . '/settings.php');
$module_name = basename(dirname(__FILE__));
mxGetLangfile($module_name);

include_once(PMX_SYSTEM_DIR . DS . 'mxNewsFunctions.php');

// Link initialisieren
$link['mod'] = 'name=' . $module_name;
// check ob gueltige Jahresangabe
$setyear = false;
if (isset($_GET['year']) && $_GET['year'] > 1970 && $_GET['year'] <= date('Y')) {
    $year = intval($_GET['year']);
    $setyear = true;
} else {
    $year = date('Y');
}

// check ob gueltige Monatsangabe
$setmonth = false;
if ($setyear && isset($_GET['month']) && $_GET['month'] >= 1 && $_GET['month'] <= 12) {
    $month = intval($_GET['month']);
    $setmonth = true;
} else {
    $month = date('n');
}

/* aktuelle Seite */
if (isset($_GET['page'])) {
    $currentpage = intval($_GET['page']);
}
if (empty($currentpage)) {
    $currentpage = 1;
}

/* Anzahl in der Artikel-liste */
if (isset($_GET['show'])) {
    $show = intval($_GET['show']);
    // check, damit $show realistischen Wert hat
    if (empty($show) || intval($show) <= 5) {
        $show = $staconf['showarticles'];
    } else if ($show > $staconf['maxarticles']) {
        // auf maximale Anzahl begrenzen
        $show = $staconf['maxarticles'];
    }
    if ($show != $staconf['showarticles']) {
        // Bei Bedarf, dann den Link erweitern, mit der gewuenschten Anzahl
        $link['show'] = 'show=' . $show;
    }
} else if ((isset($_GET['sa']) && $_GET['sa'] == 'show_all') || isset($_GET['show_all'])) {
    $show = $staconf['maxarticles'];
    $link['all'] = 'show_all=yes';
} else {
    $show = $staconf['showarticles'];
}

/* Startwert in der Artikel-liste */
$offset = ($currentpage - 1) * $show;

if ($setyear && !$setmonth) {
    // nur bestimmtes Jahr
    $title = _STORIESARCHIVE . ', ' . $year;
    $link['year'] = 'year=' . $year;
    $allstories = sta_get_articlescount($year);
    $story_list = sta_story_list($offset, $show, $year);
} elseif ($setyear && $setmonth) {
    // bestimmter Monat (show_month)
    $title = _STORIESARCHIVE . ', ' . mx_strftime('%B, %Y', mktime (0, 0, 0, $month, 15, $year));
    $link['year'] = 'year=' . $year;
    $link['month'] = 'month=' . $month;
    $allstories = sta_get_articlescount($year, $month);
    $story_list = sta_story_list($offset, $show, $year, $month);
} else {
    // alle (show_all)
    $title = _STORIESARCHIVE;
    // $link = 'modules.php?name=' . $module_name . $link;
    $allstories = sta_get_articlescount();
    $story_list = sta_story_list($offset, $show);
}

$pagetitle = $title;
$navlink = 'modules.php?' . implode('&amp;', $link);
#$month_list = sta_month_list($link, $year, $month);

/* Template initialisieren */
$template = load_class('Template');
$template->init_path(__FILE__);
/* Daten dem Template zuweisen */
$template->assign(compact('module_name', 'title', 'story_list'));
$template->assign('month_list', sta_month_list($link, $year, $month));
$template->assign('pageview', pmx_news_navigation($navlink, $allstories, $show, $currentpage));
$template->assign('is_home', (count($_GET) == 1));

include('header.php');
/* Template ausgeben */
$template->display('basic.htm');
include('footer.php');

return;

function sta_month_list($link, $year = 0, $month = 0)
{
    global $module_name, $prefix, $staconf;
    $result = sql_query("SELECT DISTINCT YEAR(`time`) AS y, MONTH(`time`) AS m, COUNT(`sid`) as c
            FROM ${prefix}_stories
            WHERE `time` <= now()
            GROUP BY y, m
            ORDER BY y DESC");
    $rows = array();
    while ($row = sql_fetch_assoc($result)) {
        $rows[$row['y']][$row['m']] = $row['c'];
    }
    $newlink = $link;
    unset($newlink['month'], $newlink['year']);
    $newlink = 'modules.php?' . implode('&amp;', $newlink);
    $out = array();
    $curyear = intval(date('Y'));
    $curmonth = intval(date('n'));
    foreach($rows as $year => $x) {
        $out[$year]['count'] = 0;
        $out[$year]['link'] = $newlink . '&amp;year=' . $year;
        $limit = (($year < $curyear) || $staconf['allmonths']) ? 12 : $curmonth;
        for ($month = $limit; $month >= 1; $month--) {
            $out[$year]['months'][$month]['name'] = mx_strftime('%B', mktime (0, 0, 0, $month, 15, $year));
            $out[$year]['months'][$month]['link'] = $newlink . '&amp;year=' . $year . '&amp;month=' . $month;
            if (isset($rows[$year][$month])) {
                $out[$year]['months'][$month]['count'] = $rows[$year][$month];
            } else {
                $out[$year]['months'][$month]['count'] = 0;
            }
            $out[$year]['count'] += $out[$year]['months'][$month]['count'];
        }
        krsort($out[$year]['months']);
    }
    return $out;
}

/**
 * Anzahl ALLER Artikel in dieser Kategorie
 */
function sta_get_articlescount($year = 0, $month = 0)
{
    global $module_name, $prefix;

    $datequery = '';
    if ($year) {
        $datequery .= ' AND YEAR(s.`time`)=' . intval($year);
    }
    if ($month) {
        $datequery .= ' AND MONTH(s.`time`)=' . intval($month);
    }

    $qry = "SELECT COUNT(sid) FROM ${prefix}_stories AS s WHERE s.`time` <= now() " . $datequery;
    $result = sql_query($qry);
    list($allstories) = sql_fetch_row($result);
    return $allstories;
}

function sta_story_list($offset, $show, $year = 0, $month = 0)
{
    global $module_name, $prefix, $staconf;

    $recommend_allowed = mxModuleAllowed('Recommend_Us');

    $datequery = '';
    if ($year) {
        $datequery .= ' AND YEAR(s.`time`)=' . intval($year);
    }
    if ($month) {
        $datequery .= ' AND MONTH(s.`time`)=' . intval($month);
    }

    $qry = "SELECT s.sid, s.title, s.hometext, UNIX_TIMESTAMP(s.`time`) AS tstamp, s.comments, s.counter, s.topic, s.alanguage, s.score, s.ratings, sc.title AS cat_title, sc.catid
            FROM ${prefix}_stories AS s LEFT JOIN ${prefix}_stories_cat AS sc
            ON s.catid = sc.catid
            WHERE s.`time` <= now() " . $datequery . "
            ORDER BY s.`time` DESC, s.`sid` DESC
            LIMIT " . intval($offset) . "," . intval($show);
    // mxDebugFuncVars($qry);
    $result = sql_query($qry);
    $rows = array();
    while ($row = sql_fetch_assoc($result)) {
        $row['title'] = '<a href="modules.php?name=News&amp;file=article&amp;sid=' . $row['sid'] . '">' . htmlspecialchars($row['title']) . '</a>';
        if (empty($row['catid'])) {
            $row['cat_title'] = '';
        } else {
            $row['cat_title'] = '<a href="modules.php?name=News&amp;file=categories&amp;catid=' . $row['catid'] . '">' . htmlspecialchars($row['cat_title']) . '</a>';
        }
        if ($GLOBALS['multilingual'] && !empty($row['alanguage'])) {
            $row['lang_img'] = '<img src="images/language/flag-' . $row['alanguage'] . '.png" border="0" hspace="2" alt="' . ucfirst($row['alanguage']) . '" width="15" height="8" />';
        } else {
            $row['lang_img'] = '';
        }
        $row['actions'] = '<a href="modules.php?name=News&amp;file=print&amp;sid=' . $row['sid'] . '" rel="nofollow">' . mxCreateImage('images/print.gif', _PRINTER) . '</a>';
        if ($recommend_allowed) {
            $row['actions'] .= '&nbsp;<a href="modules.php?name=News&amp;file=friend&amp;sid=' . $row['sid'] . '" rel="nofollow">' . mxCreateImage('images/friend.gif', _FRIEND) . '</a>';
        }
        if ($row['score'] != 0) {
            $row['rated'] = substr($row['score'] / $row['ratings'], 0, 4);
        } else {
            $row['rated'] = 0;
        }

        $row['hometext'] = preg_replace('#<(p|br)[^>]*>#i', ' ', $row['hometext']);
        $row['hometext'] = str_replace('&nbsp;', ' ', $row['hometext']);
        if ($staconf['showpics']) {
            $textlen = $staconf['textlen'];
            $row['hometext'] = strip_tags($row['hometext'], '<img>');
            $replaces = array();
            if (preg_match_all('#<img[^>]*>#si', $row['hometext'], $images)) {
                foreach($images[0] as $i => $img) {
                    $alternate = md5($img);
                    $textlen = $textlen + strlen($alternate);
                    $replaces[$alternate] = $img;
                    $row['hometext'] = str_replace($img, $alternate, $row['hometext']);
                }
            }
            $row['hometext'] = mxCutString(trim($row['hometext']), $textlen, "...", " ");
            if (count($replaces)) {
                $row['hometext'] = str_replace(array_keys($replaces), array_values($replaces), $row['hometext']);
            }
        } else {
            $row['hometext'] = strip_tags($row['hometext']);
            $row['hometext'] = mxCutString(trim($row['hometext']), $staconf['textlen'], "...", " ");
        }
        $rows[] = $row;
    }

    return $rows;
}

?>