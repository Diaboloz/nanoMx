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

$hook = function($module_name, $options, &$items)
{
    /* $options enthält nur den Schlüssel $top */
    /* $items ergänzt die bestehende Liste */

    global $prefix, $user_prefix;

    /* die Variable $top extrahieren */
    extract($options, EXTR_SKIP);

    $i_num = 0;

    /**
     * Top 10 read stories
     */
    $result = sql_query("select sid, title, counter from ${prefix}_stories WHERE  time  <= now() " . pmx_multilang_query('alanguage', 'AND') . " AND counter>0 ORDER BY counter DESC LIMIT " . intval($top));
    $rows = sql_num_rows($result);
    if ($rows > 1) {
        $i_num++;
        $items[$module_name . $i_num]['caption'] = $rows . ' ' . _READSTORIES;
        while (list($sid, $title, $counter) = sql_fetch_row($result)) {
            if ($counter > 0) {
                $items[$module_name . $i_num]['list'][] = '<a href="modules.php?name=' . $module_name . '&amp;file=article&amp;sid=' . $sid . '">' . $title . '</a> - (' . $counter . ' ' . _READS . ')';
            }
        }
    }

    /**
     * Top 10 most voted stories
     */
    $result = sql_query("select sid, title, ratings from ${prefix}_stories WHERE score>0 AND ratings>0 " . pmx_multilang_query('alanguage', 'AND') . " ORDER BY ratings DESC LIMIT " . intval($top));
    $rows = sql_num_rows($result);
    if ($rows > 1) {
        $i_num++;
        $items[$module_name . $i_num]['caption'] = $rows . ' ' . _MOSTVOTEDSTORIES;
        while (list($sid, $title, $ratings) = sql_fetch_row($result)) {
            $items[$module_name . $i_num]['list'][] = '<a href="modules.php?name=' . $module_name . '&amp;file=article&amp;sid=' . $sid . '">' . $title . '</a> - (' . $ratings . ' ' . _STORYVOTES . ')';
        }
    }

    /**
     * Top 10 best rated stories
     */
    $result = sql_query("SELECT  sid ,  title , ROUND(score/ratings,2) as rate FROM  ${prefix}_stories  WHERE score>0 AND ratings>=5 " . pmx_multilang_query('alanguage', 'AND') . " ORDER BY rate DESC, ratings DESC LIMIT " . intval($top));
    $rows = sql_num_rows($result);
    if ($rows > 1) {
        $i_num++;
        $items[$module_name . $i_num]['caption'] = $rows . ' ' . _BESTRATEDSTORIES;
        while (list($sid, $title, $rate) = sql_fetch_row($result)) {
            $items[$module_name . $i_num]['list'][] = '<a href="modules.php?name=' . $module_name . '&amp;file=article&amp;sid=' . $sid . '">' . $title . '</a> - (' . $rate . ' ' . _POINTS . ')';
        }
    }

    /**
     * Top 10 commented stories
     */
    $result = sql_query("select sid, title, comments from ${prefix}_stories WHERE  time  <= now() " . pmx_multilang_query('alanguage', 'AND') . " AND comments>0 ORDER BY comments DESC LIMIT " . intval($top));
    $rows = sql_num_rows($result);
    if ($rows > 1) {
        $i_num++;
        $items[$module_name . $i_num]['caption'] = $rows . ' ' . _COMMENTEDSTORIES;
        while (list($sid, $title, $comments) = sql_fetch_row($result)) {
            $items[$module_name . $i_num]['list'][] = '<a href="modules.php?name=' . $module_name . '&amp;file=article&amp;sid=' . $sid . '">' . $title . '</a> - (' . $comments . ' ' . _COMMENTS . ')';
        }
    }

    /**
     * Top 10 categories
     */
    $result = sql_query("select catid, title, counter from ${prefix}_stories_cat WHERE counter>0 ORDER BY counter DESC LIMIT " . intval($top));
    $rows = sql_num_rows($result);
    if ($rows > 1) {
        $i_num++;
        $items[$module_name . $i_num]['caption'] = $rows . ' ' . _ACTIVECAT;
        while (list($catid, $title, $counter) = sql_fetch_row($result)) {
            $items[$module_name . $i_num]['list'][] = '<a href="modules.php?name=' . $module_name . '&amp;file=categories&amp;catid=' . $catid . '">' . $title . '</a> - (' . $counter . ' ' . _HITS . ')';
        }
    }

    /**
     * Top 10 authors
     */
    $result = sql_query("select aid, counter from " . $prefix . "_authors WHERE counter>0 ORDER BY counter DESC LIMIT " . intval($top));
    $rows = sql_num_rows($result);
    if ($rows > 1) {
        $i_num++;
        $items[$module_name . $i_num]['caption'] = $rows . ' ' . _MOSTACTIVEAUTHORS;
        while (list($aid, $counter) = sql_fetch_row($result)) {
            $items[$module_name . $i_num]['list'][] = "$aid - ($counter " . _NEWSPUBLISHED . ')';
        }
    }
} ;

?>