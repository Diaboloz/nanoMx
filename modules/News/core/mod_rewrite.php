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
// TODO: Variablen besser benennen
// /// $key kann in url vorkommen
// /// $link, sollte $link, oder so heissen
/*

# Artikel  ( Achtung, muss vor eigentlichen News stehen!! )

RewriteRule ^News-Category-.*-([0-9]+)(-.*)?\.html$ mod.php?name=News&file=categories&catid=$1&_MORE_=$2 [L]
RewriteRule ^News-.*item-([0-9]+)(-.*)?\.html$ mod.php?name=News&file=article&sid=$1&_MORE_=$2 [L]

 */

$hook = function($name, $parameter, &$replaces)
{
    if (!isset($parameter[$name])) {
        return;
    }
    global $prefix;

    $articles = array();
    $categories = array();
    $topics = array();

    foreach ($parameter[$name] as $key => $para) {
        switch (true) {
            case ((isset($para['file']) && $para['file'] == 'article') && (isset($para['sid']) && intval($para['sid']))):
                $articles[0][] = $para['sid'];
                $articles[$key] = $para;
                break;
            case ((isset($para['file']) && $para['file'] == 'categories') && (isset($para['catid']) && intval($para['catid']))):
                $categories[0][] = $para['catid'];
                $categories[$key] = $para;
                break;
            case (isset($para['topic']) && intval($para['topic'])):
                $topics[0][] = $para['topic'];
                $topics[$key] = $para;
                break;
            case (isset($para['new_topic']) && intval($para['new_topic'])):
                // alze Schreibweise, aus new_topic wird topic ;-)
                $para['topic'] = $para['new_topic'];
                unset($para['new_topic']);
                $topics[0][] = $para['topic'];
                $topics[$key] = $para;
                break;
        }
    }

    /**
     * # News-NEWSTITEL-(ID).html
     * RewriteRule ^News-.*-\(([0-9]+)\)(-.*)?\.html$ mod.php?News&________________________file-article-sid-$1$2 [L]
     */
    if ($articles) {
        $inquery = implode(',', array_unique(array_shift($articles)));
        $qry = "SELECT sid, title FROM {$prefix}_stories WHERE sid in ($inquery)";
        $result = sql_query($qry);
        $titles = array();
        while (list($id, $title) = sql_fetch_row($result)) {
            /* den aus der DB ausgelesenen Titel als URL tauglich machen */
            $titles[$id] = pmxModrewrite::title_entities($title, '-');
        }

        foreach ($articles as $key => $link) {
            if (isset($titles[$link['sid']])) {
                /* schreibt alle Parameter um in das normale pragmaMx mod_rewrite Format */
                $new_url = pmxModrewrite::title_parameters($link, 'sid', 'file');
                /* den neuen URL zusammensetzen */
                $new_url = 'News-' . $titles[$link['sid']] . '-item-' . $link['sid'] . '' . $new_url;
                /* die globalen Ersetzungsparameter mit den neuen Werten überschreiben */
                $replaces[$key] = $link['prefix'] . $new_url . $link['suffix'];
            }
        }
    }

    /**
     * # News-Category-CategoryTITEL-(ID).html
     * RewriteRule ^News-Category-.*-\(([0-9]+)\)(-.*)?\.html$ mod.php?News&________________________file-categories-catid-$1$2 [L]
     */
    if ($categories) {
        $inquery = implode(',', array_unique(array_shift($categories)));
        $qry = "SELECT catid, title FROM {$prefix}_stories_cat WHERE catid in ($inquery)";
        $result = sql_query($qry);
        $titles = array();
        while (list($id, $title) = sql_fetch_row($result)) {
            /* den aus der DB ausgelesenen Titel als URL tauglich machen */
            $titles[$id] = pmxModrewrite::title_entities($title, '-');
        }

        foreach ($categories as $key => $link) {
            if (isset($titles[$link['catid']])) {
                /* schreibt alle Parameter um in das normale pragmaMx mod_rewrite Format */
                $new_url = pmxModrewrite::title_parameters($link, 'catid', 'file');
                /* den neuen URL zusammensetzen */
                $new_url = 'News-category-' . $titles[$link['catid']] . '-' . $link['catid'] . '' . $new_url;
                /* die globalen Ersetzungsparameter mit den neuen Werten überschreiben */
                $replaces[$key] = $link['prefix'] . $new_url . $link['suffix'];
            }
        }
    }

    /**
     * # News-Topic-TopicTITEL-(ID).html
     * RewriteRule ^News-Topic-.*-\(([0-9]+)\)(-.*)?\.html$ mod.php?News&________________________topic-$1$2 [L]
     */
    if ($topics) {
        $inquery = implode(',', array_unique(array_shift($topics)));
        $qry = "SELECT topicid, topictext FROM {$prefix}_topics WHERE topicid in ($inquery)";
        $result = sql_query($qry);
        $titles = array();
        while (list($id, $title) = sql_fetch_row($result)) {
            /* den aus der DB ausgelesenen Titel als URL tauglich machen */
            $titles[$id] = pmxModrewrite::title_entities($title, '-');
        }

        foreach ($topics as $key => $link) {
            if (isset($titles[$link['topic']])) {
                /* schreibt alle Parameter um in das normale pragmaMx mod_rewrite Format */
                $new_url = pmxModrewrite::title_parameters($link, 'topic');
                /* den neuen URL zusammensetzen */
                $new_url = 'News-topic-' . $titles[$link['topic']] . '-' . $link['topic'] . '' . $new_url;
                /* die globalen Ersetzungsparameter mit den neuen Werten überschreiben */
                $replaces[$key] = $link['prefix'] . $new_url . $link['suffix'];
            }
        }
    }
}

?>